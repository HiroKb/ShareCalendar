<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserNameRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function updateName(UpdateUserNameRequest $request)
    {

        $user = Auth::user();

        $user->name = $request->name;

        $user->save();

        return Auth::user();
    }
}
