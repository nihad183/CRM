<?php

namespace App\Services;

use App\Models\ComplianceEntry;
use App\Models\ComplianceMatch;
use App\Models\ComplianceUpload;
use App\Models\FichePropose;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class ComplianceService
{
    private const MATCH_THRESHOLD = 75.0;

    private const HEADERS = [
        'full_name' => ['nom et prenom', 'nom prenom', 'nom', 'name', 'full name'],
        'father_name' => ['nom du pere', 'pere', 'father name'],
        'mother_name' => ['nom et prenom de la mere', 'nom de la mere', 'mere', 'mother name'],
        'nationality' => ['nationalite', 'nationality'],
        'birth_date' => ['date naissance', 'date de naissance', 'birth date'],
        'birth_place' => ['lieu naissance', 'lieu de naissance', 'birth place'],
        'document_number' => ['nin passeport', 'nin/passport', 'nin', 'passeport', 'passport', 'nin/passeport'],
    ];

    public function importUploadedFile(UploadedFile $file, User $user): ComplianceUpload
    {
        return DB::transaction(function () use ($file, $user) {
            $extension = strtolower($file->getClientOriginalExtension() ?: 'xlsx');
            $filename = (string) Str::uuid() . '.' . $extension;
            $path = $file->storeAs('compliance/uploads', $filename, 'local');

            $upload = ComplianceUpload::create([
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'uploaded_by' => $user->id,
            ]);

            $rows = $this->readSpreadsheet(Storage::disk('local')->path($path), $extension);
            $entries = collect($rows)
                ->map(fn (array $row) => $this->normalizeRow($row))
                ->filter(fn (array $row) => $this->hasPersonData($row))
                ->values();

            $createdEntries = $entries->map(function (array $entry) use ($upload) {
                return ComplianceEntry::create([
                    ...$entry,
                    'compliance_upload_id' => $upload->id,
                    'source_type' => 'upload',
                    'raw_payload' => $entry,
                ]);
            });

            $matches = $this->filterEntries($createdEntries, $upload);
            $bestRatio = $matches->max('match_ratio') ?? 0;

            $upload->update([
                'entries_count' => $createdEntries->count(),
                'filtering_result' => $matches->isEmpty() ? 'Aucune correspondance' : $matches->count() . ' correspondance(s)',
                'best_match_ratio' => $bestRatio,
                'filtered_at' => now(),
            ]);

            return $upload->fresh(['uploader', 'matches.entry', 'matches.matchedEntry']);
        });
    }

    public function registerFichePeople(FichePropose $fichePropose): Collection
    {
        ComplianceEntry::query()
            ->where('fiche_propose_id', $fichePropose->id)
            ->delete();

        $people = collect();

        if ($this->hasPersonData((array) $fichePropose->legal_representative)) {
            $people->push(['person_role' => 'Representant legal', ...(array) $fichePropose->legal_representative]);
        }

        foreach ((array) $fichePropose->authorized_signatories as $person) {
            if ($this->hasPersonData((array) $person)) {
                $people->push(['person_role' => 'Mandataire signature', ...(array) $person]);
            }
        }

        foreach ((array) $fichePropose->shareholders as $person) {
            if ($this->hasPersonData((array) $person)) {
                $people->push(['person_role' => 'Actionnaire', ...(array) $person]);
            }
        }

        $entries = $people->map(function (array $person) use ($fichePropose) {
            $entry = $this->normalizeRow($person);

            return ComplianceEntry::create([
                ...$entry,
                'fiche_propose_id' => $fichePropose->id,
                'source_type' => 'dossier',
                'person_role' => $person['person_role'] ?? null,
                'raw_payload' => $entry,
            ]);
        });

        $matches = $this->filterEntries($entries);

        if ($matches->isNotEmpty()) {
            $fichePropose->update(['compliance_status' => 'pending_validation']);
        } elseif ($fichePropose->compliance_status === 'pending_validation') {
            $fichePropose->update(['compliance_status' => 'clear']);
        }

        return $matches;
    }

    public function decide(ComplianceMatch $match, User $user, string $decision, ?string $comment = null): void
    {
        $match->update([
            'decision_status' => $decision,
            'decided_by' => $user->id,
            'decided_at' => now(),
            'comment' => $comment,
        ]);

        if ($match->fichePropose) {
            $hasPending = $match->fichePropose
                ->complianceMatches()
                ->where('decision_status', 'pending')
                ->exists();

            if (! $hasPending) {
                $match->fichePropose->update([
                    'compliance_status' => $decision === 'approved' ? 'validated' : 'refused',
                ]);
            }
        }
    }

    private function filterEntries(Collection $entries, ?ComplianceUpload $upload = null): Collection
    {
        if ($entries->isEmpty()) {
            return collect();
        }

        $entryIds = $entries->pluck('id');

        $candidates = ComplianceEntry::query()
            ->with(['fichePropose', 'upload'])
            ->whereNotIn('id', $entryIds)
            ->when($upload, fn ($query) => $query->where(function ($nested) use ($upload) {
                $nested->whereNull('compliance_upload_id')
                    ->orWhere('compliance_upload_id', '<>', $upload->id);
            }))
            ->get();

        return $entries->flatMap(function (ComplianceEntry $entry) use ($candidates, $upload) {
            return $candidates
                ->map(function (ComplianceEntry $candidate) use ($entry, $upload) {
                    $comparison = $this->compareEntries($entry, $candidate);

                    if ($comparison['ratio'] < self::MATCH_THRESHOLD) {
                        return null;
                    }

                    $fiche = $entry->fichePropose;
                    $matchedFiche = $candidate->fichePropose;

                    return ComplianceMatch::create([
                        'compliance_upload_id' => $upload?->id,
                        'compliance_entry_id' => $entry->id,
                        'matched_entry_id' => $candidate->id,
                        'fiche_propose_id' => $fiche?->id,
                        'matched_fiche_propose_id' => $matchedFiche?->id,
                        'ref_dossier' => $fiche ? 'D-' . str_pad((string) $fiche->id, 5, '0', STR_PAD_LEFT) : ($upload ? 'IMP-' . $upload->id : null),
                        'nom_dossier' => $fiche?->nom_entreprise ?? $entry->full_name,
                        'matched_name' => $matchedFiche?->nom_entreprise ?? $candidate->full_name,
                        'match_ratio' => $comparison['ratio'],
                        'matched_information' => $comparison['fields'],
                    ]);
                })
                ->filter();
        })->values();
    }

    private function compareEntries(ComplianceEntry $entry, ComplianceEntry $candidate): array
    {
        $fields = [
            'full_name' => ['label' => 'Nom et prenom', 'weight' => 26],
            'father_name' => ['label' => 'Nom du pere', 'weight' => 10],
            'mother_name' => ['label' => 'Nom et prenom de la mere', 'weight' => 14],
            'nationality' => ['label' => 'Nationalite', 'weight' => 8],
            'birth_date' => ['label' => 'Date naissance', 'weight' => 16],
            'birth_place' => ['label' => 'Lieu naissance', 'weight' => 10],
            'document_number' => ['label' => 'NIN/Passeport', 'weight' => 32],
        ];

        $totalWeight = 0;
        $score = 0;
        $matchedFields = [];

        foreach ($fields as $field => $meta) {
            $left = $entry->{$field};
            $right = $candidate->{$field};

            if (! filled($left) || ! filled($right)) {
                continue;
            }

            $ratio = $this->fieldRatio($field, (string) $left, (string) $right);
            $totalWeight += $meta['weight'];
            $score += $ratio * $meta['weight'];

            if ($ratio >= self::MATCH_THRESHOLD) {
                $matchedFields[] = $meta['label'];
            }
        }

        return [
            'ratio' => $totalWeight > 0 ? round($score / $totalWeight, 2) : 0,
            'fields' => $matchedFields,
        ];
    }

    private function fieldRatio(string $field, string $left, string $right): float
    {
        $left = $this->normalizeComparableValue($left);
        $right = $this->normalizeComparableValue($right);

        if ($left === '' || $right === '') {
            return 0;
        }

        if (in_array($field, ['birth_date', 'document_number'], true)) {
            return $left === $right ? 100.0 : $this->textRatio($left, $right);
        }

        return $this->textRatio($left, $right);
    }

    private function textRatio(string $left, string $right): float
    {
        similar_text($left, $right, $percent);

        return round((float) $percent, 2);
    }

    private function normalizeComparableValue(string $value): string
    {
        $value = Str::ascii(Str::lower(trim($value)));
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?? '';

        return trim(preg_replace('/\s+/', ' ', $value) ?? '');
    }

    private function normalizeRow(array $row): array
    {
        $normalized = [];

        foreach (self::HEADERS as $target => $aliases) {
            $normalized[$target] = $this->valueForHeader($row, $target, $aliases);
        }

        $normalized['birth_date'] = $this->normalizeDate($normalized['birth_date']);

        return $normalized;
    }

    private function valueForHeader(array $row, string $target, array $aliases): ?string
    {
        if (array_key_exists($target, $row)) {
            return $this->cleanValue($row[$target]);
        }

        foreach ($row as $header => $value) {
            $normalizedHeader = $this->normalizeComparableValue((string) $header);

            foreach ($aliases as $alias) {
                if ($normalizedHeader === $this->normalizeComparableValue($alias)) {
                    return $this->cleanValue($value);
                }
            }
        }

        return null;
    }

    private function cleanValue(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function normalizeDate(?string $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::create(1899, 12, 30)->addDays((int) $value)->toDateString();
        }

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, trim($value))->toDateString();
            } catch (\Throwable) {
                //
            }
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function hasPersonData(array $row): bool
    {
        return filled($row['full_name'] ?? null)
            || filled($row['document_number'] ?? null)
            || filled($row['birth_date'] ?? null);
    }

    private function readSpreadsheet(string $path, string $extension): array
    {
        return match ($extension) {
            'csv', 'txt' => $this->readCsv($path),
            'xlsx' => $this->readXlsx($path),
            default => throw new RuntimeException('Format fichier non supporte. Utilisez XLSX ou CSV.'),
        };
    }

    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException('Impossible de lire le fichier CSV.');
        }

        $headers = fgetcsv($handle, 0, ';') ?: [];
        if (count($headers) <= 1) {
            rewind($handle);
            $headers = fgetcsv($handle, 0, ',') ?: [];
            $delimiter = ',';
        } else {
            $delimiter = ';';
        }

        $rows = [];
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rows[] = array_combine($headers, array_pad($data, count($headers), null)) ?: [];
        }

        fclose($handle);

        return $rows;
    }

    private function readXlsx(string $path): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Extension PHP ZipArchive requise pour lire les fichiers XLSX.');
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('Impossible d ouvrir le fichier XLSX.');
        }

        $sharedStrings = $this->readSharedStrings($zip);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            throw new RuntimeException('Feuille Excel introuvable.');
        }

        $sheet = new SimpleXMLElement($sheetXml);
        $rows = [];

        foreach ($sheet->sheetData->row as $row) {
            $cells = [];
            foreach ($row->c as $cell) {
                $reference = (string) $cell['r'];
                $column = preg_replace('/\d+/', '', $reference) ?: '';
                $index = $this->columnIndex($column);
                $type = (string) $cell['t'];
                $value = (string) $cell->v;
                $cells[$index] = $type === 's' ? ($sharedStrings[(int) $value] ?? '') : $value;
            }

            if ($cells !== []) {
                ksort($cells);
                $rows[] = $cells;
            }
        }

        $headers = array_map('trim', array_values($rows[0] ?? []));

        return collect(array_slice($rows, 1))
            ->map(function (array $row) use ($headers) {
                $values = [];
                for ($index = 0; $index < count($headers); $index++) {
                    $values[] = $row[$index] ?? null;
                }

                return array_combine($headers, $values) ?: [];
            })
            ->all();
    }

    private function readSharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');

        if ($xml === false) {
            return [];
        }

        $shared = new SimpleXMLElement($xml);
        $strings = [];

        foreach ($shared->si as $item) {
            if (isset($item->t)) {
                $strings[] = (string) $item->t;
                continue;
            }

            $value = '';
            foreach ($item->r as $run) {
                $value .= (string) $run->t;
            }

            $strings[] = $value;
        }

        return $strings;
    }

    private function columnIndex(string $column): int
    {
        $index = 0;
        foreach (str_split($column) as $letter) {
            $index = $index * 26 + ord(strtoupper($letter)) - 64;
        }

        return $index - 1;
    }
}
