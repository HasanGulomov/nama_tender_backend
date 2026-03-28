<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenderFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Shartli ravishda true qilamiz
    }

    public function rules(): array
    {
        return [
            'category'   => 'nullable|string',
            'location'   => 'nullable|string',
            'deadline'   => 'nullable|date',
            'min_budget' => 'nullable|numeric',
            'max_budget' => 'nullable|numeric',
        ];
    }
}