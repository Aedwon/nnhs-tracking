<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('school_level', ['JHS', 'SHS', 'BOTH'])->default('JHS')->after('email');
        });
        Schema::table('grading_periods', function (Blueprint $table) {
            $table->enum('level', ['JHS', 'SHS', 'BOTH'])->default('BOTH')->after('name');
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->enum('level', ['JHS', 'SHS'])->default('JHS')->after('grade_level');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('school_level');
        });
        Schema::table('grading_periods', function (Blueprint $table) {
            $table->dropColumn('level');
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};
