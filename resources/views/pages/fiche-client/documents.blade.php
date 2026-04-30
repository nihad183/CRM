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

        .company-name,
        .status-box,
        .error-box {
            margin-bottom: 20px;
        }

        .company-name {
            color: #475569;
            font-size: 17px;
        }

        .status-box,
        .error-box {
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

        .field-group {
            margin-bottom: 26px;
            padding: 22px;
            border-radius: 24px;
            border: 1px solid #dbe4ee;
            background: #f8fafc;
        }

        .field-group-title {
            margin: 0 0 18px;
            font-size: 20px;
            color: #0f172a;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
        }

        .field:last-child {
            margin-bottom: 0;
        }

        label {
            font-size: 14px;
            color: #334155;
            font-weight: 700;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            outline: none;
        }

        .current-file {
            margin-top: 10px;
            font-size: 14px;
            color: #1e3a8a;
        }

        .current-file a {
            color: inherit;
            font-weight: 700;
        }

        .helper-text {
            font-size: 13px;
            color: #64748b;
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
    </style>

    <div class="page-shell">
        <div class="page-card">
            <div class="top-row">
                <h1>Documents Fiche Client</h1>
                <a class="back-link" href="{{ route('fiche-client') }}">Retour</a>
            </div>

            <p class="company-name">{{ $fiche->nom_entreprise }}</p>

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

            <form action="{{ route('fiche-client.documents.update', $fiche) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="field-group">
                    <h2 class="field-group-title">N RC</h2>
                    <div class="field">
                        <label for="n_rc">Numero RC</label>
                        <input id="n_rc" type="text" name="n_rc" value="{{ old('n_rc', $fiche->n_rc) }}" placeholder="Entrez le numero RC" required>
                    </div>
                    <div class="field">
                        <label for="n_rc_piece">Piece jointe RC</label>
                        <input id="n_rc_piece" type="file" name="n_rc_piece" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                        <div class="helper-text">Formats acceptes : PDF, DOC, DOCX, JPG, JPEG, PNG. Taille maximale : 10 Mo.</div>
                        @if ($fiche->n_rc_piece_path)
                            <div class="current-file">
                                Fichier actuel :
                                <a href="{{ route('fiche-propose.documents.download', [$fiche, 'n_rc']) }}">
                                    {{ $fiche->n_rc_piece_original_name ?: 'Voir document RC' }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="field-group">
                    <h2 class="field-group-title">NIF</h2>
                    <div class="field">
                        <label for="nif">Numero NIF</label>
                        <input id="nif" type="text" name="nif" value="{{ old('nif', $fiche->nif) }}" placeholder="Entrez le numero NIF" required>
                    </div>
                    <div class="field">
                        <label for="nif_piece">Piece jointe NIF</label>
                        <input id="nif_piece" type="file" name="nif_piece" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                        <div class="helper-text">Formats acceptes : PDF, DOC, DOCX, JPG, JPEG, PNG. Taille maximale : 10 Mo.</div>
                        @if ($fiche->nif_piece_path)
                            <div class="current-file">
                                Fichier actuel :
                                <a href="{{ route('fiche-propose.documents.download', [$fiche, 'nif']) }}">
                                    {{ $fiche->nif_piece_original_name ?: 'Voir document NIF' }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="field-group">
                    <h2 class="field-group-title">NIS</h2>
                    <div class="field">
                        <label for="nis">Numero NIS</label>
                        <input id="nis" type="text" name="nis" value="{{ old('nis', $fiche->nis) }}" placeholder="Entrez le numero NIS" required>
                    </div>
                    <div class="field">
                        <label for="nis_piece">Piece jointe NIS</label>
                        <input id="nis_piece" type="file" name="nis_piece" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                        <div class="helper-text">Formats acceptes : PDF, DOC, DOCX, JPG, JPEG, PNG. Taille maximale : 10 Mo.</div>
                        @if ($fiche->nis_piece_path)
                            <div class="current-file">
                                Fichier actuel :
                                <a href="{{ route('fiche-propose.documents.download', [$fiche, 'nis']) }}">
                                    {{ $fiche->nis_piece_original_name ?: 'Voir document NIS' }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="save-row">
                    <button class="save-btn" type="submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection
