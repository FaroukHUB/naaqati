<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->integer('position')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->index('actif');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('desc_courte', 500)->nullable();
            $table->text('desc_longue')->nullable();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('poids_grammes')->nullable();
            $table->unsignedBigInteger('prix_base'); // en centimes
            $table->char('devise_code', 3)->default('DZD');
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('actif');
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('chemin');
            $table->integer('position')->default(0);
            $table->timestamps();
        });

        Schema::create('bundles', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('desc_courte', 500)->nullable();
            $table->unsignedBigInteger('prix_special'); // en centimes
            $table->char('devise_code', 3)->default('DZD');
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('actif');
        });

        Schema::create('bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('quantite')->default(1);
            $table->timestamps();
            $table->unique(['bundle_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_items');
        Schema::dropIfExists('bundles');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
