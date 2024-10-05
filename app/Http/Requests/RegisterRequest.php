<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
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
            "email" => "required|email|unique:users,email",
            "password" => "required|min:8",
            "name" => "required",
        ];
    }

    public function messages(): array
    {
        return [
            "email.required" => "Email is required",
            "email.email" => "Email is not valid",
            "password.required" => "Password is required",
            "name.required" => "Name is required",
            "email.unique"=> "Email already exists",
            "password.min"=> "Password must be at least 8 characters",
        ];
    }
}
