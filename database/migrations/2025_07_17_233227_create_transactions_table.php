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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('fish_id')->constrained('fish')->cascadeOnDelete();
            $table->string('origin'); // asal ikan
            $table->decimal('quantity', 8, 2)->default(0); // berat atau jumlah
            $table->decimal('price_per_unit', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0); // quantity * price_per_unit
            $table->dateTime('entry_datetime');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
