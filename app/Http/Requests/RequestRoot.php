<?php

namespace App\Http\Requests;

use App\Http\Helpers\FailedData;
use App\Http\Helpers\MyResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;


class RequestRoot extends FormRequest
{

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException((new MyResponse(new FailedData("Validation Failed", $validator->errors()->toArray())))->get());
    }
}
