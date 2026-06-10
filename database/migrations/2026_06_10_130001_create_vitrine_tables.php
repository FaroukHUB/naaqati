<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Slides du hero (page d'accueil)
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('titre')->nullable();
            $table->string('sous_titre')->nullable();
            $table->string('bouton_texte')->nullable();
            $table->string('lien')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // Besoins (recherche par besoin / "pathologie")
        Schema::create('concerns', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('emoji', 16)->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        Schema::create('concern_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concern_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unique(['concern_id', 'product_id']);
        });

        // Avis (témoignages affichés sur l'accueil)
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('texte');
            $table->unsignedTinyInteger('note')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('concern_product');
        Schema::dropIfExists('concerns');
        Schema::dropIfExists('hero_slides');
    }
};
