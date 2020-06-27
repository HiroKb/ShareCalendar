<?php

use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => config('test_user.id'),
            'name' => 'TestUser',
            'email' => config('test_user.email'),
            'image_path' => null,
            'email_verified_at' => \Illuminate\Support\Carbon::now(),
            'password' => \Illuminate\Support\Facades\Hash::make(config('test_user.password')),
            'provider_name' => null,
            'provider_id' => config('test_user.provider_id'),
            'provider_email' => null,
            'remember_token' => null,
            'created_at' => \Illuminate\Support\Carbon::now(),
            'updated_at' => \Illuminate\Support\Carbon::now()
        ]);
    }
}
