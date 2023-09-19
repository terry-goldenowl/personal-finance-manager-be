<?php

namespace App\Http\Requests\Plans;

use App\Http\Helpers\ReturnType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class CreateMonthPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'month' => 'numeric|required',
            'year' => 'numeric|required',
            'amount' => 'numeric|required',
            'wallet_id' => 'numeric|required'
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
