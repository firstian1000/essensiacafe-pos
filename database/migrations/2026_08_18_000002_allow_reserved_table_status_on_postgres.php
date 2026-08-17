<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE cafe_tables DROP CONSTRAINT IF EXISTS cafe_tables_status_check');
            DB::statement("ALTER TABLE cafe_tables ADD CONSTRAINT cafe_tables_status_check CHECK (status IN ('available', 'occupied', 'reserved'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE cafe_tables DROP CONSTRAINT IF EXISTS cafe_tables_status_check');
            DB::statement("ALTER TABLE cafe_tables ADD CONSTRAINT cafe_tables_status_check CHECK (status IN ('available', 'occupied'))");
        }
    }
};
