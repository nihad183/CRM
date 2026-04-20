<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiche_proposes', function (Blueprint $table) {
            $table->string('piece_jointe_path')->nullable()->after('resume');
            $table->string('piece_jointe_original_name')->nullable()->after('piece_jointe_path');
        });
    }

    public function down(): void
    {
        Schema::table('fiche_proposes', function (Blueprint $table) {
            $table->dropColumn(['piece_jointe_path', 'piece_jointe_original_name']);
        });
    }
};
