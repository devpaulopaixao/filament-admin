<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screen_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('screen_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('device_type', 20)->default('desktop'); // desktop | mobile | tablet
            $table->timestamps();

            $table->index(['screen_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screen_access_logs');
    }
};