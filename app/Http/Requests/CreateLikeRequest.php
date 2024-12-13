<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLikeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->check()) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "post_id" => "required|exists:posts,id",
            "user_id" => "required|exists:users,id",
        ];
    }

    public function messages(): array
    {
        return [
            "post_id.required" => "The post id field is required.",
            "post_id.exists" => "The post id field does not exist.",
            "user_id.required" => "The user id field is required.",
            "user_id.exists" => "The user id field does not exist.",
        ];
    }
}
