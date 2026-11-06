<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('study_deck_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('summary')->nullable();
            $table->string('status')->default('active'); // active, weak, mastered, boss, tewas
            $table->unsignedInteger('hp')->default(3);
            $table->unsignedInteger('max_hp')->default(3);
            $table->unsignedInteger('right_count')->default(0);
            $table->unsignedInteger('wrong_count')->default(0);
            $table->unsignedInteger('attempts')->default(0);
            $table->unsignedInteger('losses')->default(0);
            $table->json('mistakes')->nullable();
            $table->json('strategy')->nullable(); // level (1-3), hint (bool), note (string)
            $table->timestamp('rematch_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
