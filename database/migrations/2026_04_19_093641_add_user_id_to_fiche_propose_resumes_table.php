<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiche_propose_resumes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('fiche_propose_id')->constrained()->nullOnDelete();
        });

        DB::table('fiche_propose_resumes')
            ->join('fiche_proposes', 'fiche_proposes.id', '=', 'fiche_propose_resumes.fiche_propose_id')
            ->update([
                'fiche_propose_resumes.user_id' => DB::raw('fiche_proposes.user_id'),
            ]);
    }

    public function down(): void
    {
        Schema::table('fiche_propose_resumes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
