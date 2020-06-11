<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessages20200510 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('calendar_id');
            $table->uuid('posted_user_id')->nullable();
            $table->string('message', 1000);
            $table->timestamps();

            $table->foreign('calendar_id')->references('id')->on('shared_calendars')->onDelete('cascade');
            $table->foreign('posted_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
}
