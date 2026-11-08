<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('study_decks', function (Blueprint $table): void {
            $table->string('status', 30)->default('completed')->after('mode');
            $table->unsignedTinyInteger('progress')->default(100)->after('status');
            $table->text('error_message')->nullable()->after('progress');
        });
    }

    public function down(): void
    {
        Schema::table('study_decks', function (Blueprint $table): void {
            $table->dropColumn(['status', 'progress', 'error_message']);
        });
    }
};
