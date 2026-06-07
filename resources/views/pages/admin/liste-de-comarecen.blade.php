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
            width: min(1220px, 100%);
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
            margin-bottom: 24px;
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

        .stats-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 14px;
            margin-bottom: 24px;
        }

        .stat-card {
            padding: 18px 20px;
            border-radius: 22px;
            background: linear-gradient(135deg, #f8fafc, #eef5f9);
            border: 1px solid #dbe4ee;
        }

        .stat-card strong {
            display: block;
            font-size: 28px;
            color: #0f172a;
        }

        .stat-card span {
            color: #64748b;
            font-size: 14px;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 24px;
            border: 1px solid #dbe4ee;
            background: #f8fafc;
        }

        .status-box {
            margin-bottom: 18px;
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(20, 184, 166, 0.12);
            border: 1px solid rgba(45, 212, 191, 0.3);
            color: #115e59;
        }

        table {
            width: 100%;
            min-width: 860px;
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
            color: #334155;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background: rgba(20, 184, 166, 0.05);
        }

        .name-cell {
            font-weight: 700;
            color: #0f172a;
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

        .badge.admin {
            background: rgba(15, 118, 110, 0.12);
            color: #0f766e;
        }

        .badge.employee {
            background: rgba(59, 130, 246, 0.12);
            color: #1d4ed8;
        }

        .company-form {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .company-select {
            min-width: 170px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            outline: none;
        }

        .save-btn {
            padding: 10px 14px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #0f766e, #14b8a6);
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
        }

        .muted-text {
            color: #64748b;
            font-size: 14px;
        }

        .empty-state {
            padding: 28px;
            text-align: center;
            color: #64748b;
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
        }
    </style>

    <div class="page-shell">
        <div class="page-card">
            <div class="page-head">
                <div>
                    <h1>Liste des commrciaux</h1>
                    <p>
                        @if ($canManageCompanies)
                            Cette page vous permet d attribuer la societe de chaque employe.
                        @else
                            Cette page affiche tous les utilisateurs et la societe a laquelle chacun appartient.
                        @endif
                    </p>
                </div>
            </div>

            @if (session('status'))
                <div class="status-box">{{ session('status') }}</div>
            @endif

            <div class="stats-strip">
                <div class="stat-card">
                    <strong>{{ $users->where('role', 'employee')->count() }}</strong>
                    <span>Total commerciaux</span>
                </div>
                <div class="stat-card">
                    <strong>{{ $users->filter(fn ($user) => strtolower((string) $user->role) === 'dg')->count() }}</strong>
                    <span>DG</span>
                </div>
                <div class="stat-card">
                    <strong>{{ $users->filter(fn ($user) => $user->isCompliance())->count() }}</strong>
                    <span>Conformite</span>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prenom</th>
                            <th>Email</th>
                            <th>Telephone</th>
                            <th>Societe</th>
                            <th>Role</th>
                            @if ($canManageCompanies)
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    @php
                        $dgs = $users->filter(fn ($user) => strtolower((string) $user->role) === 'dg')->values();
                        $complianceUsers = $users->filter(fn ($user) => $user->isCompliance())->values();
                        $employees = $users->filter(fn ($user) => strtolower((string) $user->role) === 'employee')->values();
                        $visibleUsersCount = $dgs->count() + $complianceUsers->count() + $employees->count();
                    @endphp

                    <tbody>
                        @if ($visibleUsersCount > 0)
                            @foreach ($dgs as $user)
                                @php
                                    $nameParts = preg_split('/\s+/', trim((string) $user->name)) ?: [];
                                    $nom = $nameParts[0] ?? '-';
                                    $prenom = trim(implode(' ', array_slice($nameParts, 1))) ?: '-';
                                @endphp
                                <tr>
                                    <td class="name-cell">{{ $nom }}</td>
                                    <td>{{ $prenom }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?: '-' }}</td>
                                    <td>{{ $user->companyLabel() }}</td>
                                    <td>
                                        <span class="badge employee">
                                            {{ $user->roleLabel() }}
                                        </span>
                                    </td>
                                    @if ($canManageCompanies)
                                        <td>
                                            <span class="muted-text">Non modifiable</span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach

                            @foreach ($complianceUsers as $user)
                                @php
                                    $nameParts = preg_split('/\s+/', trim((string) $user->name)) ?: [];
                                    $nom = $nameParts[0] ?? '-';
                                    $prenom = trim(implode(' ', array_slice($nameParts, 1))) ?: '-';
                                @endphp
                                <tr>
                                    <td class="name-cell">{{ $nom }}</td>
                                    <td>{{ $prenom }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?: '-' }}</td>
                                    <td>{{ $user->companyLabel() }}</td>
                                    <td>
                                        <span class="badge admin">
                                            {{ $user->roleLabel() }}
                                        </span>
                                    </td>
                                    @if ($canManageCompanies)
                                        <td>
                                            <span class="muted-text">Non modifiable</span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach

                            @foreach ($employees as $user)
                                @php
                                    $nameParts = preg_split('/\s+/', trim((string) $user->name)) ?: [];
                                    $nom = $nameParts[0] ?? '-';
                                    $prenom = trim(implode(' ', array_slice($nameParts, 1))) ?: '-';
                                @endphp
                                <tr>
                                    <td class="name-cell">{{ $nom }}</td>
                                    <td>{{ $prenom }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?: '-' }}</td>
                                    <td>{{ $user->companyLabel() }}</td>
                                    <td>
                                        <span class="badge employee">
                                            {{ $user->roleLabel() }}
                                        </span>
                                    </td>
                                    @if ($canManageCompanies)
                                        <td>
                                            @if ($user->isEmployee())
                                                <form class="company-form" action="{{ route('admin.users.company.update', $user) }}" method="POST">
                                                    @csrf
                                                    <select class="company-select" name="company">
                                                        <option value="invest_market" {{ $user->normalizedCompany() === 'invest_market' ? 'selected' : '' }}>Invest Market</option>
                                                        <option value="rmgc" {{ $user->normalizedCompany() === 'rmgc' ? 'selected' : '' }}>RMGC</option>
                                                    </select>
                                                    <button class="save-btn" type="submit">Enregistrer</button>
                                                </form>
                                            @else
                                                <span class="muted-text">Non modifiable</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="{{ $canManageCompanies ? 7 : 6 }}" class="empty-state">Aucun utilisateur trouve.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
