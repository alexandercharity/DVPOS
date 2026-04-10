<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->decimal('stok', 10, 3)->default(0)->change();
            $table->decimal('stok_minimum', 10, 3)->default(10)->after('stok')
                ->comment('Threshold notif stok rendah');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->integer('stok')->default(0)->change();
            $table->dropColumn('stok_minimum');
        });
    }
};
