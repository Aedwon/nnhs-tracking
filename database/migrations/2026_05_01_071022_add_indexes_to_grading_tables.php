<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->index(['subject_id', 'section_id', 'is_finalized']);
            $table->index('teacher_id');
        });

        Schema::table('subject_teacher_section', function (Blueprint $table) {
            $table->index(['teacher_id', 'section_id']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->index('section_id');
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropIndex(['subject_id', 'section_id', 'is_finalized']);
            $table->dropIndex(['teacher_id']);
        });

        Schema::table('subject_teacher_section', function (Blueprint $table) {
            $table->dropIndex(['teacher_id', 'section_id']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['section_id']);
        });
    }
};
