<?php

namespace App\Http\Requests\Chatbot;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotRequest extends FormRequest
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
            'message' => 'required|string|max:5000',
            'conversation_history' => 'nullable|array',
            'conversation_history.*.role' => 'required_with:conversation_history|string|in:user,assistant',
            'conversation_history.*.content' => 'required_with:conversation_history|string',
            'suggested_media_ids' => 'nullable|array',
            'suggested_media_ids.*' => 'string|uuid|exists:medias,id',
            'file_url' => 'nullable|string|url|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'message.required' => 'Message is required',
            'message.max' => 'Message cannot exceed 5000 characters',
            'conversation_history.array' => 'Conversation history must be an array',
            'conversation_history.*.role.in' => 'Conversation history role must be either "user" or "assistant"',
            'suggested_media_ids.array' => 'Suggested media IDs must be an array',
            'suggested_media_ids.*.uuid' => 'Each suggested media ID must be a valid UUID',
            'suggested_media_ids.*.exists' => 'One or more suggested media IDs do not exist',
            'file_url.url' => 'File URL must be a valid URL',
            'file_url.max' => 'File URL cannot exceed 2048 characters',
        ];
    }
}

