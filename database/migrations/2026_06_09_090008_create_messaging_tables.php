<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->string('cle', 60)->unique(); // "commande_prete", "suivi_j15"
            $table->string('nom');
            $table->text('corps'); // avec {{nom}}, {{numero}}, {{adresse}}...
            $table->json('variables')->nullable();
            $table->string('type'); // pret | suivi | feedback | autre
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('message_templates')->nullOnDelete();
            $table->text('corps_rendu');
            $table->string('canal')->default('wa_link'); // wa_link | api
            $table->string('statut')->default('en_attente'); // en_attente | envoye | echoue | lu
            $table->dateTime('sent_at')->nullable();
            $table->string('erreur')->nullable();
            $table->timestamps();
            $table->index(['customer_id', 'statut']);
            $table->index('order_id');
        });

        Schema::create('followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('delai_jours'); // 15 / 30 / 45
            $table->dateTime('scheduled_at');
            $table->foreignId('template_id')->nullable()->constrained('message_templates')->nullOnDelete();
            $table->string('statut')->default('planifie'); // planifie | envoye | annule
            $table->timestamps();
            $table->index(['scheduled_at', 'statut']);
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followups');
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('message_templates');
    }
};
