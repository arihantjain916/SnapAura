<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentReplyRequest extends FormRequest
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
            "parent_id" => "required|exists:comments,id",
        ];
    }

    public function messages(): array
    {
        return [
            "comment.required" => "Comment is required",
            "comment.string" => "Comment must be a string",
            "comment.max" => "Comment must be less than 255 characters",
            "parent_id.required" => "Parent ID is required",
            "parent_id.exists" => "Parent ID must exist in the comments table",
        ];
    }
}
