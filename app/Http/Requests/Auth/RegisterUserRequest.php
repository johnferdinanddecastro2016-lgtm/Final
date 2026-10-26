<?php

namespace App\Http\Requests\Auth;

use App\Models\Workspace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->input('account_type') !== 'council'
                || ! $this->filled('province')
                || ! $this->filled('municipality')
                || ! $this->filled('barangay')) {
                return;
            }

            $province = $this->input('province');
            $municipality = $this->input('municipality');
            $barangay = $this->input('barangay');
            if (! is_string($province) || ! is_string($municipality) || ! is_string($barangay)) {
                return;
            }

            if (Workspace::where(
                'council_identifier',
                Workspace::identifierFor(
                    $province,
                    $municipality,
                    $barangay
                )
            )->exists()) {
                $validator->errors()->add('barangay', 'A workspace is already registered for this province, municipality, and barangay.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $email = $this->input('email');
        if (is_string($email)) {
            $this->merge(['email' => mb_strtolower(trim($email))]);
        }
    }

    public function rules(): array
    {
        $rules = [
            'account_type' => ['required', 'in:participant,council'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', 'min:12'],
            'phone' => ['nullable', 'string', 'max:30'],
            'terms' => ['accepted'],
        ];

        if ($this->input('account_type') === 'council') {
            $rules += [
                'province' => ['required', 'string', 'max:120'],
                'municipality' => ['required', 'string', 'max:120'],
                'barangay' => ['required', 'string', 'max:120'],
                'sk_position' => ['required', 'string', 'max:120'],
                'council_name' => ['required', 'string', 'max:255'],
            ];
        }

        return $rules;
    }
}
