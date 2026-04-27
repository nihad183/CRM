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
                </div>
            </div>

            <div class="stats-strip">
                <div class="stat-card">
                    <strong>{{ $users->count() }}</strong>
                    <span>Total utilisateurs</span>
                </div>
                <div class="stat-card">
                    <strong>{{ $users->where('role', 'admin')->count() }}</strong>
                    <span>Administrateurs</span>
                </div>
                <div class="stat-card">
                    <strong>{{ $users->where('role', 'employee')->count() }}</strong>
                    <span>Employes</span>
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
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
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
                                <td>
                                    <span class="badge {{ $user->role === 'admin' ? 'admin' : 'employee' }}">
                                        {{ $user->role === 'admin' ? 'Admin' : 'Employe' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">Aucun utilisateur trouve.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
