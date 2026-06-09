<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('relais_id')->constrained('relais')->cascadeOnDelete();
            $table->integer('stock_disponible')->default(0);
            $table->integer('stock_reserve')->default(0);
            $table->integer('stock_vendu')->default(0);
            $table->unsignedBigInteger('prix_override')->nullable(); // centimes, override par relais
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->unique(['product_id', 'relais_id']);
            $table->index('relais_id');
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('relais_id')->constrained('relais')->cascadeOnDelete();
            $table->string('type'); // App\Enums\StockMovementType
            $table->integer('quantite'); // signé
            $table->string('motif')->nullable();
            $table->foreignId('order_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['inventory_id', 'type']);
            $table->index('relais_id');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('inventories');
    }
};
