<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('work_logs', function (Blueprint $table) {
            $table->integer('elapsed_time')->nullable()->change();                
        });
    }

    public function down()
    {
        Schema::table('work_logs', function (Blueprint $table) {        
            $table->integer('elapsed_time')->nullable(false)->change();
        });
    }
};
