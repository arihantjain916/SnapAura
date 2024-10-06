<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }
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
            "image" => "required|image|mimes:jpeg,png,jpg,gif|max:2048",
            "caption" => "required|string|max:255",
        ];
    }

    public function messages(): array
    {
        return [
            "image.required" => "An image is required",
            "image.image" => "The image must be an image",
            "image.mimes" => "The image must be a jpeg, png, jpg, or gif",
            "image.max" => "The image must not be greater than 2MB",
            "caption.required" => "Caption is required",
        ];
    }
}
