<?php


namespace App\Services;


use App\Models\AdminLogin;
use App\Models\User;

class AdminService
{
    public function getIndexView()
    {
        return view('admin.index')->with('loginHistories', AdminLogin::latest()->get());
    }

    public function getRegisteredUsersView()
    {
        return view('admin.registered_users')->with('users', User::latest()->paginate(20));
    }

}