<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiche_proposes', function (Blueprint $table) {
            $table->string('compliance_status')->default('clear')->after('client_conversion_status');
            $table->json('legal_representative')->nullable()->after('nis_piece_original_name');
            $table->json('authorized_signatories')->nullable()->after('legal_representative');
            $table->json('shareholders')->nullable()->after('authorized_signatories');
        });

        Schema::create('compliance_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('path');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('entries_count')->default(0);
            $table->string('filtering_result')->default('Aucune correspondance');
            $table->decimal('best_match_ratio', 5, 2)->default(0);
            $table->timestamp('filtered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('compliance_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compliance_upload_id')->nullable()->constrained('compliance_uploads')->cascadeOnDelete();
            $table->foreignId('fiche_propose_id')->nullable()->constrained('fiche_proposes')->cascadeOnDelete();
            $table->string('source_type')->default('upload');
            $table->string('person_role')->nullable();
            $table->string('full_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('nationality')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('document_number')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });

        Schema::create('compliance_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compliance_upload_id')->nullable()->constrained('compliance_uploads')->cascadeOnDelete();
            $table->foreignId('compliance_entry_id')->constrained('compliance_entries')->cascadeOnDelete();
            $table->foreignId('matched_entry_id')->nullable()->constrained('compliance_entries')->nullOnDelete();
            $table->foreignId('fiche_propose_id')->nullable()->constrained('fiche_proposes')->cascadeOnDelete();
            $table->foreignId('matched_fiche_propose_id')->nullable()->constrained('fiche_proposes')->nullOnDelete();
            $table->string('ref_dossier')->nullable();
            $table->string('nom_dossier')->nullable();
            $table->string('matched_name')->nullable();
            $table->decimal('match_ratio', 5, 2)->default(0);
            $table->json('matched_information')->nullable();
            $table->string('decision_status')->default('pending');
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_matches');
        Schema::dropIfExists('compliance_entries');
        Schema::dropIfExists('compliance_uploads');

        Schema::table('fiche_proposes', function (Blueprint $table) {
            $table->dropColumn([
                'compliance_status',
                'legal_representative',
                'authorized_signatories',
                'shareholders',
            ]);
        });
    }
};
