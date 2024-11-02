<?php

namespace App\Http\Requests;

use App\Traits\HttpResponses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckVereficationCodeRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
            'last_verification_code' => ['required', 'integer', "digits:5"],

        ];
    }

    // Make response as trait response😎😎😎😎😎😎😎😎
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->error($errors->messages(), "Register failed !", 422);

        throw new HttpResponseException($response);
    }
}
