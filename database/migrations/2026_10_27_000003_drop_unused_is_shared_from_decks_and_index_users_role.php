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
        // Deck sharing was never built; nothing reads or writes is_shared
        Schema::table('decks', function (Blueprint $table) {
            $table->dropColumn('is_shared');
        });

        // Teacher lists and "add student by email" filter on role
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
        });

        Schema::table('decks', function (Blueprint $table) {
            $table->boolean('is_shared')->default(false)->after('title');
        });
    }
};
