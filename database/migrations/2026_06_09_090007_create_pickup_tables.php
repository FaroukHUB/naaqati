<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relais_id')->constrained('relais')->cascadeOnDelete();
            $table->unsignedTinyInteger('jour_semaine'); // 0=dimanche … 6=samedi
            $table->json('creneaux'); // ["matin","apres_midi"] ou ["09:00","10:00"]
            $table->unsignedSmallInteger('capacite_max')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->unique(['relais_id', 'jour_semaine']);
        });

        Schema::create('schedule_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relais_id')->constrained('relais')->cascadeOnDelete();
            $table->date('date');
            $table->string('creneau', 40)->nullable(); // null = toute la journée
            $table->string('type')->default('bloque'); // bloque | ferme
            $table->string('motif')->nullable();
            $table->timestamps();
            $table->index(['relais_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_exceptions');
        Schema::dropIfExists('pickup_schedules');
    }
};
