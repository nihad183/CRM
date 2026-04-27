<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiche_proposes', function (Blueprint $table) {
            $table->decimal('contract_amount', 15, 2)->nullable()->after('piece_jointe_original_name');
            $table->date('contract_signed_at')->nullable()->after('contract_amount');
            $table->foreignId('contract_user_id')->nullable()->after('contract_signed_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fiche_proposes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contract_user_id');
            $table->dropColumn([
                'contract_amount',
                'contract_signed_at',
            ]);
        });
    }
};
