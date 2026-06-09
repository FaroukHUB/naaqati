<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('relais_id')->constrained('relais')->restrictOnDelete();
            $table->string('statut')->default('recue'); // App\Enums\OrderStatus
            $table->char('devise_code', 3)->default('DZD');
            $table->unsignedBigInteger('sous_total'); // centimes
            $table->foreignId('packaging_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('frais_emballage')->default(0);
            $table->unsignedBigInteger('total');
            $table->date('date_retrait');
            $table->string('creneau_retrait', 40); // "matin" ou "10:00"
            $table->text('commentaire')->nullable();
            $table->timestamps();
            $table->index('statut');
            $table->index(['relais_id', 'date_retrait', 'creneau_retrait']);
            $table->index('customer_id');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('bundle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nom_snapshot');
            $table->unsignedBigInteger('prix_unitaire'); // centimes, figé
            $table->unsignedSmallInteger('quantite');
            $table->unsignedBigInteger('total_ligne');
            $table->timestamps();
            $table->index('order_id');
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('ancien_statut')->nullable();
            $table->string('nouveau_statut');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('note')->nullable();
            $table->timestamps();
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
