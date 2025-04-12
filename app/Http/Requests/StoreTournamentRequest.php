<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTournamentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => "required|string|max:255",
            "teams" => ["required", "array", "min:4", "max:12"],
            "teams.*.name" => ["required", "string", "distinct"],
            "teams.*.power" => ["required", "integer", "between:1,100"],
        ];
    }
}
