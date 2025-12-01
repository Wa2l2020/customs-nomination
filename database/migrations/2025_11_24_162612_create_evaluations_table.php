<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('nomination_id')->constrained()->onDelete('cascade');
            $table->integer('score')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'nomination_id']); // One evaluation per user per nomination
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
