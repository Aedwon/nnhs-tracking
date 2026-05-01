<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subject_teacher_section', function (Blueprint $table) {
            $table->json('ww_max_scores')->nullable();
            $table->json('pt_max_scores')->nullable();
            $table->decimal('qa_max_score', 5, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_teacher_section', function (Blueprint $table) {
            $table->dropColumn(['ww_max_scores', 'pt_max_scores', 'qa_max_score']);
        });
    }
};
