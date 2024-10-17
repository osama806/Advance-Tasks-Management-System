<?php

namespace App\Http\Requests\Task;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UpdateTaskRequest extends FormRequest
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
     * Get errors that show from authorize
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
            'title'             =>      'nullable|string|min:2|max:100|unique:user_tasks,title',
            'description'       =>      'nullable|string|max:256',
            'priority'          =>      'nullable|numeric|min:1|max:10',
            'assign_to'         =>      'nullable|numeric|min:1|exists:users,id',
            'due_date'          =>      'nullable|date|date_format:d-m-Y H:i'
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
            'title'        => 'Task title',
            'description'  => 'Task description',
            'priority'     => 'Task priority',
            'assign_to'    => 'Assignee',
            'due_date'     => 'Due date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return array
     */
    public function messages(): array
    {
        return [
            'required'       => 'The :attribute field is required.',
            'unique'         => 'The :attribute has already been taken',
            'numeric'        => 'The :attribute must be a number.',
            'min'            => 'The :attribute field must be at least 1.',
            'max'            => 'The :attribute field must not be greater than 10.',
            'date'           => 'Please provide a valid date for the :attribute.',
            'exists'         => 'The selected user does not exist',
            'date_format'    => 'The :attribute must be in the format of "dd-mm-yyyy hh:mm" (e.g., 25-12-2024 14:00)',

        ];
    }
}
