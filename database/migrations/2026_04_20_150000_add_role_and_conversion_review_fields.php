<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('employee')->after('position');
        });

        DB::table('users')
            ->whereNull('role')
            ->update(['role' => 'employee']);

        $firstUserId = DB::table('users')->orderBy('id')->value('id');

        if ($firstUserId) {
            DB::table('users')
                ->where('id', $firstUserId)
                ->update(['role' => 'admin']);
        }

        Schema::table('fiche_proposes', function (Blueprint $table) {
            $table->string('client_conversion_status')->default('not_requested')->after('piece_jointe_original_name');
            $table->foreignId('piece_jointe_uploaded_by')->nullable()->after('client_conversion_status')->constrained('users')->nullOnDelete();
            $table->timestamp('piece_jointe_uploaded_at')->nullable()->after('piece_jointe_uploaded_by');
            $table->foreignId('conversion_reviewed_by')->nullable()->after('piece_jointe_uploaded_at')->constrained('users')->nullOnDelete();
            $table->timestamp('conversion_reviewed_at')->nullable()->after('conversion_reviewed_by');
        });

        DB::table('fiche_proposes')
            ->where('is_fiche_client', true)
            ->update([
                'client_conversion_status' => 'approved',
                'piece_jointe_uploaded_by' => DB::raw('user_id'),
                'piece_jointe_uploaded_at' => DB::raw('COALESCE(converted_to_client_at, updated_at, created_at)'),
                'conversion_reviewed_by' => DB::raw('user_id'),
                'conversion_reviewed_at' => DB::raw('COALESCE(converted_to_client_at, updated_at, created_at)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('fiche_proposes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('piece_jointe_uploaded_by');
            $table->dropConstrainedForeignId('conversion_reviewed_by');
            $table->dropColumn([
                'client_conversion_status',
                'piece_jointe_uploaded_at',
                'conversion_reviewed_at',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
