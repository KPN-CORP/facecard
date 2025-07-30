<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('roles', function (Blueprint $table) {
        // Tambahkan kolom baru setelah kolom 'name'
        // Dibuat nullable() karena nilainya boleh kosong (artinya "berlaku untuk semua")
        $table->string('business_unit')->nullable()->after('name');
        $table->string('company')->nullable()->after('business_unit');
        $table->string('location')->nullable()->after('company');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            //
        });
    }
};
