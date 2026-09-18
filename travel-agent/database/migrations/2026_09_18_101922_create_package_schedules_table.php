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
        Schema::create('package_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_package_id')->constrained()->onDelete('cascade');
            $table->date('departure_date');
            $table->decimal('price_per_person', 12, 2);
            $table->integer('quota');
            $table->integer('remaining_quota');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_schedules');
    }
};