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
            width: min(980px, 100%);
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
            gap: 16px;
            margin-bottom: 24px;
        }

        h1 {
            margin: 0;
            padding-left: 16px;
            font-size: clamp(28px, 5vw, 42px);
            border-left: 6px solid #14b8a6;
        }

        .back-link {
            color: #0f766e;
            text-decoration: none;
            font-weight: 700;
        }

        .company-name {
            margin: 0 0 24px;
            color: #475569;
            font-size: 17px;
        }

        .timeline {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .timeline-item {
            position: relative;
            padding: 22px 22px 22px 28px;
            border-radius: 24px;
            border: 1px solid #dbe4ee;
            background: #f8fafc;
        }

        .timeline-item::before {
            content: "";
            position: absolute;
            top: 24px;
            left: 12px;
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #14b8a6;
            box-shadow: 0 0 0 6px rgba(20, 184, 166, 0.12);
        }

        .timeline-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 8px;
        }

        .timeline-title {
            margin: 0;
            font-size: 18px;
            color: #0f172a;
        }

        .timeline-date {
            color: #64748b;
            font-size: 14px;
            white-space: nowrap;
        }

        .timeline-user {
            margin-bottom: 8px;
            color: #0f766e;
            font-weight: 700;
        }

        .timeline-description {
            color: #334155;
            line-height: 1.7;
        }

        .empty-state {
            padding: 26px;
            border-radius: 22px;
            background: #f8fafc;
            border: 1px solid #dbe4ee;
            color: #64748b;
            text-align: center;
        }
    </style>

    <div class="page-shell">
        <div class="page-card">
            <div class="top-row">
                <h1>Historique</h1>
                <a class="back-link" href="{{ auth()->user()->isAdmin() && in_array($fiche->client_conversion_status, ['pending', 'rejected'], true) ? route('admin.client-conversion-requests') : ($fiche->is_fiche_client ? route('fiche-client') : route('fiche-propose')) }}">Retour</a>
            </div>

            <p class="company-name">{{ $fiche->nom_entreprise }}</p>

            @if ($events->isEmpty())
                <div class="empty-state">Aucun historique disponible.</div>
            @else
                <div class="timeline">
                    @foreach ($events as $event)
                        <div class="timeline-item">
                            <div class="timeline-head">
                                <h2 class="timeline-title">{{ $event['title'] }}</h2>
                                <div class="timeline-date">{{ optional($event['created_at'])->format('Y-m-d H:i') }}</div>
                            </div>
                            <div class="timeline-user">{{ $event['user_name'] }}</div>
                            <div class="timeline-description">{{ $event['description'] }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
