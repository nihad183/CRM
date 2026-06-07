@php
    $person = $person ?? [];
@endphp

<div class="field">
    <label>Nom et prenom</label>
    <input type="text" name="{{ $prefix }}[full_name]" value="{{ $person['full_name'] ?? '' }}" placeholder="Nom et prenom">
</div>

<div class="field">
    <label>Nom du pere</label>
    <input type="text" name="{{ $prefix }}[father_name]" value="{{ $person['father_name'] ?? '' }}" placeholder="Nom du pere">
</div>

<div class="field">
    <label>Nom et prenom de la mere</label>
    <input type="text" name="{{ $prefix }}[mother_name]" value="{{ $person['mother_name'] ?? '' }}" placeholder="Nom et prenom de la mere">
</div>

<div class="field">
    <label>Nationalite</label>
    <input type="text" name="{{ $prefix }}[nationality]" value="{{ $person['nationality'] ?? '' }}" placeholder="Nationalite">
</div>

<div class="field">
    <label>Date naissance</label>
    <input type="date" name="{{ $prefix }}[birth_date]" value="{{ $person['birth_date'] ?? '' }}">
</div>

<div class="field">
    <label>Lieu naissance</label>
    <input type="text" name="{{ $prefix }}[birth_place]" value="{{ $person['birth_place'] ?? '' }}" placeholder="Lieu naissance">
</div>

<div class="field full-width">
    <label>NIN/Passeport</label>
    <input type="text" name="{{ $prefix }}[document_number]" value="{{ $person['document_number'] ?? '' }}" placeholder="NIN ou passeport">
</div>
