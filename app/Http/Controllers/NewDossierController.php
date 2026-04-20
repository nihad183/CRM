<?php

namespace App\Http\Controllers;

use App\Models\FichePropose;
use App\Models\FicheProposeResume;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewDossierController extends Controller
{
    public function create()
    {
        return view('pages.new-dossier');
    }

    public function indexFichePropose(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $fiches = FichePropose::query()
            ->where('user_id', $request->user()->id)
            ->where(function ($query) {
                $query->where('is_fiche_client', false)
                    ->orWhereNull('is_fiche_client');
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nom_entreprise', 'like', '%' . $search . '%');
            })
            ->latest()
            ->get();

        return view('pages.fiche-propose.index', [
            'fiches' => $fiches,
            'search' => $search,
        ]);
    }

    public function indexFicheClient(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $fiches = FichePropose::query()
            ->where('user_id', $request->user()->id)
            ->where('is_fiche_client', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nom_entreprise', 'like', '%' . $search . '%');
            })
            ->latest()
            ->get();

        return view('pages.fiche-client.index', [
            'fiches' => $fiches,
            'search' => $search,
        ]);
    }

    public function showFichePropose(Request $request, FichePropose $fichePropose)
    {
        abort_unless($fichePropose->user_id === $request->user()->id, 403);

        $fichePropose->load(['contacts', 'resumes', 'user']);

        return view('pages.fiche-propose.show', [
            'fiche' => $fichePropose,
        ]);
    }

    public function showFicheHistory(Request $request, FichePropose $fichePropose)
    {
        abort_unless($fichePropose->user_id === $request->user()->id, 403);

        $fichePropose->load(['user', 'resumes.user']);

        $events = collect([
            [
                'title' => $fichePropose->is_fiche_client ? 'Creation fiche client' : 'Creation Prospect',
                'user_name' => $fichePropose->user?->name ?: 'Utilisateur inconnu',
                'description' => 'Le dossier a ete cree pour ' . $fichePropose->nom_entreprise . '.',
                'created_at' => $fichePropose->created_at,
            ],
        ]);

        if ($fichePropose->converted_to_client_at && $fichePropose->created_at?->ne($fichePropose->converted_to_client_at)) {
            $events->push([
                'title' => 'Transformation en fiche client',
                'user_name' => $fichePropose->user?->name ?: 'Utilisateur inconnu',
                'description' => 'Le dossier a ete transforme de Prospect vers fiche client.',
                'created_at' => $fichePropose->converted_to_client_at,
            ]);
        }

        $events = $events->merge(
            $fichePropose->resumes->map(function ($resume) {
                return [
                    'title' => 'Ajout de resume',
                    'user_name' => $resume->user?->name ?: 'Utilisateur inconnu',
                    'description' => $resume->titre ?: 'Resume sans titre',
                    'created_at' => $resume->created_at,
                ];
            })
        )->sortBy('created_at')->values();

        return view('pages.fiche-propose.history', [
            'fiche' => $fichePropose,
            'events' => $events,
        ]);
    }

    public function createFicheProposeResume(Request $request, FichePropose $fichePropose)
    {
        abort_unless($fichePropose->user_id === $request->user()->id, 403);

        return view('pages.fiche-propose.resume-create', [
            'fiche' => $fichePropose,
        ]);
    }

    public function createFicheClientDocument(Request $request, FichePropose $fichePropose)
    {
        abort_unless($fichePropose->user_id === $request->user()->id, 403);

        return view('pages.fiche-client.document-create', [
            'fiche' => $fichePropose,
        ]);
    }

    public function editFicheClientDocuments(Request $request, FichePropose $fichePropose)
    {
        abort_unless($fichePropose->user_id === $request->user()->id, 403);
        abort_unless($fichePropose->is_fiche_client, 404);

        return view('pages.fiche-client.documents', [
            'fiche' => $fichePropose,
        ]);
    }

    public function storeFicheClientDocument(Request $request, FichePropose $fichePropose)
    {
        abort_unless($fichePropose->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'piece_jointe' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ]);

        if ($fichePropose->piece_jointe_path) {
            Storage::disk('public')->delete($fichePropose->piece_jointe_path);
        }

        $file = $validated['piece_jointe'];
        $path = $file->store('fiche-client-documents', 'public');

        $fichePropose->update([
            'is_fiche_client' => true,
            'converted_to_client_at' => now(),
            'piece_jointe_path' => $path,
            'piece_jointe_original_name' => $file->getClientOriginalName(),
        ]);

        return redirect()
            ->route('fiche-client')
            ->with('status', 'La piece jointe a ete enregistree avec succes et le dossier a ete transforme en fiche client.');
    }

    public function updateFicheClientDocuments(Request $request, FichePropose $fichePropose)
    {
        abort_unless($fichePropose->user_id === $request->user()->id, 403);
        abort_unless($fichePropose->is_fiche_client, 404);

        $validated = $request->validate([
            'n_rc' => ['required', 'string', 'max:255'],
            'n_rc_piece' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
            'nif' => ['required', 'string', 'max:255'],
            'nif_piece' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
            'nis' => ['required', 'string', 'max:255'],
            'nis_piece' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ]);

        $uploads = [
            'n_rc_piece' => ['path' => 'n_rc_piece_path', 'name' => 'n_rc_piece_original_name', 'dir' => 'fiche-client-documents/n-rc'],
            'nif_piece' => ['path' => 'nif_piece_path', 'name' => 'nif_piece_original_name', 'dir' => 'fiche-client-documents/nif'],
            'nis_piece' => ['path' => 'nis_piece_path', 'name' => 'nis_piece_original_name', 'dir' => 'fiche-client-documents/nis'],
        ];

        $payload = [
            'n_rc' => $validated['n_rc'],
            'nif' => $validated['nif'],
            'nis' => $validated['nis'],
        ];

        foreach ($uploads as $input => $columns) {
            if ($fichePropose->{$columns['path']}) {
                Storage::disk('public')->delete($fichePropose->{$columns['path']});
            }

            $file = $validated[$input];
            $payload[$columns['path']] = $file->store($columns['dir'], 'public');
            $payload[$columns['name']] = $file->getClientOriginalName();
        }

        $fichePropose->update($payload);

        return redirect()
            ->route('fiche-client')
            ->with('status', 'Les informations N RC, NIF et NIS ont ete enregistrees avec succes.');
    }

    public function printFicheProposeResume(Request $request, FichePropose $fichePropose, FicheProposeResume $resume)
    {
        abort_unless($fichePropose->user_id === $request->user()->id, 403);
        abort_unless((int) $resume->fiche_propose_id === (int) $fichePropose->id, 404);

        $fichePropose->load(['contacts', 'user']);
        $resume->load('user');

        return view('pages.fiche-propose.resume-print', [
            'fiche' => $fichePropose,
            'resume' => $resume,
        ]);
    }

    public function downloadFicheProposeResumePdf(Request $request, FichePropose $fichePropose, FicheProposeResume $resume)
    {
        abort_unless($fichePropose->user_id === $request->user()->id, 403);
        abort_unless((int) $resume->fiche_propose_id === (int) $fichePropose->id, 404);

        $fichePropose->load(['contacts', 'user']);
        $resume->load('user');

        $pdf = Pdf::loadView('pages.fiche-propose.resume-pdf', [
            'fiche' => $fichePropose,
            'resume' => $resume,
        ])->setPaper('a4');

        $filename = 'resume-' . $fichePropose->id . '-' . $resume->id . '.pdf';

        return $pdf->download($filename);
    }

    public function storeFicheProposeResume(Request $request, FichePropose $fichePropose)
    {
        abort_unless($fichePropose->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'resume' => ['required', 'string'],
        ]);

        $fichePropose->resumes()->create([
            'user_id' => $request->user()->id,
            'titre' => $validated['titre'],
            'resume' => $validated['resume'],
        ]);

        $fichePropose->update([
            'titre' => $validated['titre'],
            'resume' => $validated['resume'],
        ]);

        return redirect()
            ->route('fiche-propose.show', $fichePropose)
            ->with('status', 'Nouveau resume ajoute avec succes.');
    }

    public function storeFichePropose(Request $request)
    {
        $validated = $request->validate([
            'dossier_type' => ['required', 'in:fiche-client,fiche-propose'],
            'titre' => ['required', 'string', 'max:255'],
            'nom_entreprise' => ['required', 'string', 'max:255'],
            'secteur_activite' => ['required', 'string', 'max:255'],
            'adresse' => ['required', 'string'],
            'resume' => ['required', 'string'],
            'contacts' => ['required', 'array', 'min:1'],
            'contacts.*.nom' => ['required', 'string', 'max:255'],
            'contacts.*.prenom' => ['required', 'string', 'max:255'],
            'contacts.*.tel' => ['required', 'regex:/^\d{10}$/'],
            'contacts.*.email' => ['nullable', 'email', 'max:255'],
            'contacts.*.poste' => ['nullable', 'string', 'max:255'],
            'piece_jointe' => ['required_if:dossier_type,fiche-client', 'nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
            'n_rc' => ['required_if:dossier_type,fiche-client', 'nullable', 'string', 'max:255'],
            'n_rc_piece' => ['required_if:dossier_type,fiche-client', 'nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
            'nif' => ['required_if:dossier_type,fiche-client', 'nullable', 'string', 'max:255'],
            'nif_piece' => ['required_if:dossier_type,fiche-client', 'nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
            'nis' => ['required_if:dossier_type,fiche-client', 'nullable', 'string', 'max:255'],
            'nis_piece' => ['required_if:dossier_type,fiche-client', 'nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ]);

        $payload = [
            'user_id' => $request->user()->id,
            'titre' => $validated['titre'],
            'nom_entreprise' => $validated['nom_entreprise'],
            'secteur_activite' => $validated['secteur_activite'],
            'adresse' => $validated['adresse'],
            'resume' => $validated['resume'],
            'is_fiche_client' => $validated['dossier_type'] === 'fiche-client',
            'converted_to_client_at' => $validated['dossier_type'] === 'fiche-client' ? now() : null,
        ];

        if ($validated['dossier_type'] === 'fiche-client') {
            $uploads = [
                'piece_jointe' => ['path' => 'piece_jointe_path', 'name' => 'piece_jointe_original_name', 'dir' => 'fiche-client-documents/contrat'],
                'n_rc_piece' => ['path' => 'n_rc_piece_path', 'name' => 'n_rc_piece_original_name', 'dir' => 'fiche-client-documents/n-rc'],
                'nif_piece' => ['path' => 'nif_piece_path', 'name' => 'nif_piece_original_name', 'dir' => 'fiche-client-documents/nif'],
                'nis_piece' => ['path' => 'nis_piece_path', 'name' => 'nis_piece_original_name', 'dir' => 'fiche-client-documents/nis'],
            ];

            $payload['n_rc'] = $validated['n_rc'];
            $payload['nif'] = $validated['nif'];
            $payload['nis'] = $validated['nis'];

            foreach ($uploads as $input => $columns) {
                $file = $validated[$input];
                $payload[$columns['path']] = $file->store($columns['dir'], 'public');
                $payload[$columns['name']] = $file->getClientOriginalName();
            }
        }

        $fichePropose = FichePropose::create($payload);

        foreach ($validated['contacts'] as $contact) {
            $fichePropose->contacts()->create([
                'nom' => $contact['nom'],
                'prenom' => $contact['prenom'],
                'tel' => $contact['tel'],
                'email' => $contact['email'] ?? null,
                'poste' => $contact['poste'] ?? null,
            ]);
        }

        $fichePropose->resumes()->create([
            'user_id' => $request->user()->id,
            'titre' => $validated['titre'],
            'resume' => $validated['resume'],
        ]);

        return redirect()
            ->route('new-dossier')
            ->with('status', $validated['dossier_type'] === 'fiche-client'
                ? 'Fiche client enregistree avec succes.'
                : 'Prospect enregistre avec succes.');
    }
}
