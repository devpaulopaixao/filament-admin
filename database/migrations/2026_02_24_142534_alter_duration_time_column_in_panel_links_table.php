<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('panel_links', function (Blueprint $table) {
            $table->string('duration_time', 8)->change(); // formato HH:MM:SS
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panel_links', function (Blueprint $table) {
            $table->string('duration_time', 5)->change(); // formato HH:MM
        });
    }
};
