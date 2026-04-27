<?php

namespace App\Http\Controllers;

use App\Models\FichePropose;
use App\Models\FicheProposeResume;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewDossierController extends Controller
{
    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }

    private function ensureCanAccessFiche(Request $request, FichePropose $fichePropose): void
    {
        $user = $request->user();

        abort_unless(
            $user !== null && ($user->isAdmin() || (int) $fichePropose->user_id === (int) $user->id),
            403
        );
    }

    private function ensureEmployeeCommercial(Request $request): void
    {
        abort_unless($request->user()?->isEmployee(), 403);
    }

    private function normalizeContractAmount(?string $amount): ?string
    {
        if ($amount === null) {
            return null;
        }

        return str_replace([',', ' '], '', trim($amount));
    }

    public function create()
    {
        return view('pages.new-dossier');
    }

    public function indexFichePropose(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $fiches = FichePropose::query()
            ->where(function ($query) {
                $query->where('is_fiche_client', false)
                    ->orWhereNull('is_fiche_client');
            })
            ->when(! $request->user()?->isAdmin(), function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
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
            ->where('is_fiche_client', true)
            ->when(! $request->user()?->isAdmin(), function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
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
        $this->ensureCanAccessFiche($request, $fichePropose);

        $fichePropose->load(['contacts', 'resumes', 'user', 'pieceJointeUploader', 'conversionReviewer', 'contractUser']);

        return view('pages.fiche-propose.show', [
            'fiche' => $fichePropose,
        ]);
    }

    public function showFicheHistory(Request $request, FichePropose $fichePropose)
    {
        $this->ensureCanAccessFiche($request, $fichePropose);

        $fichePropose->load(['user', 'resumes.user', 'pieceJointeUploader', 'conversionReviewer']);

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
                'user_name' => $fichePropose->conversionReviewer?->name ?: 'Utilisateur inconnu',
                'description' => 'Le dossier a ete transforme de Prospect vers fiche client.',
                'created_at' => $fichePropose->converted_to_client_at,
            ]);
        }

        if ($fichePropose->piece_jointe_uploaded_at) {
            $events->push([
                'title' => 'Demande de transformation',
                'user_name' => $fichePropose->pieceJointeUploader?->name ?: 'Utilisateur inconnu',
                'description' => 'Une piece jointe a ete ajoutee pour demander la transformation en fiche client.',
                'created_at' => $fichePropose->piece_jointe_uploaded_at,
            ]);
        }

        if ($fichePropose->client_conversion_status === 'rejected' && $fichePropose->conversion_reviewed_at) {
            $events->push([
                'title' => 'Transformation refusee',
                'user_name' => $fichePropose->conversionReviewer?->name ?: 'Utilisateur inconnu',
                'description' => 'La demande de transformation en fiche client a ete refusee.',
                'created_at' => $fichePropose->conversion_reviewed_at,
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
        $this->ensureCanAccessFiche($request, $fichePropose);

        return view('pages.fiche-propose.resume-create', [
            'fiche' => $fichePropose,
        ]);
    }

    public function createFicheClientDocument(Request $request, FichePropose $fichePropose)
    {
        $this->ensureCanAccessFiche($request, $fichePropose);

        return view('pages.fiche-client.document-create', [
            'fiche' => $fichePropose,
        ]);
    }

    public function editFicheClientDocuments(Request $request, FichePropose $fichePropose)
    {
        $this->ensureCanAccessFiche($request, $fichePropose);
        abort_unless($fichePropose->is_fiche_client, 404);

        return view('pages.fiche-client.documents', [
            'fiche' => $fichePropose,
        ]);
    }

    public function storeFicheClientDocument(Request $request, FichePropose $fichePropose)
    {
        $this->ensureCanAccessFiche($request, $fichePropose);
        $this->ensureEmployeeCommercial($request);

        $request->merge([
            'contract_amount' => $this->normalizeContractAmount($request->input('contract_amount')),
        ]);

        $validated = $request->validate([
            'piece_jointe' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
            'contract_amount' => ['required', 'numeric', 'min:0.01'],
            'contract_signed_at' => ['required', 'date'],
        ]);

        if ($fichePropose->piece_jointe_path) {
            Storage::disk('public')->delete($fichePropose->piece_jointe_path);
        }

        $file = $validated['piece_jointe'];
        $path = $file->store('fiche-client-documents', 'public');

        $fichePropose->forceFill([
            'is_fiche_client' => false,
            'converted_to_client_at' => null,
            'piece_jointe_path' => $path,
            'piece_jointe_original_name' => $file->getClientOriginalName(),
            'contract_amount' => $validated['contract_amount'],
            'contract_signed_at' => $validated['contract_signed_at'],
            'contract_user_id' => $request->user()->id,
            'client_conversion_status' => 'pending',
            'piece_jointe_uploaded_by' => $request->user()->id,
            'piece_jointe_uploaded_at' => now(),
            'conversion_reviewed_by' => null,
            'conversion_reviewed_at' => null,
        ])->save();

        return redirect()
            ->route('fiche-propose.show', $fichePropose)
            ->with('status', 'La piece jointe a ete envoyee pour validation admin. Le dossier restera prospect jusqu a la decision.');
    }

    public function updateFicheClientDocuments(Request $request, FichePropose $fichePropose)
    {
        $this->ensureCanAccessFiche($request, $fichePropose);
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

        $fichePropose->forceFill($payload)->save();

        return redirect()
            ->route('fiche-client')
            ->with('status', 'Les informations N RC, NIF et NIS ont ete enregistrees avec succes.');
    }

    public function printFicheProposeResume(Request $request, FichePropose $fichePropose, FicheProposeResume $resume)
    {
        $this->ensureCanAccessFiche($request, $fichePropose);
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
        $this->ensureCanAccessFiche($request, $fichePropose);
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
        $this->ensureCanAccessFiche($request, $fichePropose);

        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'resume' => ['required', 'string'],
        ]);

        $resume = $fichePropose->resumes()->make([
            'titre' => $validated['titre'],
            'resume' => $validated['resume'],
        ]);
        $resume->user_id = $request->user()->id;
        $resume->save();

        $fichePropose->forceFill([
            'titre' => $validated['titre'],
            'resume' => $validated['resume'],
        ])->save();

        return redirect()
            ->route('fiche-propose.show', $fichePropose)
            ->with('status', 'Nouveau resume ajoute avec succes.');
    }

    public function storeFichePropose(Request $request)
    {
        if ($request->input('dossier_type') === 'fiche-client') {
            $this->ensureEmployeeCommercial($request);
        }

        $request->merge([
            'contract_amount' => $this->normalizeContractAmount($request->input('contract_amount')),
        ]);

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
            'contract_amount' => ['required_if:dossier_type,fiche-client', 'nullable', 'numeric', 'min:0.01'],
            'contract_signed_at' => ['required_if:dossier_type,fiche-client', 'nullable', 'date'],
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
            'client_conversion_status' => $validated['dossier_type'] === 'fiche-client' ? 'approved' : 'not_requested',
            'piece_jointe_uploaded_by' => $validated['dossier_type'] === 'fiche-client' ? $request->user()->id : null,
            'piece_jointe_uploaded_at' => $validated['dossier_type'] === 'fiche-client' ? now() : null,
            'conversion_reviewed_by' => $validated['dossier_type'] === 'fiche-client' ? $request->user()->id : null,
            'conversion_reviewed_at' => $validated['dossier_type'] === 'fiche-client' ? now() : null,
            'contract_amount' => $validated['dossier_type'] === 'fiche-client' ? $validated['contract_amount'] : null,
            'contract_signed_at' => $validated['dossier_type'] === 'fiche-client' ? $validated['contract_signed_at'] : null,
            'contract_user_id' => $validated['dossier_type'] === 'fiche-client' ? $request->user()->id : null,
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

        $fichePropose = FichePropose::forceCreate($payload);

        foreach ($validated['contacts'] as $contact) {
            $fichePropose->contacts()->create([
                'nom' => $contact['nom'],
                'prenom' => $contact['prenom'],
                'tel' => $contact['tel'],
                'email' => $contact['email'] ?? null,
                'poste' => $contact['poste'] ?? null,
            ]);
        }

        $resume = $fichePropose->resumes()->make([
            'titre' => $validated['titre'],
            'resume' => $validated['resume'],
        ]);
        $resume->user_id = $request->user()->id;
        $resume->save();

        return redirect()
            ->route('new-dossier')
            ->with('status', $validated['dossier_type'] === 'fiche-client'
                ? 'Fiche client enregistree avec succes.'
                : 'Prospect enregistre avec succes.');
    }

    public function indexClientConversionRequests(Request $request)
    {
        $this->ensureAdmin($request);

        $pendingRequests = FichePropose::query()
            ->with(['user', 'pieceJointeUploader', 'contractUser'])
            ->where('client_conversion_status', 'pending')
            ->latest('piece_jointe_uploaded_at')
            ->get();

        $rejectedRequests = FichePropose::query()
            ->with(['user', 'pieceJointeUploader', 'conversionReviewer', 'contractUser'])
            ->where('client_conversion_status', 'rejected')
            ->latest('conversion_reviewed_at')
            ->get();

        return view('pages.admin.client-conversion-requests', [
            'pendingRequests' => $pendingRequests,
            'rejectedRequests' => $rejectedRequests,
        ]);
    }

    public function indexAdminUsers(Request $request)
    {
        $this->ensureAdmin($request);

        $users = User::query()
            ->select(['id', 'name', 'email', 'phone', 'role', 'created_at'])
            ->orderBy('name')
            ->get();

        return view('pages.admin.liste-de-comarecen', [
            'users' => $users,
        ]);
    }

    public function indexAdminCompetition(Request $request)
    {
        abort_unless($request->user() !== null, 403);

        $defaultYear = now()->year;
        $selectedYear = (int) $request->integer('year', $defaultYear);

        $availableYears = FichePropose::query()
            ->whereNotNull('contract_signed_at')
            ->selectRaw('DISTINCT YEAR(contract_signed_at) as year')
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($year) => (int) $year)
            ->values();

        if (! $availableYears->contains($defaultYear)) {
            $availableYears->prepend($defaultYear);
        }

        if (! $availableYears->contains($selectedYear)) {
            $selectedYear = $defaultYear;
        }

        $users = User::query()
            ->select(['id', 'name', 'role'])
            ->where('role', 'employee')
            ->withCount([
                'signedContracts as yearly_signed_contracts_count' => function ($query) use ($selectedYear) {
                    $query->where('is_fiche_client', true)
                        ->whereYear('contract_signed_at', $selectedYear)
                        ->whereNotNull('contract_amount');
                },
            ])
            ->withSum([
                'signedContracts as yearly_contract_total' => function ($query) use ($selectedYear) {
                    $query->where('is_fiche_client', true)
                        ->whereYear('contract_signed_at', $selectedYear);
                },
            ], 'contract_amount')
            ->orderByDesc('yearly_contract_total')
            ->orderByDesc('yearly_signed_contracts_count')
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                $user->yearly_contract_total = (float) ($user->yearly_contract_total ?? 0);

                return $user;
            })
            ->values()
            ->map(function ($user, $index) {
                $user->rank = $index + 1;

                return $user;
            });

        return view('pages.admin.competition-utilisateurs', [
            'users' => $users,
            'currentYearLabel' => $selectedYear,
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears,
        ]);
    }

    public function approveClientConversionRequest(Request $request, FichePropose $fichePropose)
    {
        $this->ensureAdmin($request);
        abort_unless(in_array($fichePropose->client_conversion_status, ['pending', 'rejected'], true), 404);
        abort_unless(filled($fichePropose->piece_jointe_path), 422);
        abort_unless(
            $fichePropose->contract_amount !== null
                && filled($fichePropose->contract_signed_at)
                && filled($fichePropose->contract_user_id)
                && $fichePropose->contractUser?->isEmployee(),
            422
        );

        $fichePropose->forceFill([
            'is_fiche_client' => true,
            'converted_to_client_at' => now(),
            'client_conversion_status' => 'approved',
            'conversion_reviewed_by' => $request->user()->id,
            'conversion_reviewed_at' => now(),
        ])->save();

        return redirect()
            ->route('admin.client-conversion-requests')
            ->with('status', 'La demande a ete acceptee. Le dossier est maintenant en fiche client.');
    }

    public function rejectClientConversionRequest(Request $request, FichePropose $fichePropose)
    {
        $this->ensureAdmin($request);
        abort_unless($fichePropose->client_conversion_status === 'pending', 404);

        $fichePropose->forceFill([
            'is_fiche_client' => false,
            'converted_to_client_at' => null,
            'client_conversion_status' => 'rejected',
            'conversion_reviewed_by' => $request->user()->id,
            'conversion_reviewed_at' => now(),
        ])->save();

        return redirect()
            ->route('admin.client-conversion-requests')
            ->with('status', 'La demande a ete refusee et placee dans la section des dossiers refuses.');
    }
}
