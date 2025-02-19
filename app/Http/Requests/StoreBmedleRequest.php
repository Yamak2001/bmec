<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBmedleRequest extends FormRequest
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
            'bmec_term_id' => [
                'required',
                'integer',
                'exists:bmec_worlde_terms,id'
            ],
            'bmec_term' => [
                'required',
                'string',
                // Maybe ensure length is 5 if your terms are always 5 letters.
            ],
            'bmedle_day' => [
                'required',
                'string',
                // or 'date' if you store an actual date format
            ],
            // Potential extra rules if you accept more fields in the "store" flow
            // e.g. 'hint_used' => 'boolean',
        ];
    }
}
