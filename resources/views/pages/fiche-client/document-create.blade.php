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
            margin-bottom: 28px;
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

        .status-box,
        .error-box,
        .notice-box {
            margin-bottom: 20px;
            padding: 16px 18px;
            border-radius: 18px;
        }

        .status-box {
            background: rgba(20, 184, 166, 0.12);
            border: 1px solid rgba(45, 212, 191, 0.3);
            color: #115e59;
        }

        .error-box {
            background: rgba(239, 68, 68, 0.16);
            border: 1px solid rgba(248, 113, 113, 0.4);
            color: #991b1b;
        }

        .notice-box {
            background: rgba(15, 118, 110, 0.1);
            border: 1px solid rgba(15, 118, 110, 0.18);
            color: #134e4a;
            line-height: 1.7;
        }

        .company-grid {
            display: grid;
            gap: 18px;
            margin-bottom: 24px;
        }

        .info-card {
            padding: 20px 22px;
            border-radius: 22px;
            border: 1px solid #dbe4ee;
            background: #f8fafc;
        }

        .info-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #475569;
        }

        .info-value {
            color: #0f172a;
            line-height: 1.8;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }

        label {
            font-size: 14px;
            color: #334155;
            font-weight: 700;
        }

        input[type="file"] {
            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            outline: none;
        }

        .helper-text {
            font-size: 13px;
            color: #64748b;
        }

        .current-file {
            margin-bottom: 22px;
            padding: 16px 18px;
            border-radius: 18px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e3a8a;
        }

        .current-file a {
            color: inherit;
            font-weight: 700;
        }

        .save-row {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
        }

        .save-btn {
            padding: 14px 24px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #0f766e, #14b8a6);
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 16px 30px rgba(20, 184, 166, 0.22);
        }

        @media (max-width: 720px) {
            .page-card {
                padding: 22px;
            }

            .top-row {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    <div class="page-shell">
        <div class="page-card">
            <div class="top-row">
                <h1>Fiche Client</h1>
                <a class="back-link" href="{{ $fiche->is_fiche_client ? route('fiche-client') : route('fiche-propose') }}">Retour</a>
            </div>

            @if (session('status'))
                <div class="status-box">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="error-box">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="company-grid">
                <div class="info-card">
                    <span class="info-label">Nom entreprise</span>
                    <div class="info-value">{{ $fiche->nom_entreprise }}</div>
                </div>

                <div class="info-card">
                    <span class="info-label">Secteur d'activite</span>
                    <div class="info-value">{{ $fiche->secteur_activite }}</div>
                </div>

                <div class="info-card">
                    <span class="info-label">Adresse</span>
                    <div class="info-value">{{ $fiche->adresse }}</div>
                </div>
            </div>

            <div class="notice-box">
                Le changement de Prospect vers fiche client ne se fait qu'apres le televersement obligatoire d'une copie du contrat.
            </div>

            @if ($fiche->piece_jointe_path)
                <div class="current-file">
                    Fichier actuel :
                    <a href="{{ asset('storage/' . $fiche->piece_jointe_path) }}" target="_blank" rel="noopener">
                        {{ $fiche->piece_jointe_original_name ?: 'Voir la piece jointe' }}
                    </a>
                </div>
            @endif

            <form action="{{ route('fiche-propose.fiche-client.store', $fiche) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="field">
                    <label for="piece_jointe">Piece jointe</label>
                    <input id="piece_jointe" type="file" name="piece_jointe" accept=".pdf,.doc,.docx" required>
                    <div class="helper-text">Formats acceptes : PDF, DOC, DOCX</div>
                </div>

                <div class="save-row">
                    <button class="save-btn" type="submit">Transformer en fiche client</button>
                </div>
            </form>
        </div>
    </div>
@endsection
