<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLogin;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(AdminService $adminService)
    {
        return $adminService->getIndexView();
    }

    public function registeredUsers(AdminService $adminService)
    {
        return $adminService->getRegisteredUsersView();
    }
}
