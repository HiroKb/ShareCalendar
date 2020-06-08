<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUsers20200608 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table){
            $table->string('email')->nullable()->change();

            $table->string('provider_name')->nullable();
            $table->string('provider_id')->nullable();
            $table->unique(['provider_id', 'provider_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table){
            $table->string('email')->nullable(false)->change();

            $table->dropUnique(['provider_id', 'provider_name']);
            $table->dropColumn('provider_name');
            $table->dropColumn('provider_id');
        });
    }
}
