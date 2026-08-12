<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('menu_variant_id')->nullable()->after('menu_id')->constrained('menu_variants')->nullOnDelete();
            $table->string('variant_name')->nullable()->after('menu_variant_id');
            $table->string('sugar_level')->nullable()->after('subtotal');
            $table->string('temperature')->nullable()->after('sugar_level');
            $table->string('ice_level')->nullable()->after('temperature');
            $table->string('add_on')->nullable()->after('ice_level');
            $table->text('note')->nullable()->after('add_on');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'sugar_level',
                'menu_variant_id',
                'variant_name',
                'temperature',
                'ice_level',
                'add_on',
                'note',
            ]);
        });
    }
};
