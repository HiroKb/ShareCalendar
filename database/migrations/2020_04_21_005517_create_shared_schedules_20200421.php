<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSharedSchedules20200421 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shared_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('calendar_id');
            $table->string('title', 50);
            $table->string('description', 100)->nullable();
            $table->date('date');
            $table->time('time')->nullable();
            $table->timestamps();

            $table->foreign('calendar_id')->references('id')->on('shared_calendars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shared_schedules');
    }
}
