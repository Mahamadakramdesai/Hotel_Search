<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:3'],
            'meal_plan' => ['nullable', 'in:room_only,breakfast']
        ];
    }

     public function messages()
    {
        return [
            'check_in.after_or_equal' => 'Check-in cannot be in the past',
            'check_out.after' => 'Check-out must be after check-in',
            'guests.max' => 'Maximum 3 guests allowed'
        ];
    }
}
