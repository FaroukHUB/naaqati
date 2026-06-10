<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('a_l_appoint')->default(true)->after('recuperateur');
            $table->unsignedBigInteger('paie_avec')->nullable()->after('a_l_appoint'); // centimes
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['a_l_appoint', 'paie_avec']);
        });
    }
};
