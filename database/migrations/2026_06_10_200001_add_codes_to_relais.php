<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('relais', function (Blueprint $table) {
            $table->string('code_porte')->nullable()->after('code_portail');
            $table->string('code_ascenseur')->nullable()->after('code_porte');
        });
    }

    public function down(): void
    {
        Schema::table('relais', function (Blueprint $table) {
            $table->dropColumn(['code_porte', 'code_ascenseur']);
        });
    }
};
