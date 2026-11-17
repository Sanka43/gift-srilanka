<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL only: ensure carts.id is AUTO_INCREMENT (fixes "Field 'id' doesn't have a default value")
        if (DB::getDriverName() !== 'mysql') {
            return;
        }
        DB::statement('ALTER TABLE carts MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }
        DB::statement('ALTER TABLE carts MODIFY id BIGINT UNSIGNED NOT NULL');
    }
};
