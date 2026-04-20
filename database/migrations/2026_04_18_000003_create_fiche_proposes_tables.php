<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiche_proposes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nom_entreprise');
            $table->string('secteur_activite');
            $table->text('adresse');
            $table->longText('resume');
            $table->timestamps();
        });

        Schema::create('fiche_propose_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiche_propose_id')->constrained('fiche_proposes')->cascadeOnDelete();
            $table->string('nom');
            $table->string('prenom');
            $table->string('tel', 50);
            $table->string('email')->nullable();
            $table->string('poste')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiche_propose_contacts');
        Schema::dropIfExists('fiche_proposes');
    }
};
