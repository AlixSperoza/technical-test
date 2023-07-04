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
        Schema::create('tracker_history', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tracker_id');
            $table->index('tracker_id');

            $table->decimal('latitude', 11, 8);
            $table->decimal('longitude', 11, 8);

            $table->json('sensors');
            $table->json('battery');
            $table->string('strength_signal', 8)->nullable();

            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracker_history');
    }
};
