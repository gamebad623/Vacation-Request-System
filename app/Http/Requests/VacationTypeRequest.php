<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VacationTypeRequest extends FormRequest
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
            'name' => ['required' , 'string'],
            'is_paid' => ['boolean'],
            'max_days_per_year' => ['integer' , 'min:0']
        ];
    }
}
