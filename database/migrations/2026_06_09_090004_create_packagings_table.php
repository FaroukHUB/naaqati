<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packagings', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('type'); // App\Enums\PackagingType
            $table->unsignedBigInteger('prix')->default(0); // centimes
            $table->char('devise_code', 3)->default('DZD');
            $table->string('couleur')->nullable();
            $table->foreignId('relais_id')->nullable()->constrained('relais')->nullOnDelete(); // null = global
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['relais_id', 'actif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packagings');
    }
};
