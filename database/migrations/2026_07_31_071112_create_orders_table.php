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
    Schema::create('orders', function (Blueprint $table) {

        $table->id();

        $table->foreignId('cafe_table_id')
              ->constrained('cafe_tables')
              ->cascadeOnDelete();

        $table->string('customer_name')->nullable();

        $table->decimal('total', 10, 2)->default(0);

        $table->enum('status', [
            'pending',
            'processing',
            'completed',
            'cancelled'
        ])->default('pending');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
