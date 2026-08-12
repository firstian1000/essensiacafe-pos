<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('add_on_menu_id')->nullable()->after('add_on')->constrained('menus')->nullOnDelete();
            $table->integer('add_on_price')->default(0)->after('add_on_menu_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('add_on_menu_id');
            $table->dropColumn('add_on_price');
        });
    }
};
