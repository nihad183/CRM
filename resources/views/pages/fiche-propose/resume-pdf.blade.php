<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>{{ $resume->titre ?: 'Resume' }}</title>

@php
    $primaryContact = $fiche->contacts->first();
    $sheetTitle = $fiche->is_fiche_client ? 'Fiche client' : 'Fiche prospect';
    $writerName = trim((string) optional($resume->user)->name) ?: trim((string) optional($fiche->user)->name) ?: 'Nom et prenom';
    $contactName = $primaryContact ? trim($primaryContact->nom . ' ' . $primaryContact->prenom) : '';
@endphp

<style>
@page {
    size: A4;
    margin: 10mm;
}

body {
    margin: 0;
    font-family: "Times New Roman", serif;
    font-size: 14px;
    color: #111;
}

/* ===== SHEET ===== */
.sheet {
    width: 190mm;
    min-height: 277mm;
    margin: 0 auto;
    padding: 10mm 10mm 18mm;
    position: relative;
    border: 1px solid #444;
    box-sizing: border-box;
}

/* ===== HEADER ===== */
.sheet-head {
    display: table;
    width: 100%;
    margin-bottom: 10mm;
    padding-bottom: 4mm;
}

.head-col {
    display: table-cell;
    vertical-align: middle;
}

.head-col.left {
    width: 80px;
}

.logo-box img {
    width: 65px;
}

.sheet-title {
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    letter-spacing: 0.5px;
}

/* ===== FIELDS ===== */
.field-list {
    margin-top: 5mm;
}

.line {
    margin-bottom: 6px;
}

.line-label {
    display: inline-block;
    width: 130px;
    font-weight: bold;
    text-transform: uppercase;
}

.line-value {
    display: inline-block;
    width: calc(100% - 135px);
    border-bottom: 1px solid #444;
    padding-bottom: 2px;
}

/* ===== TWO COL ===== */
.dual-line {
    margin-bottom: 6px;
}

.dual-item {
    display: inline-block;
    width: 48%;
}

.dual-item .line-label {
    width: 80px;
}

.dual-item .line-value {
    width: calc(100% - 85px);
}

/* ===== RESUME ===== */
.resume-block {
    margin-top: 18mm;
}

.resume-label {
    font-weight: bold;
    margin-bottom: 5mm;
    text-transform: uppercase;
}

/* ⭐ أهم تحسين */
.resume-content {
    font-size: 15px;
    line-height: 1.9;
    text-align: justify;
    white-space: pre-wrap;
}

/* ===== SIGNATURE ===== */
.writer {
    position: absolute;
    right: 10mm;
    bottom: 10mm;
    font-weight: bold;
    font-size: 15px;
}

</style>
</head>

<body>

<main class="sheet">

    <!-- HEADER -->
    <div class="sheet-head">
        <div class="head-col left">
            <div class="logo-box">
                <img src="{{ public_path('images/logo-invest-market.png') }}">
            </div>
        </div>

        <div class="head-col">
            <div class="sheet-title">
                CRV : {{ $sheetTitle }}
            </div>
        </div>

        <div class="head-col"></div>
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
            <div class="dual-item">
                <span class="line-label">Tel :</span>
                <span class="line-value">{{ $primaryContact?->tel ?: '-' }}</span>
            </div>

            <div class="dual-item">
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

</body>
</html>
