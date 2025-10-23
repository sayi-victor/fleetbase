<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFuelReportRequest extends FormRequest
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
            "vehicle" => "required|exists:vehicles,public_id",
            "driver" => "nullable|exists:drivers,public_id",
            "amount" => "required|numeric|min:1",
            "volume" => "required|numeric|min:1",
            "odometer" => "required|numeric|min:0",
            "status" => "required|in:draft,approved,revised",
            "location" => "required|string|max:60"
        ];
    }
}
