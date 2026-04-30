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
            width: min(1320px, 100%);
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
            margin-bottom: 28px;
        }

        .page-head h1 {
            margin: 0;
            padding-left: 16px;
            font-size: clamp(30px, 5vw, 44px);
            border-left: 6px solid #14b8a6;
        }

        .page-head p {
            margin: 8px 0 0;
            color: #64748b;
        }

        .status-box {
            margin-bottom: 18px;
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(20, 184, 166, 0.12);
            border: 1px solid rgba(45, 212, 191, 0.3);
            color: #115e59;
        }

        .section-title {
            margin: 30px 0 16px;
            padding-left: 12px;
            font-size: 24px;
            border-left: 4px solid #14b8a6;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 24px;
            border: 1px solid #dbe4ee;
            background: #f8fafc;
        }

        table {
            width: 100%;
            min-width: 960px;
            border-collapse: collapse;
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
            color: #334155;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .company {
            font-weight: 700;
            color: #0f172a;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn,
        .link-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 104px;
            padding: 10px 14px;
            border-radius: 14px;
            border: none;
            text-decoration: none;
            font-weight: 700;
            cursor: pointer;
        }

        .link-btn {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #0f172a;
        }

        .btn-approve {
            background: linear-gradient(135deg, #0f766e, #14b8a6);
            color: #ffffff;
            box-shadow: 0 14px 28px rgba(20, 184, 166, 0.2);
        }

        .btn-reject {
            background: rgba(239, 68, 68, 0.12);
            color: #b91c1c;
            border: 1px solid rgba(239, 68, 68, 0.22);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge.pending {
            background: rgba(245, 158, 11, 0.16);
            color: #92400e;
        }

        .badge.rejected {
            background: rgba(239, 68, 68, 0.14);
            color: #991b1b;
        }

        .empty-state {
            padding: 26px;
            text-align: center;
            color: #64748b;
        }
    </style>

    <div class="page-shell">
        <div class="page-card">
            <div class="page-head">
                <div>
                    <h1>Demandes Fiche Client</h1>
                    <p>Validation admin des dossiers prospect apres ajout de piece jointe.</p>
                </div>
            </div>

            @if (session('status'))
                <div class="status-box">{{ session('status') }}</div>
            @endif

            <h2 class="section-title">En attente</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nom entreprise</th>
                            <th>Cree par</th>
                            <th>Commercial</th>
                            <th>Montant</th>
                            <th>Date signature</th>
                            <th>Piece jointe ajoutee par</th>
                            <th>Date demande</th>
                            <th>Etat</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingRequests as $fiche)
                            <tr>
                                <td class="company">{{ $fiche->nom_entreprise }}</td>
                                <td>{{ $fiche->user?->name ?: '-' }}</td>
                                <td>{{ $fiche->contractUser?->name ?: '-' }}</td>
                                <td>{{ $fiche->contract_amount !== null ? number_format((float) $fiche->contract_amount, 0, '.', ',') . ' DZD' : '-' }}</td>
                                <td>{{ $fiche->contract_signed_at?->format('Y-m-d') ?: '-' }}</td>
                                <td>{{ $fiche->pieceJointeUploader?->name ?: '-' }}</td>
                                <td>{{ $fiche->piece_jointe_uploaded_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                <td><span class="badge pending">En attente</span></td>
                                <td>
                                    <div class="actions">
                                        <a class="link-btn" href="{{ route('fiche-propose.show', $fiche) }}">Details</a>
                                        <a class="link-btn" href="{{ route('fiche-propose.documents.download', [$fiche, 'contract']) }}">Voir piece</a>
                                        <form action="{{ route('admin.client-conversion-requests.approve', $fiche) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-approve" type="submit">Accepter</button>
                                        </form>
                                        <form action="{{ route('admin.client-conversion-requests.reject', $fiche) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-reject" type="submit">Refuser</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="empty-state">Aucune demande en attente.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <h2 class="section-title">Refuse</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nom entreprise</th>
                            <th>Cree par</th>
                            <th>Commercial</th>
                            <th>Montant</th>
                            <th>Date signature</th>
                            <th>Piece jointe ajoutee par</th>
                            <th>Refuse par</th>
                            <th>Date refus</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rejectedRequests as $fiche)
                            <tr>
                                <td class="company">{{ $fiche->nom_entreprise }}</td>
                                <td>{{ $fiche->user?->name ?: '-' }}</td>
                                <td>{{ $fiche->contractUser?->name ?: '-' }}</td>
                                <td>{{ $fiche->contract_amount !== null ? number_format((float) $fiche->contract_amount, 0, '.', ',') . ' DZD' : '-' }}</td>
                                <td>{{ $fiche->contract_signed_at?->format('Y-m-d') ?: '-' }}</td>
                                <td>{{ $fiche->pieceJointeUploader?->name ?: '-' }}</td>
                                <td>{{ $fiche->conversionReviewer?->name ?: '-' }}</td>
                                <td>{{ $fiche->conversion_reviewed_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                <td>
                                    <div class="actions">
                                        <span class="badge rejected">Refuse</span>
                                        <a class="link-btn" href="{{ route('fiche-propose.show', $fiche) }}">Details</a>
                                        @if ($fiche->piece_jointe_path)
                                            <a class="link-btn" href="{{ route('fiche-propose.documents.download', [$fiche, 'contract']) }}">Voir piece</a>
                                        @endif
                                        <form action="{{ route('admin.client-conversion-requests.approve', $fiche) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-approve" type="submit">Accepter</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="empty-state">Aucun dossier refuse.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
