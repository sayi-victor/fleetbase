<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->user()) return true;
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
            "plate_number" => "required|string|min:2|max:10|unique:vehicles",
            "vin" => "required|string|min:2|max:30|unique:vehicles",
            "make" => "required|string|max:60",
            "model" => "required|string|max:60",
            "year" => "required|numeric",
            "status" => "required|in:active,pending",
            "vehicle" => "nullable|string|exists:drivers,public_id",
        ];
    }
}
