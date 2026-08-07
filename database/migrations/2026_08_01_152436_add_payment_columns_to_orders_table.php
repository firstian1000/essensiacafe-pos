<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->string('phone')->nullable();

            $table->string('payment_method')->default('cash');

            $table->string('payment_status')->default('pending');

            $table->text('snap_token')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropColumn([
                'phone',
                'payment_method',
                'payment_status',
                'snap_token'
            ]);

        });
    }
};