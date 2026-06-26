<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('ingredients')->nullable()->after('desc_longue');
            $table->text('conseils')->nullable()->after('ingredients');
            $table->text('bienfaits')->nullable()->after('conseils');
            $table->text('precautions')->nullable()->after('bienfaits');
            $table->text('conservation')->nullable()->after('precautions');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['ingredients', 'conseils', 'bienfaits', 'precautions', 'conservation']);
        });
    }
};
