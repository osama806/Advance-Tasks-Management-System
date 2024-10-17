<?php

namespace App\Http\Requests\Task;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FilterTaskRequest extends FormRequest
{
    use ResponseTrait;
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
            "priority"      =>      'nullable|numeric|min:1|max:10',
            "status"        =>      'nullable|string|in:pending,in-progress,done'
        ];
    }

    /**
     * Get message that errors explanation
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException($this->getResponse('errors', $validator->errors(), 422));
    }

    /**
     * Get custom attributes for validator errors.
     * @return string[]
     */
    public function attributes()
    {
        return [
            "priority"      =>      'Task priority',
            "status"        =>      'Task status'
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return string[]
     */
    public function messages()
    {
        return [
            "numeric" => 'The :attribute must be a number.',
            "string"  => 'The :attribute must be a string.',
            "in"      => 'The selected :attribute is invalid.',
            "min"     => 'The :attribute field must be at least :min.',
            'max'     => 'The :attribute must not exceed :max characters.',
        ];
    }
}
