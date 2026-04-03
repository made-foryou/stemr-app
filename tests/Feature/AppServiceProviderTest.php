<?php

use App\Providers\AppServiceProvider;
use Illuminate\Validation\Rules\Password;

test('password defaults enforce minimum 8 characters in non-production', function () {
    $rule = Password::default();

    expect($rule)->toBeInstanceOf(Password::class);

    $validator = validator(['password' => 'short'], ['password' => $rule]);
    expect($validator->fails())->toBeTrue();

    $validator = validator(['password' => 'longpassword'], ['password' => $rule]);
    expect($validator->fails())->toBeFalse();
});

test('password defaults enforce strict rules in production', function () {
    app()->detectEnvironment(fn () => 'production');

    // Re-bootstrap to trigger production defaults
    (new AppServiceProvider(app()))->boot();

    $rule = Password::default();

    // Short password should fail
    $validator = validator(['password' => 'short'], ['password' => $rule]);
    expect($validator->fails())->toBeTrue();

    // Simple 8 char password should fail (missing mixed case, symbols, etc.)
    $validator = validator(['password' => 'password'], ['password' => $rule]);
    expect($validator->fails())->toBeTrue();
});
