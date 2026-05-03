@extends('layouts.app')

@section('content')
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
            width: min(1180px, 100%);
            margin: 0 auto;
            padding: 34px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(219, 228, 238, 0.92);
            box-shadow: 0 28px 70px rgba(15, 23, 42, 0.32);
            color: #0f172a;
        }

        .page-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 18px;
            margin-bottom: 26px;
        }

        .page-head h1 {
            margin: 0;
            padding-left: 16px;
            font-size: clamp(30px, 5vw, 44px);
            border-left: 6px solid #14b8a6;
        }

        .page-head span {
            color: #64748b;
            font-size: 15px;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
            flex-wrap: wrap;
        }

        .search-meta {
            color: #64748b;
            font-size: 14px;
        }

        .search-box {
            position: relative;
            width: min(320px, 100%);
        }

        .search-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: #64748b;
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            padding: 12px 16px 12px 42px;
            border-radius: 16px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            font-size: 14px;
            outline: none;
        }

        .search-input:focus {
            border-color: #14b8a6;
            box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.12);
        }

        .reset-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 96px;
            padding: 12px 14px;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 700;
            border: 1px solid #cbd5e1;
        }

        .reset-button {
            background: #ffffff;
            color: #334155;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 24px;
            border: 1px solid #dbe4ee;
            background: #f8fafc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        thead th {
            padding: 18px 20px;
            text-align: left;
            font-size: 14px;
            color: #475569;
            background: #eef5f9;
            border-bottom: 1px solid #dbe4ee;
        }

        tbody td {
            padding: 18px 20px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background: rgba(20, 184, 166, 0.05);
        }

        .summary-row {
            cursor: pointer;
        }

        .summary-row.is-open {
            background: rgba(20, 184, 166, 0.06);
        }

        .expand-indicator {
            display: none;
            margin-left: auto;
            width: 34px;
            height: 34px;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #e2e8f0;
            color: #334155;
            font-size: 18px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .summary-row.is-open .expand-indicator {
            transform: rotate(45deg);
        }

        .company {
            font-weight: 700;
            color: #0f172a;
        }

        .date-cell {
            white-space: nowrap;
            color: #334155;
        }

        .details-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 98px;
            padding: 11px 16px;
            border-radius: 14px;
            background: linear-gradient(135deg, #0f766e, #14b8a6);
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
            box-shadow: 0 14px 28px rgba(20, 184, 166, 0.2);
        }

        .actions-cell {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            margin-left: 10px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            background: #e2e8f0;
            color: #334155;
        }

        .status-chip.pending {
            background: rgba(245, 158, 11, 0.16);
            color: #92400e;
        }

        .status-chip.rejected {
            background: rgba(239, 68, 68, 0.14);
            color: #991b1b;
        }

        .next-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 88px;
            padding: 11px 16px;
            border-radius: 14px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            text-decoration: none;
            font-weight: 700;
        }

        .client-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 120px;
            padding: 11px 16px;
            border-radius: 14px;
            border: 1px solid rgba(15, 118, 110, 0.16);
            background: rgba(20, 184, 166, 0.1);
            color: #0f766e;
            text-decoration: none;
            font-weight: 700;
        }

        .history-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 98px;
            padding: 11px 16px;
            border-radius: 14px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #334155;
            text-decoration: none;
            font-weight: 700;
        }

        .mobile-details-row {
            display: none;
            background: #f1f5f9;
        }

        .mobile-details {
            display: none;
            padding: 18px 20px 20px;
        }

        .mobile-details-group + .mobile-details-group {
            margin-top: 16px;
        }

        .mobile-details-label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
        }

        .empty-state {
            padding: 34px 24px;
            text-align: center;
            color: #64748b;
        }

        @media (max-width: 860px) {
            .page-card {
                padding: 22px;
            }

            .page-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .table-wrap {
                overflow-x: visible;
            }

            table {
                min-width: 0;
            }

            thead th:nth-child(3) {
                display: none;
            }

            .summary-row td:nth-child(3) {
                display: none;
            }

            .summary-row td:nth-child(1) {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .expand-indicator {
                display: inline-flex;
            }

            .mobile-details-row.is-open {
                display: table-row;
            }

            .mobile-details {
                display: block;
            }
        }
    </style>

    <div class="page-shell">
        <div class="page-card">
            <div class="page-head">
                <h1>Prospect</h1>
                <span id="results-count">{{ $fiches->count() }} fichier(s) cree(s)</span>
            </div>

            <form class="search-form" method="GET" action="{{ route('fiche-propose') }}" data-search-form>
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M20 20L16.65 16.65"></path>
                    </svg>
                    <input
                        class="search-input"
                        type="search"
                        name="search"
                        value="{{ $search ?? '' }}"
                        placeholder="Nom entreprise"
                        autocomplete="off"
                        data-search-input
                    >
                </div>
                @if (!empty($search))
                    <a class="reset-button" href="{{ route('fiche-propose') }}">Effacer</a>
                @endif
            </form>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nom entreprise</th>
                            <th>Date creation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody data-search-body>
                        @forelse ($fiches as $fiche)
                            @php
                                $canModifyFiche = auth()->user()->canModifyFiche($fiche);
                            @endphp
                            <tr class="summary-row" data-search-row data-company="{{ Str::lower($fiche->nom_entreprise) }}">
                                <td class="company">
                                    <span>{{ $fiche->nom_entreprise }}</span>
                                    <span class="status-chip">{{ $fiche->user?->companyLabel() ?? 'Invest Market' }}</span>
                                    @if ($fiche->client_conversion_status === 'pending')
                                        <span class="status-chip pending">En attente admin</span>
                                    @elseif ($fiche->client_conversion_status === 'rejected')
                                        <span class="status-chip rejected">Refuse</span>
                                    @endif
                                    <span class="expand-indicator" aria-hidden="true">+</span>
                                </td>
                                <td class="date-cell">{{ $fiche->created_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="actions-cell">
                                        <a class="details-link" href="{{ route('fiche-propose.show', $fiche) }}">Details</a>
                                        <a class="history-link" href="{{ route('fiche-propose.history', $fiche) }}">Historique</a>
                                        @if ($canModifyFiche)
                                            <a class="next-link" href="{{ route('fiche-propose.resume.create', $fiche) }}">Next</a>
                                            <a class="client-link" href="{{ route('fiche-propose.fiche-client.create', $fiche) }}">Passer en fiche client</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr class="mobile-details-row" data-details-row hidden>
                                <td colspan="3">
                                    <div class="mobile-details">
                                        <div class="mobile-details-group">
                                            <span class="mobile-details-label">Actions</span>
                                            <div class="actions-cell">
                                                <a class="details-link" href="{{ route('fiche-propose.show', $fiche) }}">Details</a>
                                                <a class="history-link" href="{{ route('fiche-propose.history', $fiche) }}">Historique</a>
                                                @if ($canModifyFiche)
                                                    <a class="next-link" href="{{ route('fiche-propose.resume.create', $fiche) }}">Next</a>
                                                    <a class="client-link" href="{{ route('fiche-propose.fiche-client.create', $fiche) }}">Passer en fiche client</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty-server-row>
                                <td colspan="3" class="empty-state">Aucun prospect n'a encore ete cree.</td>
                            </tr>
                        @endforelse
                        <tr data-empty-search-row hidden>
                            <td colspan="3" class="empty-state">Aucun resultat pour cette recherche.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('[data-search-form]');
            const input = document.querySelector('[data-search-input]');
            const rows = Array.from(document.querySelectorAll('[data-search-row]'));
            const emptySearchRow = document.querySelector('[data-empty-search-row]');
            const resultsCount = document.getElementById('results-count');

            if (!form || !input) {
                return;
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();
            });

            function applyFilter() {
                const query = input.value.trim().toLowerCase();
                let visibleCount = 0;

                rows.forEach(function (row) {
                    const company = row.dataset.company || '';
                    const detailsRow = row.nextElementSibling;
                    const isVisible = query === '' || company.includes(query);
                    row.hidden = !isVisible;
                    if (detailsRow && detailsRow.hasAttribute('data-details-row')) {
                        detailsRow.hidden = true;
                        detailsRow.classList.remove('is-open');
                        row.classList.remove('is-open');
                    }
                    if (isVisible) {
                        visibleCount += 1;
                    }
                });

                if (emptySearchRow) {
                    emptySearchRow.hidden = visibleCount !== 0 || query === '';
                }

                if (resultsCount) {
                    resultsCount.textContent = visibleCount + ' fichier(s) cree(s)';
                }
            }

            rows.forEach(function (row) {
                row.addEventListener('click', function (event) {
                    if (window.innerWidth > 860) {
                        return;
                    }

                    if (event.target.closest('a')) {
                        return;
                    }

                    const detailsRow = row.nextElementSibling;
                    if (!detailsRow || !detailsRow.hasAttribute('data-details-row')) {
                        return;
                    }

                    const willOpen = detailsRow.hidden;

                    rows.forEach(function (currentRow) {
                        const currentDetails = currentRow.nextElementSibling;
                        currentRow.classList.remove('is-open');
                        if (currentDetails && currentDetails.hasAttribute('data-details-row')) {
                            currentDetails.hidden = true;
                            currentDetails.classList.remove('is-open');
                        }
                    });

                    row.classList.toggle('is-open', willOpen);
                    detailsRow.hidden = !willOpen;
                    detailsRow.classList.toggle('is-open', willOpen);
                });
            });

            input.addEventListener('input', applyFilter);
            applyFilter();
        });
    </script>
@endsection
