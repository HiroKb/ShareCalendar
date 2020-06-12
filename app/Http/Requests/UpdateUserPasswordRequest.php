<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdateUserPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'current_password' => [
                'required',
                function ($attribute, $value, $fail){
                    if (Auth::user()->password === null) {
                        $fail('パスワードが登録されていません');
                    }
                    if (!Hash::check($value, Auth::user()->password)) {
                        $fail('現在のパスワードが間違っています');
                    }
                }
            ],
            'new_password' => 'required|string|min:8'
        ];
    }
}
