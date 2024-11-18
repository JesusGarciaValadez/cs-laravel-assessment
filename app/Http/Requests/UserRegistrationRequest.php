<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:3',
            'email' => [
                'required',
                'email',
                Rule::notIn(['manager@controlfreak.com']),
            ],
            'username' => [
                'required',
                'min:4',
                'unique:users,username',
                'not_regex:/(brian)/i',
            ],
            'password' => 'required|min:10|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[@$!%^*#?&]/',
        ];
    }
}
