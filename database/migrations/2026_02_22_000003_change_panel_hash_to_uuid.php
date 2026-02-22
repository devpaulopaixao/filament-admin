<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Expandir a coluna primeiro (CHAR(36) aceita o UUID inteiro)
        DB::statement('ALTER TABLE panels MODIFY COLUMN hash CHAR(36) NOT NULL');

        // 2. Actualizar registos existentes com UUIDs
        foreach (DB::table('panels')->get() as $panel) {
            DB::table('panels')
                ->where('id', $panel->id)
                ->update(['hash' => (string) Str::uuid()]);
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE panels MODIFY COLUMN hash VARCHAR(5) NOT NULL');
    }
};