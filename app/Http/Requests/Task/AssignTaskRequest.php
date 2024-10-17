<?php

namespace App\Http\Requests\Task;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class AssignTaskRequest extends FormRequest
{
    use ResponseTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role !== null;
    }

    /**
     * Handle failed authorization.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    public function failedAuthorization()
    {
        throw new HttpResponseException($this->getResponse('error', 'This action is unauthorized.', 401));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'assign_to' => 'required|numeric|min:1|exists:users,id',
            'due_date'  => 'required|date|date_format:d-m-Y H:i'
        ];
    }

    /**
     * Handle failed validation.
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
            'assign_to' => 'Assignee',
            'due_date'  => 'Due date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return array
     */
    public function messages(): array
    {
        return [
            'required'    => 'The :attribute field is required.',
            'numeric'     => 'The :attribute must be a number.',
            'min'         => 'The :attribute field must be at least :min.',
            'exists'      => 'The selected user does not exist',
            'date'        => 'Please provide a valid date for the :attribute.',
            'date_format' => 'Please provide a valid date format for the :attribute. Expected format: d-m-Y H:i.',
        ];
    }
}
