<?php

namespace App\Console\Commands;

use App\models\EmailUpdate;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredEmailUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:expired_email_updates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '期限切れのメールアドレス変更リクエスト(email_updatesテーブル内)を削除';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $anHourAgo = Carbon::now()->subHour();
        EmailUpdate::where('created_at', '<', $anHourAgo)->delete();
    }
}
