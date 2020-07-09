<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLogin;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index')->with('loginHistories', AdminLogin::getHistories());
    }

    public function registeredUsers()
    {
        return view('admin.registered_users')->with('users', User::latest()->get());
    }
}
