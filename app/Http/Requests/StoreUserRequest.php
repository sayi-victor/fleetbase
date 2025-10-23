<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->check()) return true;

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
            "user.name" => "required|string|max:60",
            "user.email" => "required|email|max:60|unique:users,email",
            "user.role_uuid" => "required|exists:roles,id",
            "user.phone_number" => "nullable|string|min:10|max:15"
        ];
    }

    public function messages() :array
    {
        return [
            "user.name.required" => "Place enter user's name",
            "user.name.string" => "Please enter a valid name",
            "user.name.max" => "Name cannot exceed 60 characters",
            "user.email.required" => "Please enter the user's email",
            "user.email.email" => "Please enter a valid email",
            "user.email.max" => "Email cannot exceed 60 characters",
            "user.email.unique" => "This email already exists",
            "user.role_uuid.required" => "Please enter user's role",
            "user.role_uuid.exists" => "Role does not exist",
            "user.phone_number.string" => "Please enter a valid phone number",
            "user.phone_number.min" => "Phone number cannot be less than 10 characters",
            "user.phone_number.max" => "Phone number cannot exceed 15 characters"
        ];
    }
}
