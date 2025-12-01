<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Unified Departments Table (Hierarchy)
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable(); // If null -> Central, If set -> General
            $table->foreign('parent_id')->references('id')->on('departments')->onDelete('cascade');
            $table->timestamps();
        });

        // Users (Managers & Committee)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role'); // central, general, committee, chairman
            
            // Profile Data
            $table->string('job_number')->nullable();
            $table->string('phone')->nullable();
            
            // Link to Department (for managers)
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            
            $table->rememberToken();
            $table->timestamps();
        });

        // Nominations
        Schema::create('nominations', function (Blueprint $table) {
            $table->id();
            // Candidate Info
            $table->string('employee_name');
            $table->string('job_number');
            $table->string('job_title');
            $table->string('phone');
            $table->string('email');
            
            // Links
            $table->unsignedBigInteger('central_dept_id');
            $table->unsignedBigInteger('general_dept_id');
            
            $table->string('category');
            $table->json('answers')->nullable();
            $table->json('attachments')->nullable();
            
            // Workflow
            $table->string('status')->default('pending'); // pending, approved_general, approved_central, winner, rejected
            $table->float('score_avg')->default(0);
            
            $table->timestamps();
        });

        // Evaluations table creation removed to avoid conflict with 2025_11_24_162612_create_evaluations_table.php
        
        // Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Laravel Default Tables (Sessions, Cache, Jobs) - Added to fix missing table errors
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('nominations');
        Schema::dropIfExists('users');
        Schema::dropIfExists('departments');
    }
};
