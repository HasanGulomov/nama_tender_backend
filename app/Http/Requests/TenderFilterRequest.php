<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenderFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
{
    return [
        'category'     => 'nullable|string',
        'min_budget'   => 'nullable|numeric', // Alohida kelsa
        'max_budget'   => 'nullable|numeric', // Alohida kelsa
        'closingDate'  => 'nullable|date', 
        'region'       => 'nullable|string',
        'source'       => 'nullable|string',
    ];
}
}