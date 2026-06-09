<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->char('iso2', 2)->unique();
            $table->char('devise_code', 3);
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->index('actif');
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->timestamps();
        });

        Schema::create('relais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('adresse')->nullable();
            $table->string('batiment')->nullable();
            $table->string('code_portail')->nullable();
            $table->string('etage')->nullable();
            $table->string('telephone')->nullable();
            $table->char('devise_code', 3)->default('DZD');
            $table->boolean('actif')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->index('actif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relais');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('countries');
    }
};
