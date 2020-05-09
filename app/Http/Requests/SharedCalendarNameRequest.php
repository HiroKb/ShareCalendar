<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SharedCalendarNameRequest extends FormRequest
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
            'calendar_name' => 'required|string|max:100'
        ];
    }

    public function attributes()
    {
        return [
            'calendar_name' => '共有カレンダー名'
        ];
    }
}