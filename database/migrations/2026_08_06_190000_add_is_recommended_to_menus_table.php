<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('menus', 'is_recommended')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->boolean('is_recommended')->default(false)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('menus', 'is_recommended')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropColumn('is_recommended');
            });
        }
    }
};
