<?php

namespace App\Http\Requests\Users;

use App\Http\Helpers\ReturnType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "email" => ['required', 'string', 'email'],
            "password" => ['required', Rules\Password::defaults()]
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ReturnType::response([
            'status' => 'fail',
            'error' => $validator->errors(),
        ]));
    }
}
