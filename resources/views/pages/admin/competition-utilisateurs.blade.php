@extends('layouts.app')

@section('content')
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Tahoma, Arial, sans-serif;
           background:
                linear-gradient(rgba(17, 24, 39, 0.94), rgba(30, 41, 59, 0.88)),
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
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.97), rgba(248, 250, 252, 0.95));
            border: 1px solid rgba(148, 163, 184, 0.22);
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.42);
            color: #111827;
            backdrop-filter: blur(10px);
        }

        .page-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 18px;
            margin-bottom: 24px;
        }

        .filter-form {
            display: flex;
            align-items: end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-field label {
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .filter-field select {
            min-width: 160px;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            outline: none;
        }

        .filter-btn {
            padding: 12px 18px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #0f766e, #14b8a6);
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 16px 30px rgba(20, 184, 166, 0.18);
        }

        .page-head h1 {
            margin: 0;
            padding-left: 16px;
            font-size: clamp(30px, 5vw, 44px);
            border-left: 6px solid #f59e0b;
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
            background: #64748b;
        }

        .podium-card.second {
            background:#313b49;
        }

        .podium-card.third {
            background: #243041;
        }

        .podium-rank {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.18);
            font-weight: 700;
        }

      
        .podium-name {
            margin: 18px 0 8px;
            font-size: 24px;
            font-weight: 700;
            color: #f8fafc
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
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.84), rgba(15, 23, 42, 0.94));
            overflow: hidden;
        }

        .leaderboard-head {
            display: grid;
            grid-template-columns: 84px minmax(220px, 1.4fr) 150px 180px 180px;
            gap: 14px;
            padding: 18px 22px;
            background: rgba(30, 41, 59, 0.82);
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
            border-color: rgba(245, 158, 11, 0.28);
            box-shadow: 0 18px 30px rgba(2, 6, 23, 0.28);
        }

        .player-row.first-place {
            background: linear-gradient(90deg, rgba(245, 158, 11, 0.12), rgba(15, 23, 42, 0.75));
            border-color: rgba(245, 158, 11, 0.24);
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
            background: rgba(245, 158, 11, 0.18);
            border-color: rgba(245, 158, 11, 0.28);
            color: #fcd34d;
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
            background: rgba(59, 130, 246, 0.12);
            border: 1px solid rgba(59, 130, 246, 0.22);
            color: #93c5fd;
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

        .metric-top.amount-metric {
            align-items: baseline;
        }

        .metric-top.amount-metric strong {
            font-size: 16px;
            line-height: 1.2;
            text-align: right;
            word-break: break-word;
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
            background: linear-gradient(90deg, #60a5fa, #2563eb);
        }

        .progress-bar.client {
            background: linear-gradient(90deg, #f59e0b, #f97316);
        }

        .rule-note {
            margin: 0 0 18px;
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 159, 11, 0.199);
            color: #220f05;
            font-size: 12px;
        }

        .empty-state {
            padding: 28px;
            text-align: center;
            color: #94a3b8;
        }

        @media (max-width: 768px) {
            .page-shell {
                padding: 96px 14px 28px;
            }

            .page-card {
                padding: 22px 14px;
                border-radius: 24px;
            }

            .page-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .filter-form,
            .filter-field,
            .filter-field select,
            .filter-btn {
                width: 100%;
            }

            .podium {
                grid-template-columns: 1fr;
            }

            .podium-card {
                padding: 20px;
                border-radius: 22px;
            }

            .podium-name {
                font-size: 21px;
            }

            .podium-score {
                gap: 8px;
            }

            .podium-score span {
                width: 100%;
                justify-content: center;
                text-align: center;
            }
        }

        @media (max-width: 980px) {
            .table-wrap {
                overflow: visible;
            }

            .leaderboard-head {
                display: none;
            }

            .leaderboard-list {
                padding: 12px;
            }

            .player-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                grid-template-areas:
                    "rank role"
                    "name name"
                    "contracts contracts"
                    "amount amount";
                gap: 16px;
                align-items: start;
                min-width: 0;
                padding: 18px 16px;
            }

            .player-row > div:nth-child(1) {
                grid-area: rank;
            }

            .player-row > div:nth-child(2) {
                grid-area: name;
            }

            .player-row > div:nth-child(3) {
                grid-area: role;
                display: flex;
                justify-content: flex-end;
            }

            .player-row > div:nth-child(4) {
                grid-area: contracts;
            }

            .player-row > div:nth-child(5) {
                grid-area: amount;
            }

            .rank-badge {
                min-width: 46px;
                height: 46px;
            }

            .player-name {
                white-space: normal;
            }

            .metric {
                padding: 14px;
                border-radius: 18px;
                background: rgba(30, 41, 59, 0.32);
                border: 1px solid rgba(148, 163, 184, 0.1);
            }

            .metric-top strong {
                font-size: 20px;
            }

            .metric-top.amount-metric strong {
                font-size: 15px;
            }
        }

        @media (max-width: 560px) {
            .page-head h1 {
                font-size: 28px;
            }

            .player-row {
                grid-template-columns: 1fr;
                grid-template-areas:
                    "rank"
                    "role"
                    "name"
                    "contracts"
                    "amount";
            }

            .player-row > div:nth-child(3) {
                justify-content: flex-start;
            }

            .metric-top {
                align-items: flex-start;
                flex-direction: column;
            }

            .metric-top.amount-metric {
                align-items: flex-start;
            }

            .metric-top strong,
            .metric-top.amount-metric strong {
                text-align: left;
            }
        }
    </style>

    <div class="page-shell">
        <div class="page-card">
            <div class="page-head">
                <div>
                    <h1>Ranking Classement</h1>
                </div>
                <form class="filter-form" method="GET" action="{{ route('admin.competition-utilisateurs') }}">
                    <div class="filter-field">
                        <label for="year">Filtre annee</label>
                        <select id="year" name="year">
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}" {{ (int) $selectedYear === (int) $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button class="filter-btn" type="submit">Filtrer</button>
                </form>
            </div>

            <p class="rule-note">
                Le classement annuel de {{ $currentYearLabel }} est base sur le montant cumule des contrats signes. En cas d'egalite, le nombre de contrats signes departage les commerciaux.
            </p>

            @php
                $topUsers = $users->take(3)->values();
                $maxContracts = max((int) $users->max('yearly_signed_contracts_count'), 1);
                $maxTotal = max((float) $users->max('yearly_contract_total'), 1);
            @endphp

            @if ($topUsers->isNotEmpty())
                <div class="podium">
                    @foreach ($topUsers as $topUser)
                        <div class="podium-card {{ $loop->first ? 'first' : ($loop->iteration === 2 ? 'second' : 'third') }}">
                            <span class="podium-rank">#{{ $topUser->rank }}</span>
                            <div class="podium-name">{{ $topUser->name }}</div>
                            <div class="podium-score">
                                <span>{{ number_format((float) $topUser->yearly_contract_total, 0, '.', ',') }} DZD</span>
                                <span>{{ $topUser->yearly_signed_contracts_count }} contrat{{ $topUser->yearly_signed_contracts_count > 1 ? 's' : '' }}</span>
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
                        <div>Contrats signes</div>
                        <div>Montant cumule</div>
                    </div>

                    <div class="leaderboard-list">
                        @forelse ($users as $user)
                            @php
                                $contractsWidth = min(($user->yearly_signed_contracts_count / $maxContracts) * 100, 100);
                                $totalWidth = min((((float) $user->yearly_contract_total) / $maxTotal) * 100, 100);
                            @endphp
                            <div class="player-row {{ $user->rank === 1 ? 'first-place' : '' }}">
                                <div>
                                    <span class="rank-badge {{ $user->rank === 1 ? 'gold' : '' }}">#{{ $user->rank }}</span>
                                </div>

                                <div class="name-cell">
                                    <div class="player-name">{{ $user->name }}</div>
                                    <div class="player-subtitle">Classement annuel des commerciaux</div>
                                </div>

                                <div>
                                    <span class="role-badge {{ $user->role === 'admin' ? 'admin' : 'employee' }}">
                                        {{ $user->role === 'admin' ? 'Admin' : 'Employe' }}
                                    </span>
                                </div>

                                <div class="metric">
                                    <div class="metric-top">
                                        <span>Contrats</span>
                                        <strong>{{ $user->yearly_signed_contracts_count }}</strong>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-bar prospect" style="width: {{ $contractsWidth }}%"></div>
                                    </div>
                                </div>

                                <div class="metric">
                                    <div class="metric-top amount-metric">
                                        <span>Montant</span>
                                        <strong>{{ number_format((float) $user->yearly_contract_total, 0, '.', ',') }} DZD</strong>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-bar client" style="width: {{ $totalWidth }}%"></div>
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
