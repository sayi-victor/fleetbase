<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverRequest extends FormRequest
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
            "name" => "required|string|max:60",
            "email" => "required|email|max:60|unique:users,email",
            "password" => "required|confirmed|string|min:8",
            "license" => "nullable|string|max:60",
            "vehicle" => "nullable|exists:vehicles,uuid"
        ];
    }
}
