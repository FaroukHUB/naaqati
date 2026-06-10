<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('concerns', function (Blueprint $table) {
            // Stocke désormais un nom d'icône (heroicon-...), plus long que 16.
            $table->string('emoji', 64)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('concerns', function (Blueprint $table) {
            $table->string('emoji', 16)->nullable()->change();
        });
    }
};
