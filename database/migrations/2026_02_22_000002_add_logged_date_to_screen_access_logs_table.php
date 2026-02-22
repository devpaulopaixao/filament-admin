<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adicionar como nullable para não falhar com linhas existentes
        Schema::table('screen_access_logs', function (Blueprint $table) {
            $table->date('logged_date')->nullable()->after('device_type');
        });

        // 2. Preencher linhas existentes com a data de criação
        DB::table('screen_access_logs')
            ->whereNull('logged_date')
            ->update(['logged_date' => DB::raw('DATE(created_at)')]);

        // 3. Remover duplicatas — manter apenas o registo com maior id por grupo
        DB::statement('
            DELETE a FROM screen_access_logs a
            INNER JOIN screen_access_logs b
                ON  a.screen_id   = b.screen_id
                AND a.ip_address  = b.ip_address
                AND a.device_type = b.device_type
                AND a.logged_date = b.logged_date
                AND a.id < b.id
        ');

        // 4. Tornar NOT NULL e adicionar índice único
        Schema::table('screen_access_logs', function (Blueprint $table) {
            $table->date('logged_date')->nullable(false)->change();

            // 1 registo por dia, por IP, por tipo de dispositivo, por tela
            $table->unique(['screen_id', 'ip_address', 'device_type', 'logged_date'], 'screen_access_logs_unique');
        });
    }

    public function down(): void
    {
        Schema::table('screen_access_logs', function (Blueprint $table) {
            $table->dropUnique('screen_access_logs_unique');
            $table->dropColumn('logged_date');
        });
    }
};