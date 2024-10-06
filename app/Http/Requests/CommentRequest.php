<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
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
            "comment" => "required|string|max:255",
            "post_id" => "required|exists:posts,id",
        ];
    }

    public function messages(): array
    {
        return [
            "comment.required" => "Comment is required",
            "comment.string" => "Comment must be a string",
            "comment.max" => "Comment must be less than 255 characters",
            "post_id.required" => "Post id is required",
            "post_id.exists" => "Post id does not exist",
        ];
    }
}
