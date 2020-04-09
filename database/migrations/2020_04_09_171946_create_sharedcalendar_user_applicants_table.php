<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSharedcalendarUserApplicantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sharedcalendar_user_applicants', function (Blueprint $table) {
            $table->uuid('calendar_id');
            $table->uuid('user_id');
            $table->timestamps();

            $table->foreign('calendar_id')->references('id')->on('shared_calendars')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->primary(['calendar_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sharedcalendar_user_applicants');
    }
}