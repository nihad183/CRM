@extends('layouts.app')

@section('content')
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Tahoma, Arial, sans-serif;
            background:
                linear-gradient(rgba(15, 23, 42, 0.94), rgba(15, 23, 42, 0.86)),
                url('{{ asset('images/crm.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            color: #020617;
        }

        .page-shell {
            min-height: 100vh;
            padding: 92px 48px 44px;
        }

        .page-inner {
            width: min(1200px, 100%);
        }

        .page-head {
            margin-bottom: 22px;
        }

        .page-head h1 {
            margin: 0 0 6px;
            font-size: 24px;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: 0;
            color: #ffffff;
        }

        .page-head p {
            margin: 0;
            color: #dbeafe;
            font-size: 15px;
        }

        .tabs {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .tab-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 38px;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #3b22ff;
            background: #ffffff;
            color: #3b22ff;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
        }

        .tab-button svg {
            width: 16px;
            height: 16px;
        }

        .tab-button.is-active {
            background: #3b22ff;
            color: #ffffff;
        }

        .panel {
            display: none;
        }

        .panel.is-active {
            display: block;
        }

        .content-box {
            border: 1px solid #dbe1ea;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .box-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 56px;
            padding: 0 18px;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 800;
        }

        .box-head svg {
            width: 18px;
            height: 18px;
            color: #3b22ff;
        }

        .box-body {
            padding: 18px;
        }

        .feedback,
        .error-box {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
        }

        .feedback {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .upload-form {
            display: grid;
            gap: 14px;
            margin-bottom: 22px;
        }

        .field {
            display: grid;
            gap: 10px;
        }

        label {
            font-size: 14px;
            color: #020617;
        }

        input[type="file"],
        textarea {
            width: 100%;
            min-height: 38px;
            border: 1px solid #d6dee8;
            border-radius: 6px;
            color: #020617;
            background: #ffffff;
        }

        input[type="file"] {
            padding: 0;
        }

        input[type="file"]::file-selector-button {
            min-height: 36px;
            margin-right: 12px;
            padding: 0 12px;
            border: 0;
            border-right: 1px solid #d6dee8;
            background: #f8fafc;
            color: #020617;
            cursor: pointer;
        }

        textarea {
            min-height: 42px;
            padding: 10px 12px;
            resize: vertical;
        }

        button {
            font-family: inherit;
        }

        .primary-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: fit-content;
            min-height: 38px;
            padding: 8px 12px;
            border: 0;
            border-radius: 7px;
            background: #3b22ff;
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
        }

        .primary-button svg {
            width: 16px;
            height: 16px;
        }

        .section-title {
            margin: 0 0 14px;
            font-size: 20px;
            line-height: 1.25;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 780px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px 4px;
            border-bottom: 1px solid #dbe1ea;
            text-align: left;
            vertical-align: top;
            font-size: 14px;
        }

        th {
            font-weight: 800;
            color: #020617;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .ratio {
            display: inline-flex;
            min-width: 70px;
            justify-content: center;
            padding: 4px 8px;
            border-radius: 999px;
            background: #fef3c7;
            color: #92400e;
            font-weight: 800;
        }

        .ratio.high {
            background: #fee2e2;
            color: #991b1b;
        }

        .actions {
            display: grid;
            gap: 8px;
            min-width: 240px;
        }

        .match-details {
            min-width: 300px;
        }

        .match-details summary {
            min-height: 34px;
            padding: 0 10px;
            border: 1px solid #dbe1ea;
            border-radius: 7px;
            background: #f8fafc;
            font-size: 13px;
            font-weight: 800;
        }

        .match-details summary::after {
            width: 8px;
            height: 8px;
        }

        .match-details[open] summary {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .match-details-body {
            display: grid;
            gap: 12px;
            padding: 12px;
            border: 1px solid #dbe1ea;
            border-top: 0;
            border-radius: 0 0 7px 7px;
            background: #ffffff;
        }

        .matched-fiche {
            display: grid;
            gap: 12px;
            padding: 12px;
            border: 1px solid #c9d8ea;
            border-radius: 8px;
            background: #ffffff;
        }

        .matched-fiche-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .matched-fiche-title {
            display: grid;
            gap: 3px;
        }

        .matched-fiche-title strong {
            color: #020617;
            font-size: 14px;
        }

        .matched-fiche-title span {
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .doc-links {
            display: flex;
            gap: 7px;
            flex-wrap: wrap;
        }

        .doc-link {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 5px 8px;
            border-radius: 7px;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
        }

        .doc-link.is-empty {
            border-color: #e2e8f0;
            background: #f8fafc;
            color: #64748b;
        }

        .people-section {
            display: grid;
            gap: 8px;
        }

        .people-title {
            margin: 0;
            font-size: 13px;
            font-weight: 800;
            color: #3b22ff;
        }

        .people-table-wrap {
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            background: #f8fafc;
        }

        .people-table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
        }

        .people-table th,
        .people-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            font-size: 12px;
            vertical-align: top;
        }

        .people-table th {
            background: #eef5ff;
            color: #020617;
            font-weight: 800;
            white-space: nowrap;
        }

        .people-table td {
            color: #334155;
            overflow-wrap: anywhere;
        }

        .people-table tbody tr:last-child td {
            border-bottom: none;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-buttons button {
            min-height: 34px;
            padding: 7px 10px;
            border: 0;
            border-radius: 7px;
            cursor: pointer;
            font-weight: 700;
        }

        .approve {
            background: #dcfce7;
            color: #166534;
        }

        .refuse {
            background: #fee2e2;
            color: #991b1b;
        }

        .comment {
            background: #e0f2fe;
            color: #075985;
        }

        .accordion {
            border: 1px solid #dbe1ea;
            border-radius: 6px;
            overflow: hidden;
        }

        details + details {
            border-top: 1px solid #dbe1ea;
        }

        summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 52px;
            padding: 0 20px;
            cursor: pointer;
            list-style: none;
            font-size: 16px;
        }

        summary::-webkit-details-marker {
            display: none;
        }

        summary::after {
            content: "";
            width: 10px;
            height: 10px;
            border-right: 1.5px solid #020617;
            border-bottom: 1.5px solid #020617;
            transform: rotate(45deg);
            transition: transform 0.16s ease;
        }

        details[open] summary::after {
            transform: rotate(225deg);
        }

        .accordion-body {
            padding: 0 20px 16px;
        }

        .empty-state {
            padding: 14px 4px;
            color: #64748b;
        }

        @media (max-width: 760px) {
            .page-shell {
                padding: 92px 18px 32px;
            }

            .tab-button {
                flex: 1 1 auto;
                justify-content: center;
            }

            .box-body {
                padding: 16px;
            }
        }
    </style>

    <div class="page-shell">
        <div class="page-inner">
            <div class="page-head">
                <h1>Services conformité</h1>
                <p>Import Excel, résultats et historique des traitements.</p>
            </div>

            @if (session('status'))
                <div class="feedback">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="error-box">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="tabs" role="tablist" aria-label="Services conformite">
                <button class="tab-button is-active" type="button" data-tab-button="import">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M12 3v12"></path>
                        <path d="m7 8 5-5 5 5"></path>
                        <path d="M5 15v4h14v-4"></path>
                    </svg>
                    Import Excel
                </button>
                <button class="tab-button" type="button" data-tab-button="results">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M4 19h16"></path>
                        <path d="M7 15V9"></path>
                        <path d="M12 15V5"></path>
                        <path d="M17 15v-3"></path>
                        <rect x="5" y="7" width="4" height="10" rx="1"></rect>
                        <rect x="10" y="3" width="4" height="14" rx="1"></rect>
                        <rect x="15" y="10" width="4" height="7" rx="1"></rect>
                    </svg>
                    Résultats
                </button>
                <button class="tab-button" type="button" data-tab-button="history">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 7v5l3 2"></path>
                    </svg>
                    Historique
                </button>
            </div>

            <section class="panel is-active" data-tab-panel="import">
                <div class="content-box">
                    <div class="box-head">
                        <span>Import Excel</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <path d="M14 2v6h6"></path>
                            <path d="M8 13h8"></path>
                            <path d="M8 17h8"></path>
                            <path d="M8 9h2"></path>
                        </svg>
                    </div>
                    <div class="box-body">
                        <form action="{{ route('compliance.upload') }}" method="POST" enctype="multipart/form-data" class="upload-form">
                            @csrf
                            <div class="field">
                                <label for="compliance-file">Fichier Excel</label>
                                <input id="compliance-file" type="file" name="file" accept=".xlsx,.csv,.txt" required>
                            </div>
                            <button type="submit" class="primary-button">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M12 3v12"></path>
                                    <path d="m7 8 5-5 5 5"></path>
                                    <path d="M5 15v4h14v-4"></path>
                                </svg>
                                Importer
                            </button>
                        </form>

                        <h2 class="section-title">Fichiers importés</h2>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Nom du fichier</th>
                                        <th>Importé par</th>
                                        <th>Entrées</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($uploads as $upload)
                                        <tr>
                                            <td>{{ $upload->created_at?->format('d/m/Y H:i') }}</td>
                                            <td>{{ $upload->original_name }}</td>
                                            <td>{{ $upload->uploader?->name ?? '-' }}</td>
                                            <td>{{ $upload->entries_count ?: '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="empty-state">Aucun fichier importé.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <section class="panel" data-tab-panel="results">
                <div class="content-box">
                    <div class="box-head">
                        <span>Résultats</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M4 19h16"></path>
                            <path d="M7 15V9"></path>
                            <path d="M12 15V5"></path>
                            <path d="M17 15v-3"></path>
                        </svg>
                    </div>
                    <div class="box-body">
                        @php
                            $personFields = [
                                'full_name' => 'Nom complet',
                                'father_name' => 'Nom pere',
                                'mother_name' => 'Nom mere',
                                'nationality' => 'Nationalite',
                                'birth_date' => 'Date naissance',
                                'birth_place' => 'Lieu naissance',
                                'document_number' => 'Document',
                            ];

                            $normalizePeople = function ($people) {
                                if (blank($people)) {
                                    return [];
                                }

                                $people = (array) $people;
                                $personKeys = ['full_name', 'father_name', 'mother_name', 'nationality', 'birth_date', 'birth_place', 'document_number'];
                                $personHasData = fn (array $person) => collect($personKeys)
                                    ->contains(fn ($key) => filled($person[$key] ?? null));

                                if (collect($personKeys)->contains(fn ($key) => array_key_exists($key, $people))) {
                                    return $personHasData($people) ? [$people] : [];
                                }

                                return collect($people)
                                    ->map(fn ($person) => (array) $person)
                                    ->filter($personHasData)
                                    ->values()
                                    ->all();
                            };

                            $documentsForFiche = function ($fiche) {
                                if (! $fiche) {
                                    return [];
                                }

                                return collect([
                                    'contract' => ['label' => 'Contrat', 'path' => $fiche->piece_jointe_path],
                                    'n_rc' => ['label' => 'RC', 'path' => $fiche->n_rc_piece_path],
                                    'nif' => ['label' => 'NIF', 'path' => $fiche->nif_piece_path],
                                    'nis' => ['label' => 'NIS', 'path' => $fiche->nis_piece_path],
                                ])
                                    ->filter(fn ($document) => filled($document['path']))
                                    ->map(fn ($document, $type) => [
                                        'label' => $document['label'],
                                        'url' => route('fiche-propose.documents.download', [$fiche, $type]),
                                    ])
                                    ->values()
                                    ->all();
                            };
                        @endphp
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Ref dossier</th>
                                        <th>Nom dossier</th>
                                        <th>Match ratio</th>
                                        <th>Nom détecté</th>
                                        <th>Information</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($matches as $match)
                                        <tr>
                                            <td>{{ $match->ref_dossier ?? '-' }}</td>
                                            <td>{{ $match->nom_dossier ?? '-' }}</td>
                                            <td><span class="ratio {{ $match->match_ratio >= 80 ? 'high' : '' }}">{{ number_format((float) $match->match_ratio, 2) }}%</span></td>
                                            <td>{{ $match->matched_name ?? $match->matchedEntry?->full_name ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $detailsFiches = collect([$match->fichePropose, $match->matchedFichePropose])
                                                        ->filter()
                                                        ->unique('id')
                                                        ->values();
                                                    $hasComplianceDetails = $detailsFiches->isNotEmpty();
                                                @endphp

                                                <div>{{ implode(', ', $match->matched_information ?? []) ?: '-' }}</div>

                                                @if ($hasComplianceDetails)
                                                    <details class="match-details">
                                                        <summary>Details</summary>
                                                        <div class="match-details-body">
                                                            @foreach ($detailsFiches as $fiche)
                                                                @php
                                                                    $legalRepresentatives = $normalizePeople($fiche->legal_representative);
                                                                    $authorizedSignatories = $normalizePeople($fiche->authorized_signatories);
                                                                    $shareholders = $normalizePeople($fiche->shareholders);
                                                                    $documents = $documentsForFiche($fiche);
                                                                @endphp

                                                                <div class="matched-fiche">
                                                                    <div class="matched-fiche-head">
                                                                        <div class="matched-fiche-title">
                                                                            <strong>{{ $fiche->nom_entreprise ?: 'Dossier #' . $fiche->id }}</strong>
                                                                            <span>{{ $fiche->user?->companyLabel() ?? 'Invest Market' }}</span>
                                                                        </div>
                                                                        <div class="doc-links">
                                                                            @forelse ($documents as $document)
                                                                                <a class="doc-link" href="{{ $document['url'] }}">{{ $document['label'] }}</a>
                                                                            @empty
                                                                                <span class="doc-link is-empty">Aucun fichier</span>
                                                                            @endforelse
                                                                        </div>
                                                                    </div>

                                                                    @foreach ([
                                                                        'Representant legal' => $legalRepresentatives,
                                                                        'Mandataires signature' => $authorizedSignatories,
                                                                        'Actionnaires' => $shareholders,
                                                                    ] as $sectionTitle => $people)
                                                                        @if (filled($people))
                                                                            <div class="people-section">
                                                                                <h3 class="people-title">{{ $sectionTitle }}</h3>
                                                                                <div class="people-table-wrap">
                                                                                    <table class="people-table">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                @foreach ($personFields as $label)
                                                                                                    <th>{{ $label }}</th>
                                                                                                @endforeach
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            @foreach ($people as $person)
                                                                                                <tr>
                                                                                                    @foreach ($personFields as $field => $label)
                                                                                                        <td>{{ $person[$field] ?? '-' }}</td>
                                                                                                    @endforeach
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </details>
                                                @endif
                                            </td>
                                            <td>
                                                <form class="actions" action="{{ route('compliance.matches.decision', $match) }}" method="POST">
                                                    @csrf
                                                    <textarea name="comment" placeholder="Commentaire"></textarea>
                                                    <div class="action-buttons">
                                                        <button class="approve" name="decision" value="approved">Approve</button>
                                                        <button class="refuse" name="decision" value="refused">Refuse</button>
                                                        <button class="comment" name="decision" value="commented">Commentaire</button>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="empty-state">Aucun résultat en attente.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <section class="panel" data-tab-panel="history">
                <div class="content-box">
                    <div class="box-head">
                        <span>Historique</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 7v5l3 2"></path>
                        </svg>
                    </div>
                    <div class="box-body">
                        <div class="accordion">
                            <details>
                                <summary>Historique filtrage</summary>
                                <div class="accordion-body">
                                    <div class="table-wrap">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Date filtrage</th>
                                                    <th>Fichier</th>
                                                    <th>Importé par</th>
                                                    <th>Entrées importées</th>
                                                    <th>Résultat filtrage</th>
                                                    <th>Meilleur taux</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($uploads as $upload)
                                                    <tr>
                                                        <td>{{ $upload->filtered_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                                        <td>{{ $upload->original_name }}</td>
                                                        <td>{{ $upload->uploader?->name ?? '-' }}</td>
                                                        <td>{{ $upload->entries_count }}</td>
                                                        <td>{{ $upload->filtering_result }}</td>
                                                        <td><span class="ratio {{ $upload->best_match_ratio >= 80 ? 'high' : '' }}">{{ number_format((float) $upload->best_match_ratio, 2) }}%</span></td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="6" class="empty-state">Aucun filtrage.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </details>

                            <details>
                                <summary>Historique décisions</summary>
                                <div class="accordion-body">
                                    <div class="table-wrap">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Date d’analyse</th>
                                                    <th>Résultat</th>
                                                    <th>Pourcentage de correspondance</th>
                                                    <th>Décidé par</th>
                                                    <th>Décision</th>
                                                    <th>Commentaire</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($decisions as $decision)
                                                    <tr>
                                                        <td>{{ $decision->decided_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                                        <td>{{ $decision->matched_name ?? $decision->entry?->full_name ?? '-' }}</td>
                                                        <td><span class="ratio {{ $decision->match_ratio >= 80 ? 'high' : '' }}">{{ number_format((float) $decision->match_ratio, 2) }}%</span></td>
                                                        <td>{{ $decision->decider?->name ?? '-' }}</td>
                                                        <td>{{ ucfirst($decision->decision_status) }}</td>
                                                        <td>{{ $decision->comment ?? '-' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="6" class="empty-state">Aucune décision.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </details>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = Array.from(document.querySelectorAll('[data-tab-button]'));
            const panels = Array.from(document.querySelectorAll('[data-tab-panel]'));
            const validTabs = buttons.map((button) => button.dataset.tabButton);

            function activateTab(tab) {
                const nextTab = validTabs.includes(tab) ? tab : 'import';

                buttons.forEach((button) => {
                    button.classList.toggle('is-active', button.dataset.tabButton === nextTab);
                });

                panels.forEach((panel) => {
                    panel.classList.toggle('is-active', panel.dataset.tabPanel === nextTab);
                });

                window.history.replaceState(null, '', '#' + nextTab);
            }

            buttons.forEach((button) => {
                button.addEventListener('click', function () {
                    activateTab(button.dataset.tabButton);
                });
            });

            activateTab(window.location.hash.replace('#', '') || 'import');
        });
    </script>
@endsection
