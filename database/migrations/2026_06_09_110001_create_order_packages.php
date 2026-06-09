<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('packaging_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nom_destinataire')->nullable();
            $table->text('message_cadeau')->nullable();
            $table->unsignedBigInteger('frais_emballage')->default(0);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->index('order_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('order_package_id')->nullable()->after('order_id')
                ->constrained('order_packages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_package_id');
        });
        Schema::dropIfExists('order_packages');
    }
};
