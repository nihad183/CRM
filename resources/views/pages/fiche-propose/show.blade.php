@extends('layouts.app')

@section('content')
    @php
        $canModifyFiche = auth()->user()->canModifyFiche($fiche);
        $resumeEntries = $fiche->resumes
            ->map(function ($resume) use ($fiche) {
                return [
                    'id' => 'resume-' . $resume->id,
                    'title' => $resume->titre ?: 'Resume sans titre',
                    'written_at' => $resume->created_at,
                    'content' => $resume->resume,
                    'print_url' => route('fiche-propose.resume.print', ['fichePropose' => $fiche, 'resume' => $resume]),
                ];
            })
            ->values();
    @endphp

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Tahoma, Arial, sans-serif;
            background:
                linear-gradient(rgba(15, 23, 42, 0.93), rgba(15, 23, 42, 0.86)),
                url('{{ asset('images/crm.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            color: #e2e8f0;
        }

        .page-shell {
            min-height: 100vh;
            padding: 110px 24px 40px;
        }

        .page-card {
            width: min(1280px, 100%);
            margin: 0 auto;
            padding: 34px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(219, 228, 238, 0.92);
            box-shadow: 0 28px 70px rgba(15, 23, 42, 0.32);
            color: #0f172a;
        }

        .top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            margin-bottom: 28px;
        }

        h1,
        h2 {
            margin: 0;
        }

        h1 {
            padding-left: 16px;
            font-size: clamp(28px, 5vw, 42px);
            border-left: 6px solid #14b8a6;
        }

        h2 {
            margin-bottom: 18px;
            padding-left: 12px;
            font-size: 24px;
            border-left: 4px solid #14b8a6;
        }

        .back-link {
            color: #0f766e;
            text-decoration: none;
            font-weight: 700;
        }

        .status-box {
            margin-bottom: 18px;
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(20, 184, 166, 0.12);
            border: 1px solid rgba(45, 212, 191, 0.3);
            color: #115e59;
        }

        .content-stack {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .section-card {
            padding: 24px;
            border-radius: 26px;
            border: 1px solid #dbe4ee;
            background: #f8fafc;
        }

        .info-stack {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .info-block {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding-bottom: 18px;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-block:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-block.is-dropdown {
            display: block;
        }

        .info-label {
            min-width: 170px;
            font-weight: 700;
            color: #0f172a;
        }

        .info-value {
            flex: 1;
            color: #334155;
            line-height: 1.8;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .info-value.inline-value {
            white-space: normal;
        }

        .doc-inline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            width: 100%;
            flex-wrap: wrap;
        }

        .doc-number {
            color: #334155;
            font-weight: 600;
            word-break: break-word;
        }

        .doc-file-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 92px;
            padding: 9px 14px;
            border-radius: 12px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            text-decoration: none;
            font-weight: 700;
            white-space: nowrap;
        }

        .resume-table-wrap {
            overflow: hidden;
            border-radius: 20px;
            border: 1px solid #dbe4ee;
            background: #ffffff;
            margin-bottom: 18px;
        }

        .contacts-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .contacts-toggle {
            width: 100%;
        }

        .contacts-toggle-summary {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: auto;
            padding: 0 0 18px;
            cursor: pointer;
            list-style: none;
            color: #0f172a;
            font-weight: 700;
            border-bottom: 1px solid #e2e8f0;
        }

        .contacts-toggle-summary::-webkit-details-marker {
            display: none;
        }

        .contacts-arrow {
            width: 10px;
            height: 10px;
            border-right: 2px solid currentColor;
            border-bottom: 2px solid currentColor;
            transform: rotate(45deg);
            transition: transform 0.2s ease;
            margin-top: 2px;
        }

        .contacts-toggle[open] .contacts-toggle-summary {
            color: #0f766e;
        }

        .contacts-toggle[open] .contacts-arrow {
            transform: rotate(225deg);
        }

        .contacts-toggle-body {
            padding: 14px 0 0;
        }

        .contact-item {
            border: 1px solid #c9d8ea;
            border-radius: 20px;
            background: #ffffff;
            overflow: hidden;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
        }

        .contact-summary {
            display: grid;
            grid-template-columns: minmax(200px, 1.15fr) minmax(170px, 1fr) minmax(150px, 0.9fr) minmax(220px, 1fr);
            gap: 18px;
            align-items: center;
            min-height: 58px;
            padding: 0 20px;
            cursor: pointer;
            list-style: none;
            color: #0f172a;
            font-weight: 700;
        }

        .contact-summary::-webkit-details-marker {
            display: none;
        }

        .contact-item[open] .contact-summary {
            background: rgba(20, 184, 166, 0.08);
            border-bottom: 1px solid #dbe4ee;
        }

        .contact-meta {
            color: #1e3a5f;
            font-weight: 600;
            word-break: break-word;
        }

        .contact-empty {
            color: #64748b;
        }

        .resume-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 540px;
        }

        .resume-table th {
            padding: 16px 18px;
            text-align: left;
            font-size: 13px;
            color: #475569;
            background: #eef5f9;
            border-bottom: 1px solid #dbe4ee;
        }

        .resume-table td {
            padding: 15px 18px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }

        .resume-table tbody tr:last-child td {
            border-bottom: none;
        }

        .resume-row {
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .resume-row:hover {
            background: rgba(20, 184, 166, 0.06);
        }

        .resume-row.is-active {
            background: rgba(20, 184, 166, 0.12);
        }

        .resume-title {
            font-weight: 700;
            color: #0f172a;
        }

        .resume-date {
            white-space: nowrap;
        }

        .resume-card-meta {
            display: none;
        }

        .print-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 90px;
            padding: 10px 14px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #0f766e, #14b8a6);
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 14px 28px rgba(20, 184, 166, 0.2);
        }

        .resume-viewer {
            display: none;
            padding: 22px;
            border-radius: 22px;
            border: 1px solid #dbe4ee;
            background: #ffffff;
        }

        .resume-viewer.is-visible {
            display: block;
        }

        .viewer-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }

        .viewer-title {
            margin: 0 0 6px;
            font-size: 22px;
            color: #0f172a;
        }

        .viewer-date {
            color: #64748b;
            font-size: 14px;
        }

        .viewer-content {
            color: #334155;
            line-height: 1.9;
            white-space: pre-wrap;
            word-break: break-word;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .back-link,
            .resume-table-wrap,
            .top-row {
                display: none;
            }

            .page-card,
            .section-card,
            .resume-viewer {
                box-shadow: none;
                border: none;
                background: #ffffff;
            }

            .content-stack {
                display: block;
            }
        }

        @media (max-width: 860px) {
            .page-card {
                padding: 22px;
            }

            .top-row,
            .viewer-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .resume-table-wrap {
                overflow-x: auto;
            }

            .info-block:not(.is-dropdown) {
                flex-direction: column;
                gap: 6px;
            }

            .contacts-toggle-summary {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }

            .info-label {
                min-width: 0;
            }

            .contact-summary {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .resume-table {
                min-width: 0;
            }

            .resume-table thead {
                display: none;
            }

            .resume-table,
            .resume-table tbody,
            .resume-table tr,
            .resume-table td {
                display: block;
                width: 100%;
            }

            .resume-table tbody {
                display: flex;
                flex-direction: column;
                gap: 14px;
                padding: 14px;
            }

            .resume-table tr {
                padding: 16px;
                border: 1px solid #dbe4ee;
                border-radius: 18px;
                background: #f8fafc;
                box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
            }

            .resume-table td {
                padding: 0;
                border-bottom: none;
            }

            .resume-title {
                margin-bottom: 10px;
                font-size: 17px;
            }

            .resume-date {
                display: none;
            }

            .resume-card-meta {
                display: block;
                margin-bottom: 14px;
                color: #64748b;
                font-size: 13px;
            }

            .resume-table td:last-child {
                margin-top: 8px;
            }

            .print-btn {
                width: 100%;
            }

            .resume-viewer {
                padding: 18px;
            }
        }
    </style>

    <div class="page-shell">
        <div class="page-card">
            <div class="top-row">
                <h1>{{ $fiche->nom_entreprise }}</h1>
                <a class="back-link" href="{{ auth()->user()->isAdmin() && in_array($fiche->client_conversion_status, ['pending', 'rejected'], true) ? route('admin.client-conversion-requests') : ($fiche->is_fiche_client ? route('fiche-client') : route('fiche-propose')) }}">Retour a la liste</a>
            </div>

            @if (session('status'))
                <div class="status-box">{{ session('status') }}</div>
            @endif

            @unless ($canModifyFiche)
                <div class="status-box">هذه البطاقة في وضع العرض فقط. يمكنك رؤية ملفات الشركة الاخرى لكن لا يمكنك تعديلها.</div>
            @endunless

            @if ($fiche->client_conversion_status === 'pending')
                <div class="status-box">Cette fiche prospect attend la validation d un administrateur pour devenir fiche client.</div>
            @elseif ($fiche->client_conversion_status === 'rejected')
                <div class="status-box">La demande de transformation a ete refusee. Une nouvelle piece jointe peut etre envoyee pour relancer la validation.</div>
            @endif

            <div class="content-stack">
                <section class="section-card">
                    <h2>Informations societe</h2>
                    <div class="info-stack">
                        <div class="info-block">
                            <span class="info-label">Nom entreprise:</span>
                            <div class="info-value">{{ $fiche->nom_entreprise }}</div>
                        </div>

                        <div class="info-block">
                            <span class="info-label">Secteur activite:</span>
                            <div class="info-value">{{ $fiche->secteur_activite ?: '-' }}</div>
                        </div>

                        <div class="info-block">
                            <span class="info-label">Adresse:</span>
                            <div class="info-value">{{ $fiche->adresse ?: '-' }}</div>
                        </div>

                        <div class="info-block is-dropdown">
                            <details class="contacts-toggle">
                                <summary class="contacts-toggle-summary">
                                    <span class="info-label">Contacts:</span>
                                    <span class="info-value"></span>
                                    <span class="contacts-arrow" aria-hidden="true"></span>
                                </summary>

                                <div class="contacts-toggle-body">
                                    <div class="contacts-list">
                                        @forelse ($fiche->contacts as $contact)
                                            <div class="contact-item">
                                                <div class="contact-summary">
                                                    <span>{{ trim($contact->nom . ' ' . $contact->prenom) ?: '-' }}</span>
                                                    <span class="contact-meta">{{ $contact->poste ?: '-' }}</span>
                                                    <span class="contact-meta">{{ $contact->tel ?: '-' }}</span>
                                                    <span class="contact-meta">{{ $contact->email ?: '-' }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="contact-empty">Aucun contact enregistre.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </details>
                        </div>

                        <div class="info-block">
                            <span class="info-label">Date creation:</span>
                            <div class="info-value">{{ $fiche->created_at?->format('Y-m-d H:i') }}</div>
                        </div>

                        <div class="info-block">
                            <span class="info-label">Societe:</span>
                            <div class="info-value">{{ $fiche->user?->companyLabel() ?? 'Invest Market' }}</div>
                        </div>

                        <div class="info-block">
                            <span class="info-label">Cree par:</span>
                            <div class="info-value">{{ $fiche->user?->name ?: '-' }}</div>
                        </div>

                        @if ($fiche->contract_amount !== null || $fiche->contract_signed_at || $fiche->contractUser || $fiche->piece_jointe_path)
                            <div class="info-block">
                                <span class="info-label">Montant contrat:</span>
                                <div class="info-value inline-value">{{ $fiche->contract_amount !== null ? number_format((float) $fiche->contract_amount, 0, '.', ',') . ' DZD' : '-' }}</div>
                            </div>

                            <div class="info-block">
                                <span class="info-label">Date signature:</span>
                                <div class="info-value inline-value">{{ $fiche->contract_signed_at?->format('Y-m-d') ?: '-' }}</div>
                            </div>

                            <div class="info-block">
                                <span class="info-label">Commercial concerne:</span>
                                <div class="info-value inline-value">{{ $fiche->contractUser?->name ?: '-' }}</div>
                            </div>

                            <div class="info-block">
                                <span class="info-label">Contrat signe:</span>
                                <div class="info-value inline-value">
                                    @if ($fiche->piece_jointe_path)
                                        <a class="doc-file-link" href="{{ route('fiche-propose.documents.download', [$fiche, 'contract']) }}">
                                            {{ $fiche->piece_jointe_original_name ?: 'Voir fichier contrat' }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($fiche->hasCompleteClientDocuments())
                            <div class="info-block">
                                <span class="info-label">N RC:</span>
                                <div class="info-value doc-inline">
                                    <span class="doc-number">{{ $fiche->n_rc }}</span>
                                    <a class="doc-file-link" href="{{ route('fiche-propose.documents.download', [$fiche, 'n_rc']) }}">
                                        Fichier
                                    </a>
                                </div>
                            </div>

                            <div class="info-block">
                                <span class="info-label">NIF:</span>
                                <div class="info-value doc-inline">
                                    <span class="doc-number">{{ $fiche->nif }}</span>
                                    <a class="doc-file-link" href="{{ route('fiche-propose.documents.download', [$fiche, 'nif']) }}">
                                        Fichier
                                    </a>
                                </div>
                            </div>

                            <div class="info-block">
                                <span class="info-label">NIS:</span>
                                <div class="info-value doc-inline">
                                    <span class="doc-number">{{ $fiche->nis }}</span>
                                    <a class="doc-file-link" href="{{ route('fiche-propose.documents.download', [$fiche, 'nis']) }}">
                                        Fichier
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="section-card">
                    <h2>Liste Resume</h2>

                    <div class="resume-table-wrap">
                        <table class="resume-table">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Date ecriture</th>
                                    <th>Imprimer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($resumeEntries as $entry)
                                    <tr
                                        class="resume-row"
                                        data-resume-row
                                        data-title="{{ $entry['title'] }}"
                                        data-date="{{ optional($entry['written_at'])->format('Y-m-d H:i') }}"
                                        data-content="{{ $entry['content'] }}"
                                    >
                                        <td>
                                            <div class="resume-title">{{ $entry['title'] }}</div>
                                            <div class="resume-card-meta">{{ optional($entry['written_at'])->format('Y-m-d H:i') }}</div>
                                        </td>
                                        <td class="resume-date">{{ optional($entry['written_at'])->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a class="print-btn" href="{{ $entry['print_url'] }}" target="_blank" rel="noopener">Imprimer</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="resume-viewer" id="resume-viewer">
                        <div class="viewer-head">
                            <div>
                                <h3 class="viewer-title" id="viewer-title"></h3>
                                <div class="viewer-date" id="viewer-date"></div>
                            </div>
                        </div>

                        <div class="viewer-content" id="viewer-content"></div>
                    </div>
                </section>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rows = Array.from(document.querySelectorAll('[data-resume-row]'));
            const viewer = document.getElementById('resume-viewer');
            const viewerTitle = document.getElementById('viewer-title');
            const viewerDate = document.getElementById('viewer-date');
            const viewerContent = document.getElementById('viewer-content');

            function activateRow(row) {
                rows.forEach((item) => item.classList.remove('is-active'));
                row.classList.add('is-active');
                viewer.classList.add('is-visible');
                viewerTitle.textContent = row.dataset.title || 'Resume';
                viewerDate.textContent = row.dataset.date || '';
                viewerContent.textContent = row.dataset.content || '';
            }

            function closeViewer() {
                rows.forEach((item) => item.classList.remove('is-active'));
                viewer.classList.remove('is-visible');
                viewerTitle.textContent = '';
                viewerDate.textContent = '';
                viewerContent.textContent = '';
            }

            rows.forEach((row) => {
                row.addEventListener('click', function (event) {
                    if (event.target.closest('.print-btn')) {
                        return;
                    }

                    if (row.classList.contains('is-active')) {
                        closeViewer();
                        return;
                    }

                    activateRow(row);
                });
            });
        });
    </script>
@endsection
