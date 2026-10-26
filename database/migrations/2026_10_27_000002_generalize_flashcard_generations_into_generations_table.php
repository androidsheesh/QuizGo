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
        // AI quiz generation now tracks progress the same way flashcard generation does
        Schema::rename('flashcard_generations', 'generations');

        Schema::table('generations', function (Blueprint $table) {
            $table->string('kind', 20)->default('flashcards')->after('uuid');
            $table->foreignId('quiz_id')->nullable()->after('deck_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('quiz_id');
            $table->dropColumn('kind');
        });

        Schema::rename('generations', 'flashcard_generations');
    }
};
