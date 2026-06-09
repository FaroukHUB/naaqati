<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->date('date_retrait')->nullable()->change();
            $table->string('creneau_retrait', 40)->nullable()->change();
            $table->string('recuperateur')->nullable()->after('creneau_retrait');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('recuperateur');
            // Retour en non-nullable (les valeurs nulles devront être corrigées au préalable).
            $table->date('date_retrait')->nullable(false)->change();
            $table->string('creneau_retrait', 40)->nullable(false)->change();
        });
    }
};
