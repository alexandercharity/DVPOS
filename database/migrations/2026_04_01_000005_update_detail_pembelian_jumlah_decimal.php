<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_pembelian', function (Blueprint $table) {
            $table->decimal('jumlah', 10, 3)->change();
        });
    }

    public function down(): void
    {
        Schema::table('detail_pembelian', function (Blueprint $table) {
            $table->integer('jumlah')->change();
        });
    }
};
