<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('panels', function (Blueprint $table) {
            $table->boolean('blocked')->default(false)->after('show_title');
            $table->string('password')->nullable()->after('blocked');
        });
    }

    public function down(): void
    {
        Schema::table('panels', function (Blueprint $table) {
            $table->dropColumn(['blocked', 'password']);
        });
    }
};
