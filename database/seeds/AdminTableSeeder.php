<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
            'name' => config('admin_user.name'),
            'password' => \Illuminate\Support\Facades\Hash::make(config('admin_user.password')),
        ]);
    }
}
