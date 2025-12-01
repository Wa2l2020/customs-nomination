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
        Schema::table('users', function (Blueprint $table) {
            $table->string('job_grade')->nullable(); // First, Second, Third, Fourth
            $table->string('highest_degree')->nullable();
            $table->string('job_title')->nullable(); // Manual entry
            $table->string('department_name')->nullable(); // Manual entry
        });

        Schema::table('nominations', function (Blueprint $table) {
            $table->string('job_grade')->nullable();
            $table->string('highest_degree')->nullable();
            $table->string('department_name')->nullable(); // Manual entry
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['job_grade', 'highest_degree', 'job_title', 'department_name']);
        });

        Schema::table('nominations', function (Blueprint $table) {
            $table->dropColumn(['job_grade', 'highest_degree', 'department_name']);
        });
    }
};
