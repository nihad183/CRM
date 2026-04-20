<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $resume->titre ?: 'Resume' }}</title>

    @php
        $primaryContact = $fiche->contacts->first();
        $sheetTitle = request()->routeIs('fiche-client*') ? 'Fiche client' : 'Fiche prospect';
        $writerName = trim((string) optional($resume->user)->name) ?: trim((string) optional($fiche->user)->name) ?: 'Nom et prenom';
        $contactName = $primaryContact ? trim($primaryContact->nom . ' ' . $primaryContact->prenom) : '';
    @endphp

    <style>
        /* ===== PAGE SETUP ===== */
        @page {
            size: A4;
            margin: 20mm 15mm;
        }

        body {
            margin: 0;
            font-family: "Times New Roman", Times, serif;
            color: #111;
            font-size: 14px;
            background: #d1d5db;
        }

        .page-wrap {
            padding: 18px;
        }

        /* ===== MAIN CONTAINER ===== */
        .sheet {
            width: 210mm;
            max-width: calc(100vw - 36px);
            min-height: 257mm;
            margin: 0 auto;
            position: relative;
            padding: 12mm 12mm 18mm;
            background: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
            border: 1px solid #a3a3a3;
            overflow: hidden;
        }

        /* ===== HEADER ===== */
        .sheet-head {
            display: grid;
            grid-template-columns: 80px 1fr 80px;
            align-items: center;
            margin-bottom: 8mm;
        }

        .logo-box {
            width: 70px;
            height: 55px;
        }

        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .sheet-title {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        }

        /* ===== FIELDS ===== */
        .field-list {
            display: grid;
            gap: 6px;
        }

        .line {
            display: flex;
            gap: 6px;
        }

        .line-label {
            min-width: 130px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .line-value {
            flex: 1;
            border-bottom: 1px solid #000;
            padding-bottom: 2.5px;
        }

        .dual-line {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        /* ===== RESUME ===== */
        .resume-block {
            margin-top: 15mm;
            margin-bottom: 15mm;
        }

        .resume-label {
            font-weight: bold;
            text-transform: uppercase;
        }

        .resume-content {
        line-height: 1.8;
        white-space: pre-wrap;
        padding-bottom: 15px;
        font-size: 20px;
        }

        /* ===== SIGNATURE ===== */
        .writer {
            position: absolute;
            right: 12mm;
            bottom: 10mm;
            font-weight: bold;
            font-size: 18px;
            text-align: right;
        }

        /* ===== SCREEN BUTTONS (NOT PRINTED) ===== */
        .actions {
            margin: 10px;
            text-align: right;
        }

        .btn {
            display: inline-block;
            padding: 8px 14px;
            border: none;
            background: #0f766e;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
        }

        @media print {
            body {
                background: #fff;
            }

            .page-wrap {
                padding: 0;
            }

            .actions {
                display: none;
            }

            .sheet {
                width: 180mm;
                max-width: none;
                min-height: 257mm;
                margin: 0;
                padding: 12mm 12mm 18mm;
                border: none;
                box-shadow: none;
                overflow: hidden;
            }
        }
    </style>
</head>

<body>
<div class="page-wrap">
    <div class="actions">
        <button class="btn" onclick="window.print()">Imprimer</button>
        <a class="btn" href="{{ route('fiche-propose.resume.pdf', ['fichePropose' => $fiche, 'resume' => $resume]) }}">Télécharger PDF</a>
        <a class="btn" href="{{ route('fiche-propose.show', $fiche) }}">Retour</a>
    </div>

    <main class="sheet">

    <!-- HEADER -->
    <div class="sheet-head">
        <div class="logo-box">
            <img src="{{ asset('images/logo-invest-market.png') }}" alt="CRM">
        </div>

        <div class="sheet-title">
           <span>CRV : </span> {{ $sheetTitle }} 
        </div>

        <div></div>
    </div>

    <!-- INFO -->
    <section class="field-list">
        <div class="line">
            <span class="line-label">Date :</span>
            <span class="line-value">{{ $resume->created_at?->format('d/m/Y') ?: '-' }}</span>
        </div>

        <div class="line">
            <span class="line-label">Client :</span>
            <span class="line-value">{{ $fiche->nom_entreprise ?: '-' }}</span>
        </div>

        <div class="line">
            <span class="line-label">Poste :</span>
            <span class="line-value">{{ $primaryContact?->poste ?: '-' }}</span>
        </div>

        <div class="line">
            <span class="line-label">Contact :</span>
            <span class="line-value">{{ $contactName ?: '-' }}</span>
        </div>

        <div class="dual-line">
            <div class="line">
                <span class="line-label">Tel :</span>
                <span class="line-value">{{ $primaryContact?->tel ?: '-' }}</span>
            </div>

            <div class="line">
                <span class="line-label">Email :</span>
                <span class="line-value">{{ $primaryContact?->email ?: '-' }}</span>
            </div>
        </div>

        <div class="line">
            <span class="line-label">Adresse :</span>
            <span class="line-value">{{ $fiche->adresse ?: '-' }}</span>
        </div>

        <div class="line">
            <span class="line-label">Secteur :</span>
            <span class="line-value">{{ $fiche->secteur_activite ?: '-' }}</span>
        </div>
    </section>

    <!-- RESUME -->
    <section class="resume-block">
        <div class="resume-label">Résumé :</div>
        <div class="resume-content">
            {{ $resume->resume }}
        </div>
    </section>

    <!-- SIGNATURE -->
    <div class="writer">
        {{ $writerName }}
    </div>

    </main>
</div>

<script>
    window.onload = () => window.print();
</script>

</body>
</html>
