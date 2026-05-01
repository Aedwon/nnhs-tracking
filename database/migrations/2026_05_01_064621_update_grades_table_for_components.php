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
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn(['student_id_number', 'student_name', 'subject_code']);
            $table->foreignId('student_id')->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->after('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->after('subject_id')->constrained()->onDelete('cascade');
            $table->json('written_work_scores')->nullable();
            $table->json('performance_task_scores')->nullable();
            $table->decimal('exam_score', 5, 2)->nullable();
            $table->boolean('is_finalized')->default(false);
            $table->text('justification')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->string('student_id_number')->nullable();
            $table->string('student_name')->nullable();
            $table->string('subject_code')->nullable();
            $table->dropForeign(['student_id']);
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['section_id']);
            $table->dropColumn(['student_id', 'subject_id', 'section_id', 'written_work_scores', 'performance_task_scores', 'exam_score', 'is_finalized', 'justification']);
        });
    }
};
