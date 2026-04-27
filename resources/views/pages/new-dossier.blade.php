@extends('layouts.app')

@php
    $selectedType = old('dossier_type', '');
    $contacts = old('contacts', [
        [
            'nom' => '',
            'prenom' => '',
            'tel' => '',
            'email' => '',
            'poste' => '',
        ],
    ]);
@endphp

@section('content')
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Tahoma, Arial, sans-serif;
            background:
                linear-gradient(rgba(15, 23, 42, 0.94), rgba(15, 23, 42, 0.86)),
                url('{{ asset('images/crm.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            color: #f8fafc;
        }

        .page-shell {
            min-height: 100vh;
            padding: 110px 24px 40px;
        }

        .page-card {
            width: min(1400px, 100%);
            margin: 0 auto;
            padding: 42px;
            border-radius: 30px;
            background: #ffffff;
            border: 1px solid #dbe4ee;
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.712);
            color: #0f172a;
        }

        .page-head {
            margin-bottom: 28px;
        }

        .page-head h1 {
            display: inline-flex;
            align-items: center;
            margin: 0;
            padding: 0 0 14px 18px;
            font-size: clamp(30px, 5vw, 46px);
            border-left: 6px solid #14b8a6;
            border-bottom: 2px solid #dbe4ee;
            border-radius: 2px;
            line-height: 1.05;
        }

        .feedback,
        .error-box,
        .placeholder-box {
            margin-bottom: 22px;
            padding: 16px 18px;
            border-radius: 18px;
        }

        .feedback {
            background: rgba(20, 184, 166, 0.18);
            border: 1px solid rgba(45, 212, 191, 0.4);
            color: #115e59;
        }

        .error-box {
            background: rgba(239, 68, 68, 0.16);
            border: 1px solid rgba(248, 113, 113, 0.4);
            color: #991b1b;
        }

        .placeholder-box {
            background: rgba(251, 191, 36, 0.14);
            border: 1px solid rgba(245, 158, 11, 0.32);
            color: #92400e;
        }

        .type-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .type-option {
            position: relative;
            display: block;
        }

        .type-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .type-card {
            display: flex;
            align-items: center;
            height: 100%;
            min-height: 120px;
            padding: 22px;
            border-radius: 22px;
            border: 1.5px solid #d5e2ee;
            background: linear-gradient(180deg, #f8fbff 0%, #f1f5f9 100%);
            cursor: pointer;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
            transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        .type-card:hover {
            transform: translateY(-2px);
            border-color: rgba(20, 184, 166, 0.45);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
        }

        .type-option input:checked + .type-card {
            border-color: rgba(20, 184, 166, 0.82);
            background: linear-gradient(180deg, rgba(20, 184, 166, 0.16) 0%, rgba(20, 184, 166, 0.08) 100%);
            box-shadow: 0 18px 32px rgba(8, 145, 178, 0.18);
        }

        .type-card strong {
            display: inline-flex;
            align-items: center;
            min-height: 44px;
            padding-left: 16px;
            font-size: 21px;
            color: #1e293b;
            border-left: 4px solid #14b8a6;
        }

        .type-card span {
            color: #64748b;
            line-height: 1.6;
        }

        .form-panel {
            display: none;
        }

        .form-panel.is-visible {
            display: block;
        }

        .section-title {
            display: inline-flex;
            align-items: center;
            margin: 0 0 18px;
            padding-left: 14px;
            font-size: 24px;
            border-left: 4px solid #14b8a6;
        }

        .field-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 18px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field.full-width {
            grid-column: 1 / -1;
        }

        label {
            font-size: 14px;
            color: #334155;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            outline: none;
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        input::placeholder,
        textarea::placeholder {
            color: #94a3b8;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #14b8a6;
            background: #ffffff;
        }

        textarea {
            min-height: 140px;
            resize: vertical;
        }

        .contacts-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin: 30px 0 18px;
        }

        .contacts-head h2 {
            margin: 0;
            padding-left: 14px;
            font-size: 24px;
            border-left: 4px solid #14b8a6;
        }

        .ghost-btn,
        .save-btn,
        .remove-btn {
            border: none;
            border-radius: 14px;
            cursor: pointer;
            font-weight: 700;
        }

        .ghost-btn {
            padding: 12px 18px;
            background: #f8fafc;
            color: #0f172a;
            border: 1px solid #cbd5e1;
        }

        .contact-card {
            padding: 22px;
            margin-bottom: 16px;
            border-radius: 22px;
            background: #f8fafc;
            border: 1px solid #dbe4ee;
        }

        .contact-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .contact-top h3 {
            margin: 0;
            padding-left: 12px;
            font-size: 18px;
            border-left: 3px solid #14b8a6;
        }

        .remove-btn {
            padding: 10px 14px;
            background: rgba(239, 68, 68, 0.15);
            color: #991b1b;
            border: 1px solid rgba(248, 113, 113, 0.24);
        }

        .save-row {
            margin-top: 26px;
            display: flex;
            justify-content: flex-end;
        }

        .save-btn {
            padding: 14px 24px;
            background: linear-gradient(135deg, #0f766e, #14b8a6);
            color: #fff;
            min-width: 180px;
            box-shadow: 0 16px 30px rgba(20, 184, 166, 0.22);
        }

        @media (max-width: 860px) {
            .type-grid,
            .field-grid {
                grid-template-columns: 1fr;
            }

            .page-card {
                padding: 24px;
            }

            .contacts-head,
            .contact-top {
                flex-direction: column;
                align-items: stretch;
            }

            .save-row {
                justify-content: stretch;
            }

            .save-btn {
                width: 100%;
            }
        }
    </style>

    <div class="page-shell">
            <div class="page-card">
            <div class="page-head">
                <h1>Nouveau dossier</h1>
            </div>

            @if (session('status'))
                <div class="feedback">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="error-box">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('fiche-propose.store') }}" method="POST" id="new-dossier-form" enctype="multipart/form-data">
                @csrf

                <div class="type-grid">
                    <label class="type-option">
                        <input type="radio" name="dossier_type" value="fiche-client" {{ $selectedType === 'fiche-client' ? 'checked' : '' }}>
                        <span class="type-card">
                            <strong>Fiche Client</strong>
                        </span>
                    </label>

                    <label class="type-option">
                        <input type="radio" name="dossier_type" value="fiche-propose" {{ $selectedType === 'fiche-propose' ? 'checked' : '' }}>
                        <span class="type-card">
                            <strong>Fiche Prospect</strong>
                        </span>
                    </label>
                </div>

                <div id="dossier-details-panel" class="form-panel {{ in_array($selectedType, ['fiche-propose', 'fiche-client'], true) ? 'is-visible' : '' }}">
                    <h2 class="section-title">Informations entreprise</h2>

                    <div class="field-grid">
                        <div class="field">
                            <label for="nom_entreprise">Nom entreprise</label>
                            <input id="nom_entreprise" type="text" name="nom_entreprise" value="{{ old('nom_entreprise') }}" placeholder="Nom de l'entreprise">
                        </div>

                        <div class="field">
                            <label for="secteur_activite">Secteur activite</label>
                            <input id="secteur_activite" type="text" name="secteur_activite" value="{{ old('secteur_activite') }}" placeholder="Ex: Immobilier, Finance, Industrie">
                        </div>

                        <div class="field full-width">
                            <label for="adresse">Adresse</label>
                            <textarea id="adresse" name="adresse" placeholder="Adresse complete de l'entreprise">{{ old('adresse') }}</textarea>
                        </div>
                    </div>

                    <div class="contacts-head">
                        <h2>Contacts</h2>
                        <button class="ghost-btn" type="button" id="add-contact-btn">Ajouter un contact</button>
                    </div>

                    <div id="contacts-wrapper">
                        @foreach ($contacts as $index => $contact)
                            <div class="contact-card" data-contact-card>
                                <div class="contact-top">
                                    <h3>Contact <span data-contact-number>{{ $index + 1 }}</span></h3>
                                    <button class="remove-btn" type="button" data-remove-contact {{ count($contacts) === 1 ? 'hidden' : '' }}>Supprimer</button>
                                </div>

                                <div class="field-grid">
                                    <div class="field">
                                        <label>Nom</label>
                                        <input type="text" name="contacts[{{ $index }}][nom]" value="{{ $contact['nom'] ?? '' }}" placeholder="Nom">
                                    </div>

                                    <div class="field">
                                        <label>Prenom</label>
                                        <input type="text" name="contacts[{{ $index }}][prenom]" value="{{ $contact['prenom'] ?? '' }}" placeholder="Prenom">
                                    </div>

                                    <div class="field">
                                        <label>Tel</label>
                                        <input type="text" name="contacts[{{ $index }}][tel]" value="{{ $contact['tel'] ?? '' }}" placeholder="0550123456" inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10">
                                    </div>

                                    <div class="field">
                                        <label>Email</label>
                                        <input type="email" name="contacts[{{ $index }}][email]" value="{{ $contact['email'] ?? '' }}" placeholder="contact@entreprise.com">
                                    </div>

                                    <div class="field full-width">
                                        <label>Poste personne</label>
                                        <input type="text" name="contacts[{{ $index }}][poste]" value="{{ $contact['poste'] ?? '' }}" placeholder="Directeur, Responsable achat...">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>


                    <div id="fiche-client-extra" class="form-panel {{ $selectedType === 'fiche-client' ? 'is-visible' : '' }}">
                        <h2 class="section-title">Documents fiche client</h2>

                        <div class="field-grid">
                            <div class="field">
                                <label for="piece_jointe">Piece de jointe contrat</label>
                                <input id="piece_jointe" type="file" name="piece_jointe" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            </div>

                            <div class="field">
                                <label for="contract_amount">Montant du contrat</label>
                                <input id="contract_amount" type="text" name="contract_amount" value="{{ old('contract_amount') }}" placeholder="Ex: 2,500,000" inputmode="decimal">
                            </div>

                            <div class="field">
                                <label for="contract_signed_at">Date de signature</label>
                                <input id="contract_signed_at" type="date" name="contract_signed_at" value="{{ old('contract_signed_at') }}">
                            </div>

                            <div class="field">
                                <label for="contract_commercial_name">Commercial concerne</label>
                                <input id="contract_commercial_name" type="text" value="{{ auth()->user()->name }}" readonly>
                            </div>

                            <div class="field">
                                <label>&nbsp;</label>
                                <div style="padding: 14px 16px; border-radius: 16px; border: 1px dashed #cbd5e1; color: #64748b; background: #f8fafc;">
                                    Le contrat signe doit contenir le montant, la date de signature et le commercial concerne.
                                </div>
                            </div>

                            <div class="field">
                                <label for="n_rc">N RC</label>
                                <input id="n_rc" type="text" name="n_rc" value="{{ old('n_rc') }}" placeholder="Numero RC">
                            </div>

                            <div class="field">
                                <label for="n_rc_piece">Piece de jointe RC</label>
                                <input id="n_rc_piece" type="file" name="n_rc_piece" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            </div>

                            <div class="field">
                                <label for="nif">NIF</label>
                                <input id="nif" type="text" name="nif" value="{{ old('nif') }}" placeholder="Numero NIF">
                            </div>

                            <div class="field">
                                <label for="nif_piece">Piece de jointe NIF</label>
                                <input id="nif_piece" type="file" name="nif_piece" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            </div>

                            <div class="field">
                                <label for="nis">NIS</label>
                                <input id="nis" type="text" name="nis" value="{{ old('nis') }}" placeholder="Numero NIS">
                            </div>

                            <div class="field">
                                <label for="nis_piece">Piece de jointe NIS</label>
                                <input id="nis_piece" type="file" name="nis_piece" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>
                    
                    <h2 class="section-title">Resume</h2>

                    <div class="field">
                        <label for="titre">Titre </label>
                        <input id="titre" type="text" name="titre" value="{{ old('titre') }}" placeholder="Titre ">
                    </div>

                    <div class="field">
                        <label for="resume">Resume</label>
                        <textarea id="resume" name="resume" placeholder="Ajoutez ici un resume du prospect...">{{ old('resume') }}</textarea>
                    </div>


                    <div class="save-row">
                        <button class="save-btn" type="submit">Enregistrer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <template id="contact-template">
        <div class="contact-card" data-contact-card>
            <div class="contact-top">
                <h3>Contact <span data-contact-number></span></h3>
                <button class="remove-btn" type="button" data-remove-contact>Supprimer</button>
            </div>

            <div class="field-grid">
                <div class="field">
                    <label>Nom</label>
                    <input type="text" data-name="nom" placeholder="Nom">
                </div>

                <div class="field">
                    <label>Prenom</label>
                    <input type="text" data-name="prenom" placeholder="Prenom">
                </div>

                <div class="field">
                    <label>Tel</label>
                    <input type="text" data-name="tel" placeholder="0550123456" inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10">
                </div>

                <div class="field">
                    <label>Email</label>
                    <input type="email" data-name="email" placeholder="contact@entreprise.com">
                </div>

                <div class="field full-width">
                    <label>Poste personne</label>
                    <input type="text" data-name="poste" placeholder="Directeur, Responsable achat...">
                </div>
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeInputs = Array.from(document.querySelectorAll('input[name="dossier_type"]'));
            const dossierDetailsPanel = document.getElementById('dossier-details-panel');
            const ficheClientExtra = document.getElementById('fiche-client-extra');
            const selectionHint = document.getElementById('selection-hint');
            const contactsWrapper = document.getElementById('contacts-wrapper');
            const addContactButton = document.getElementById('add-contact-btn');
            const contactTemplate = document.getElementById('contact-template');

            function togglePanels() {
                const selected = document.querySelector('input[name="dossier_type"]:checked')?.value;
                const showDetails = selected === 'fiche-propose' || selected === 'fiche-client';

                dossierDetailsPanel.classList.toggle('is-visible', showDetails);
                ficheClientExtra.classList.toggle('is-visible', selected === 'fiche-client');
                selectionHint?.classList.toggle('is-visible', !selected);
            }

            function normalizePhoneInput(input) {
                input.value = input.value.replace(/\D/g, '').slice(0, 10);
            }

            function bindPhoneValidation(scope = document) {
                scope.querySelectorAll('input[name$="[tel]"], input[data-name="tel"]').forEach((input) => {
                    input.addEventListener('input', function () {
                        normalizePhoneInput(input);
                    });

                    normalizePhoneInput(input);
                });
            }

            function updateContactIndexes() {
                const cards = Array.from(contactsWrapper.querySelectorAll('[data-contact-card]'));

                cards.forEach((card, index) => {
                    const number = card.querySelector('[data-contact-number]');
                    const removeButton = card.querySelector('[data-remove-contact]');

                    if (number) {
                        number.textContent = index + 1;
                    }

                    if (removeButton) {
                        removeButton.hidden = cards.length === 1;
                    }

                    card.querySelectorAll('input').forEach((input) => {
                        const field = input.getAttribute('data-name') || input.name.match(/\[(\w+)\]$/)?.[1];

                        if (field) {
                            input.name = `contacts[${index}][${field}]`;
                        }
                    });
                });
            }

            function addContact() {
                const fragment = contactTemplate.content.cloneNode(true);
                contactsWrapper.appendChild(fragment);
                bindPhoneValidation(contactsWrapper.lastElementChild ?? contactsWrapper);
                updateContactIndexes();
            }

            typeInputs.forEach((input) => {
                input.addEventListener('change', togglePanels);
            });

            addContactButton.addEventListener('click', addContact);

            contactsWrapper.addEventListener('click', function (event) {
                const removeButton = event.target.closest('[data-remove-contact]');

                if (!removeButton) {
                    return;
                }

                const cards = contactsWrapper.querySelectorAll('[data-contact-card]');

                if (cards.length === 1) {
                    return;
                }

                removeButton.closest('[data-contact-card]')?.remove();
                updateContactIndexes();
            });

            togglePanels();
            bindPhoneValidation(contactsWrapper);
            updateContactIndexes();
        });
    </script>
@endsection
