<?php

use App\Models\User;
use Laravel\Socialite\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

test('apple redirect route redirects to apple', function () {
    Socialite::fake('apple');

    $response = $this->get(route('auth.apple.redirect'));

    $response->assertRedirect();
});

test('apple callback creates new user and logs in', function () {
    Socialite::fake('apple', (new SocialiteUser)->map([
        'id' => 'apple-123',
        'name' => 'Jan Jansen',
        'email' => 'jan@example.com',
    ]));

    $response = $this->post(route('auth.apple.callback'));

    $response->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'name' => 'Jan Jansen',
        'email' => 'jan@example.com',
        'apple_id' => 'apple-123',
    ]);

    $user = User::where('email', 'jan@example.com')->first();
    expect($user->password)->toBeNull();
    expect($user->hasVerifiedEmail())->toBeTrue();
});

test('apple callback logs in existing user matched by apple_id', function () {
    $user = User::factory()->withApple()->create([
        'apple_id' => 'apple-456',
        'email' => 'bestaand@example.com',
    ]);

    Socialite::fake('apple', (new SocialiteUser)->map([
        'id' => 'apple-456',
        'name' => $user->name,
        'email' => 'bestaand@example.com',
    ]));

    $response = $this->post(route('auth.apple.callback'));

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);

    expect(User::count())->toBe(1);
});

test('apple callback merges account when email already exists', function () {
    $user = User::factory()->create([
        'email' => 'merge@example.com',
        'password' => 'existing-password',
        'apple_id' => null,
    ]);

    Socialite::fake('apple', (new SocialiteUser)->map([
        'id' => 'apple-789',
        'name' => $user->name,
        'email' => 'merge@example.com',
    ]));

    $response = $this->post(route('auth.apple.callback'));

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);

    $user->refresh();
    expect($user->apple_id)->toBe('apple-789');
    expect($user->password)->not->toBeNull();
});

test('apple callback handles error gracefully', function () {
    Socialite::fake('apple');

    $response = $this->post(route('auth.apple.callback'), ['error' => 'access_denied']);

    $response->assertRedirect(route('login'));
});

test('authenticated users cannot access apple redirect', function () {
    $user = User::factory()->create();

    Socialite::fake('apple');

    $response = $this->actingAs($user)->get(route('auth.apple.redirect'));

    $response->assertRedirect(route('dashboard'));
});

test('apple callback uses fallback name when name is null', function () {
    Socialite::fake('apple', (new SocialiteUser)->map([
        'id' => 'apple-no-name',
        'name' => null,
        'email' => 'naamloos@privaterelay.appleid.com',
    ]));

    $response = $this->post(route('auth.apple.callback'));

    $response->assertRedirect(route('dashboard'));
    $this->assertDatabaseHas('users', [
        'name' => 'Apple User',
        'email' => 'naamloos@privaterelay.appleid.com',
        'apple_id' => 'apple-no-name',
    ]);
});
