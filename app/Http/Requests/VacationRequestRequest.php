<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VacationRequestRequest extends FormRequest
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
            'vacation_type_id' => ['required' , 'exists:vacation_types,id'],
            'start_date' => ['required' , 'date'],
            'end_date' => ['required' , 'date'],
            'reason' => ['nullable' , 'string']
        ];
    }
}
