<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->foreignId('kategori_bahan_baku_id')->nullable()->after('id')
                ->constrained('kategori_bahan_baku')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropForeign(['kategori_bahan_baku_id']);
            $table->dropColumn('kategori_bahan_baku_id');
        });
        Schema::dropIfExists('kategori_bahan_baku');
    }
};
