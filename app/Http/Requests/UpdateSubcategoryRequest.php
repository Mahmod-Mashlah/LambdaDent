<?php

namespace App\Http\Requests;

use App\Traits\HttpResponses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSubcategoryRequest extends FormRequest
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
            'name' => ["required", "string", "min:3", "unique:subcategories,name"],
            'category_id' => ["required", "integer", "exists:categories,id"],
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->error($errors->messages(), "Error", 422);

        throw new HttpResponseException($response);
    }
}
