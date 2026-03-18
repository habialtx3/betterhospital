<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BookingTransactionRequest extends FormRequest
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
        $user = Auth::user();
        $user_id = $user->id;
        return [
            //
            'user_id' => $user_id,
            'doctor_id' => 'required|exists:doctors.id',
            'status',
            'started_at' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $date = Carbon::parse($value)->startOfDay();
                    $min = now()->addDay()->startOfDay();
                    $max = now()->addDay(3)->endOfDay();

                    if ($date->lt($min) || $date->gt($max)) {
                        $fail('Tanggal konsultasi hanya boleh dipilih dari H-1 sampai H-3');
                    }
                }
            ],
            'time_at' => [
                'required',
                'date_format:H:i',
                Rule::in(['09:30', '13:00', '15:00', '17:00',])
            ],
            'proof' => 'required|image|max:2048'
        ];
    }
}
