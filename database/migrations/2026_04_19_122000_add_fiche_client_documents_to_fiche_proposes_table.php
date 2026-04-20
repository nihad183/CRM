<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiche_proposes', function (Blueprint $table) {
            $table->string('n_rc')->nullable()->after('piece_jointe_original_name');
            $table->string('n_rc_piece_path')->nullable()->after('n_rc');
            $table->string('n_rc_piece_original_name')->nullable()->after('n_rc_piece_path');
            $table->string('nif')->nullable()->after('n_rc_piece_original_name');
            $table->string('nif_piece_path')->nullable()->after('nif');
            $table->string('nif_piece_original_name')->nullable()->after('nif_piece_path');
            $table->string('nis')->nullable()->after('nif_piece_original_name');
            $table->string('nis_piece_path')->nullable()->after('nis');
            $table->string('nis_piece_original_name')->nullable()->after('nis_piece_path');
        });
    }

    public function down(): void
    {
        Schema::table('fiche_proposes', function (Blueprint $table) {
            $table->dropColumn([
                'n_rc',
                'n_rc_piece_path',
                'n_rc_piece_original_name',
                'nif',
                'nif_piece_path',
                'nif_piece_original_name',
                'nis',
                'nis_piece_path',
                'nis_piece_original_name',
            ]);
        });
    }
};
