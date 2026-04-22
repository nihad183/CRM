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
            width: min(1240px, 100%);
            margin: 0 auto;
            padding: 34px;
            border-radius: 34px;
            background: #ffffff;
            border: 1px solid rgba(148, 163, 184, 0.2);
            box-shadow: 0 30px 80px rgba(2, 6, 23, 0.5);
            color: #0a0a0a;
            backdrop-filter: blur(10px);
        }

        .page-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 18px;
            margin-bottom: 24px;
        }

        .page-head h1 {
            margin: 0;
            padding-left: 16px;
            font-size: clamp(30px, 5vw, 44px);
            border-left: 6px solid #14b8a6;
        }

        .podium {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 26px;
        }

        .podium-card {
            position: relative;
            padding: 24px;
            border-radius: 28px;
            color: #fff;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 24px 50px rgba(2, 6, 23, 0.35);
        }

        .podium-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.28), transparent 34%),
                linear-gradient(180deg, transparent, rgba(15, 23, 42, 0.18));
            pointer-events: none;
        }

        .podium-card.first {
            background: linear-gradient(135deg, #0f766e, #14b8a6);
        }

        .podium-card.second {
            background: linear-gradient(135deg, #115e59, #0f766e);
        }

        .podium-card.third {
            background: linear-gradient(135deg, #134e4a, #0f766e);
        }

        .podium-rank {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 999px;
            background: rgba(21, 5, 238, 0.18);
            font-weight: 700;
        }

      
        .podium-name {
            margin: 18px 0 8px;
            font-size: 24px;
            font-weight: 700;
            color: #0a0a0a
        }

        

        .podium-score {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        .podium-score span {
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.16);
            font-size: 13px;
            font-weight: 700;
        }

        .leaderboard-shell {
            border-radius: 28px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.82), rgba(15, 23, 42, 0.92));
            overflow: hidden;
        }

        .leaderboard-head {
            display: grid;
            grid-template-columns: 84px minmax(220px, 1.4fr) 150px 180px 180px;
            gap: 14px;
            padding: 18px 22px;
            background: rgba(30, 41, 59, 0.8);
            color: #94a3b8;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .leaderboard-list {
            padding: 10px;
        }

        .player-row {
            display: grid;
            grid-template-columns: 84px minmax(220px, 1.4fr) 150px 180px 180px;
            gap: 14px;
            align-items: center;
            padding: 16px 12px;
            margin-bottom: 10px;
            border-radius: 22px;
            background: rgba(15, 23, 42, 0.68);
            border: 1px solid rgba(148, 163, 184, 0.12);
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .player-row:hover {
            transform: translateY(-2px);
            border-color: rgba(250, 204, 21, 0.28);
            box-shadow: 0 18px 30px rgba(2, 6, 23, 0.28);
        }

        .player-row.first-place {
            background: linear-gradient(90deg, rgba(250, 204, 21, 0.12), rgba(15, 23, 42, 0.75));
            border-color: rgba(250, 204, 21, 0.22);
        }

        .table-wrap {
            overflow-x: auto;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 52px;
            height: 52px;
            border-radius: 999px;
            font-weight: 700;
            background: rgba(30, 41, 59, 0.95);
            border: 1px solid rgba(148, 163, 184, 0.15);
            color: #f8fafc;
        }

        .rank-badge.gold {
            background: rgba(250, 204, 21, 0.18);
            border-color: rgba(250, 204, 21, 0.26);
            color: #fde68a;
        }

        .name-cell {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0;
        }

        .player-name {
            font-weight: 700;
            color: #f8fafc;
            font-size: 18px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .player-subtitle {
            color: #94a3b8;
            font-size: 13px;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: fit-content;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .role-badge.admin {
            background: rgba(249, 115, 22, 0.14);
            border: 1px solid rgba(249, 115, 22, 0.24);
            color: #fdba74;
        }

        .role-badge.employee {
            background: rgba(56, 189, 248, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.22);
            color: #7dd3fc;
        }

        .metric {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .metric-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            color: #cbd5e1;
            font-size: 13px;
        }

        .metric-top strong {
            font-size: 22px;
            color: #f8fafc;
        }

        .progress-track {
            width: 100%;
            height: 10px;
            border-radius: 999px;
            background: rgba(51, 65, 85, 0.9);
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            border-radius: inherit;
        }

        .progress-bar.prospect {
            background: linear-gradient(90deg, #38bdf8, #0ea5e9);
        }

        .progress-bar.client {
            background: linear-gradient(90deg, #facc15, #f97316);
        }

        .rule-note {
            margin: 0 0 18px;
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(250, 204, 21, 0.08);
            border: 1px solid rgba(250, 204, 21, 0.18);
            color: #313130;
        }

        .empty-state {
            padding: 28px;
            text-align: center;
            color: #94a3b8;
        }

        @media (max-width: 768px) {
            .page-card {
                padding: 24px 18px;
                border-radius: 24px;
            }

            .page-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .hero-strip {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 980px) {
            .leaderboard-head,
            .player-row {
                min-width: 860px;
            }
        }
    </style>

    <div class="page-shell">
        <div class="page-card">
            <div class="page-head">
                <div>
                    <h1>Competition </h1>
                </div>
            </div>

            <p class="rule-note">
                Le classement est base d'abord sur le nombre de fiche client, puis sur le nombre de fiche prospect en cas d'egalite.
            </p>

            @php
                $topUsers = $users->take(3)->values();
                $maxProspects = max((int) $users->max('fiche_prospect_count'), 1);
                $maxClients = max((int) $users->max('fiche_client_count'), 1);
            @endphp

            @if ($topUsers->isNotEmpty())
                <div class="podium">
                    @foreach ($topUsers as $topUser)
                        <div class="podium-card {{ $loop->first ? 'first' : ($loop->iteration === 2 ? 'second' : 'third') }}">
                            <span class="podium-rank">#{{ $topUser->rank }}</span>
                            <div class="podium-name">{{ $topUser->name }}</div>
                            <div class="podium-score">
                                <span>{{ $topUser->fiche_client_count }} fiche client</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="table-wrap">
                <div class="leaderboard-shell">
                    <div class="leaderboard-head">
                        <div>Rang</div>
                        <div>Utilisateur</div>
                        <div>Role</div>
                        <div>Fiche prospect</div>
                        <div>Fiche client</div>
                    </div>

                    <div class="leaderboard-list">
                        @forelse ($users as $user)
                            @php
                                $prospectWidth = min(($user->fiche_prospect_count / $maxProspects) * 100, 100);
                                $clientWidth = min(($user->fiche_client_count / $maxClients) * 100, 100);
                            @endphp
                            <div class="player-row {{ $user->rank === 1 ? 'first-place' : '' }}">
                                <div>
                                    <span class="rank-badge {{ $user->rank === 1 ? 'gold' : '' }}">#{{ $user->rank }}</span>
                                </div>

                                <div class="name-cell">
                                    <div class="player-name">{{ $user->name }}</div>
                                    <div class="player-subtitle">Visible dans le classement general</div>
                                </div>

                                <div>
                                    <span class="role-badge {{ $user->role === 'admin' ? 'admin' : 'employee' }}">
                                        {{ $user->role === 'admin' ? 'Admin' : 'Employe' }}
                                    </span>
                                </div>

                                <div class="metric">
                                    <div class="metric-top">
                                        <span>Prospects</span>
                                        <strong>{{ $user->fiche_prospect_count }}</strong>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-bar prospect" style="width: {{ $prospectWidth }}%"></div>
                                    </div>
                                </div>

                                <div class="metric">
                                    <div class="metric-top">
                                        <span>Clients</span>
                                        <strong>{{ $user->fiche_client_count }}</strong>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-bar client" style="width: {{ $clientWidth }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">Aucune donnee disponible pour le classement.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
