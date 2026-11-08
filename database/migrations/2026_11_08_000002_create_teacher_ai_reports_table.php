<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_ai_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('study_deck_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('pending');
            $table->text('summary')->nullable();
            $table->json('misconceptions')->nullable();
            $table->json('recommendations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_ai_reports');
    }
};
