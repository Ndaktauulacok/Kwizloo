<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('study_deck_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->json('options'); // array of 4 options
            $table->unsignedTinyInteger('correct_index'); // 0-3
            $table->unsignedTinyInteger('difficulty')->default(2); // 1-3
            $table->text('explanation');
            $table->text('quote'); // verbatim excerpt from source note
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
