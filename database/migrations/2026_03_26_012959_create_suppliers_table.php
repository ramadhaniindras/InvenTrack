<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $row) {
            $row->id();
            $row->string('name'); // Nama Toko / PT
            $row->string('contact_person')->nullable(); // Nama Sales
            $row->string('phone'); // No WA buat kirim PO
            $row->text('address')->nullable();
            $row->string('email')->nullable();
            $row->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
