<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->boolean('is_cross_midnight')->default(false);
            $table->unsignedSmallInteger('late_tolerance_minutes')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['is_cross_midnight', 'late_tolerance_minutes']);
        });
    }
};
