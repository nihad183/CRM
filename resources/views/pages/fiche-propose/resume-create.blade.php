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

        .company-name {
            margin: 0 0 24px;
            color: #475569;
            font-size: 17px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 18px;
        }

        label {
            font-size: 14px;
            color: #334155;
            font-weight: 700;
        }

        input,
        textarea {
            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            outline: none;
        }

        textarea {
            min-height: 220px;
            resize: vertical;
        }

        .error-box {
            margin-bottom: 18px;
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(239, 68, 68, 0.16);
            border: 1px solid rgba(248, 113, 113, 0.4);
            color: #991b1b;
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
                <h1>Nouveau Resume</h1>
                <a class="back-link" href="{{ $fiche->is_fiche_client ? route('fiche-client') : route('fiche-propose') }}">Retour</a>
            </div>

            <p class="company-name">{{ $fiche->nom_entreprise }}</p>

            @if ($errors->any())
                <div class="error-box">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('fiche-propose.resume.store', $fiche) }}" method="POST">
                @csrf

                <div class="field">
                    <label for="titre">Titre du resume</label>
                    <input id="titre" type="text" name="titre" value="{{ old('titre') }}" placeholder="Titre du resume">
                </div>

                <div class="field">
                    <label for="resume">Resume</label>
                    <textarea id="resume" name="resume" placeholder="Ecrivez ici le nouveau resume...">{{ old('resume') }}</textarea>
                </div>

                <div class="save-row">
                    <button class="save-btn" type="submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection
