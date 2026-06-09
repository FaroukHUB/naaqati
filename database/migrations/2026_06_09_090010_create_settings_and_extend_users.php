<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('cle', 100);
            $table->text('valeur')->nullable();
            $table->foreignId('relais_id')->nullable()->constrained('relais')->cascadeOnDelete(); // null = global
            $table->timestamps();
            $table->unique(['cle', 'relais_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('relais_id')->nullable()->after('email')
                ->constrained('relais')->nullOnDelete(); // null = admin central
            $table->boolean('actif')->default(true)->after('relais_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('relais_id');
            $table->dropColumn('actif');
        });
        Schema::dropIfExists('settings');
    }
};
