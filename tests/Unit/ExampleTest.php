<?php

use App\Models\User;

test('user initials returns first letters of name parts', function () {
    $user = new User(['name' => 'Jan Jansen']);
    expect($user->initials())->toBe('JJ');
});

test('user initials handles single name', function () {
    $user = new User(['name' => 'Jan']);
    expect($user->initials())->toBe('J');
});

test('user initials takes at most two parts', function () {
    $user = new User(['name' => 'Jan van Jansen']);
    expect($user->initials())->toBe('Jv');
});
