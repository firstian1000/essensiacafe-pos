<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE orders ALTER COLUMN cafe_table_id DROP NOT NULL');
        } elseif (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE orders MODIFY cafe_table_id BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE orders ALTER COLUMN cafe_table_id SET NOT NULL');
        } elseif (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE orders MODIFY cafe_table_id BIGINT UNSIGNED NOT NULL');
        }
    }
};
