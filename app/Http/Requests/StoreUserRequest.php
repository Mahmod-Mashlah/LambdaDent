<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; // 😎😎😎😎😎😎😎😎
use Illuminate\Http\Exceptions\HttpResponseException; // 😎😎😎😎😎😎😎😎
use APP\Traits\HttpResponses; // 😎😎😎😎😎😎😎😎

class StoreUserRequest extends FormRequest
{
    use HttpResponses; // this is my trait

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
            'first_name' => ['required', 'string', 'min:3', 'max:30'],
            'last_name' => ['required', 'string', 'min:3', 'max:30'],
            'phone' => ['required', 'string', 'size:10', 'regex:/^[0-9]+$/', 'starts_with:09', 'unique:users,phone'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::defaults() /*be sure that minimum size of the password is 6 not 8 in Illuminate\Validation\Rules\Password  */
            ],
        ];
    }

    // Make response as trait response😎😎😎😎😎😎😎😎// 😎😎😎😎😎😎😎😎
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->error($errors->messages(), "Register failed !", 422);

        throw new HttpResponseException($response);
    }
    // 😎😎😎😎😎😎😎😎// 😎😎😎😎😎😎😎😎
}
