<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSharedCalendars20200331 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shared_calendars', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('search_id')->unique();
            $table->uuid('admin_id');
            $table->string('calendar_name');
            $table->string('image_path')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shared_calendars');
    }
}
