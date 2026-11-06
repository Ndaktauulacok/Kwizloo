<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_decks', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->longText('source_text');
            $table->unsignedInteger('total_words')->default(0);
            $table->string('mode')->default('sim'); // 'sim' or 'ai'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_decks');
    }
};
