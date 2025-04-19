<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('work_logs', function (Blueprint $table) {
        $table->id();
        $table->text('work_description')->nullable(); 
        $table->integer('elapsed_time');
        $table->timestamps();
        $table->timestamp('start_time')->nullable();
        $table->timestamp('end_time')->nullable();
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_logs');
    }
};
