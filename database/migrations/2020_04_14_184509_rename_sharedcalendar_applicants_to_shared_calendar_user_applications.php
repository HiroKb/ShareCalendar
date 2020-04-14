<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameSharedcalendarApplicantsToSharedCalendarUserApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('sharedcalendar_user_applicants', 'shared_calendar_user_applications');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('shared_calendar_user_applications', 'sharedcalendar_user_applicants');
    }
}
