<?php

use App\Concerns\PasswordValidationRules;

test('current password rules returns expected validation rules', function () {
    $instance = new class
    {
        use PasswordValidationRules;

        public function rules(): array
        {
            return $this->currentPasswordRules();
        }
    };

    $rules = $instance->rules();

    expect($rules)->toContain('required')
        ->toContain('string')
        ->toContain('current_password');
});
