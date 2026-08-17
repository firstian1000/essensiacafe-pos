<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE cafe_tables MODIFY status ENUM('available', 'occupied', 'reserved') DEFAULT 'available'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE cafe_tables MODIFY status ENUM('available', 'occupied') DEFAULT 'available'");
        }
    }
};
