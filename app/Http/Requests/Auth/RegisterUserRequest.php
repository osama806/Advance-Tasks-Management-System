<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ResponseTrait;

class RegisterUserRequest extends FormRequest
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
            'name'      =>      'required|string|max:100',
            'email'     =>      'required|string|email|unique:users,email',
            'password'  =>      'required|string|confirmed|min:8|max:15',
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
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name'     => 'Full Name',
            'email'    => 'Email Address',
            'password' => 'Password',
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return array
     */
    public function messages(): array
    {
        return [
            'required'      => 'The :attribute is required.',
            'string'        => 'The :attribute must be a valid string.',
            'max'           => 'The :attribute must not exceed :max characters.',
            'email'         => 'Please provide a valid :attribute.',
            'unique'        => 'This :attribute is already taken',
            'confirmed'     => ':attribute confirmation does not match.',
            'min'           => 'The :attribute must be at least :min characters long.',
        ];
    }
}
