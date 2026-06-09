<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('telephone', 30)->unique();
            $table->string('email', 190)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('total_depense')->default(0); // centimes
            $table->unsignedInteger('nb_commandes')->default(0);
            $table->dateTime('derniere_commande_at')->nullable();
            $table->timestamps();
            $table->index('telephone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
