<?php

namespace App\Http\Controllers;

use App\Models\ComplianceMatch;
use App\Models\ComplianceUpload;
use App\Services\ComplianceService;
use Illuminate\Http\Request;

class ComplianceController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureComplianceAccess($request);

        $uploads = ComplianceUpload::query()
            ->with('uploader')
            ->latest()
            ->get();

        $matches = ComplianceMatch::query()
            ->with(['entry', 'matchedEntry', 'fichePropose.user', 'matchedFichePropose.user', 'upload', 'decider'])
            ->where('decision_status', 'pending')
            ->latest()
            ->get();

        $decisions = ComplianceMatch::query()
            ->with(['entry', 'fichePropose.user', 'matchedFichePropose.user', 'upload', 'decider'])
            ->whereIn('decision_status', ['approved', 'refused', 'commented'])
            ->latest('decided_at')
            ->limit(80)
            ->get();

        return view('pages.compliance.index', [
            'uploads' => $uploads,
            'matches' => $matches,
            'decisions' => $decisions,
        ]);
    }

    public function upload(Request $request, ComplianceService $service)
    {
        $this->ensureComplianceAccess($request);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:20480', 'mimes:xlsx,csv,txt'],
        ]);

        try {
            $upload = $service->importUploadedFile($validated['file'], $request->user());
        } catch (\Throwable $exception) {
            return back()->withErrors(['file' => $exception->getMessage()]);
        }

        return redirect()
            ->route('compliance.index')
            ->with('status', 'Fichier importe: ' . $upload->original_name . '. ' . $upload->filtering_result . '.');
    }

    public function decide(Request $request, ComplianceMatch $match, ComplianceService $service)
    {
        $this->ensureComplianceAccess($request);

        $validated = $request->validate([
            'decision' => ['required', 'in:approved,refused,commented'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validated['decision'] === 'commented' && blank($validated['comment'] ?? null)) {
            return back()->withErrors(['comment' => 'Le commentaire est obligatoire pour cette action.']);
        }

        $service->decide($match, $request->user(), $validated['decision'], $validated['comment'] ?? null);

        return redirect()
            ->route('compliance.index')
            ->with('status', 'Decision enregistree.');
    }

    private function ensureComplianceAccess(Request $request): void
    {
        abort_unless($request->user()?->canAccessComplianceFeatures(), 403);
    }
}
