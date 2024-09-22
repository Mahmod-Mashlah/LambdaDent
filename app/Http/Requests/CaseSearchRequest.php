<?php

namespace App\Http\Requests;

use App\Traits\HttpResponses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class CaseSearchRequest extends FormRequest
{
    use HttpResponses;
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

            'client_name' => ["string", "min:3", "max:15"],
            'patient_name' => ["string", "min:3", "max:70"],
            'expected_delivery_date' => ["date"],
            // 'status' => ["string", "in:accepted,in progress,ready,pending,delivered"],
            'status' => ["integer", "between:0,4"],
            'confirm_delivery' => ["boolean"],
            'created_date' => ['date'],

        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->error($errors->messages(), "Error", 422);

        throw new HttpResponseException($response);
    }
}
