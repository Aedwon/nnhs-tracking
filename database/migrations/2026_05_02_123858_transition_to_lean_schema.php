<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add expected_subjects_count to sections
        Schema::table('sections', function (Blueprint $table) {
            $table->integer('expected_subjects_count')->default(0);
        });

        // 2. Clear old data to prevent foreign key conflicts during overhaul
        DB::table('subject_teacher_section')->truncate();
        DB::table('grades')->truncate();
        DB::table('subjects')->truncate();

        // 3. Modify subjects table
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['subject_code', 'written_weight', 'performance_weight', 'exam_weight', 'level']);
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
        });

        // 4. Drop the redundant linking table
        Schema::dropIfExists('subject_teacher_section');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('subject_teacher_section', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->string('school_year')->nullable();
            $table->json('ww_max_scores')->nullable();
            $table->json('pt_max_scores')->nullable();
            $table->integer('qa_max_score')->nullable();
            $table->timestamps();
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
            $table->string('subject_code')->unique()->nullable();
            $table->integer('written_weight')->default(0);
            $table->integer('performance_weight')->default(0);
            $table->integer('exam_weight')->default(0);
            $table->string('level')->default('JHS');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('expected_subjects_count');
        });
    }
};
