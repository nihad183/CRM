<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiche_propose_resumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiche_propose_id')->constrained('fiche_proposes')->cascadeOnDelete();
            $table->string('titre')->nullable();
            $table->longText('resume');
            $table->timestamps();
        });

        $rows = DB::table('fiche_proposes')
            ->select('id', 'titre', 'resume', 'created_at', 'updated_at')
            ->whereNotNull('resume')
            ->get()
            ->map(function ($row) {
                return [
                    'fiche_propose_id' => $row->id,
                    'titre' => $row->titre,
                    'resume' => $row->resume,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ];
            })
            ->all();

        if ($rows !== []) {
            DB::table('fiche_propose_resumes')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fiche_propose_resumes');
    }
};
