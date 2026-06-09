<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
            $table->string('adresse')->nullable()->after('password');
            $table->string('ville')->nullable()->after('adresse');
            $table->boolean('newsletter')->default(false)->after('ville');
            $table->timestamp('email_verified_at')->nullable()->after('newsletter');
            $table->rememberToken();
        });

        Schema::create('newsletter_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscriptions');
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['password', 'adresse', 'ville', 'newsletter', 'email_verified_at', 'remember_token']);
        });
    }
};
