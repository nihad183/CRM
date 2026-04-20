<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiche_proposes', function (Blueprint $table) {
            $table->boolean('is_fiche_client')->default(false)->after('resume');
        });
    }

    public function down(): void
    {
        Schema::table('fiche_proposes', function (Blueprint $table) {
            $table->dropColumn('is_fiche_client');
        });
    }
};
