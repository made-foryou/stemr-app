<?php

use App\Models\User;
use Laravel\Socialite\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

test('google redirect route redirects to google', function () {
    Socialite::fake('google');

    $response = $this->get(route('auth.google.redirect'));

    $response->assertRedirect();
});

test('google callback creates new user and logs in', function () {
    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-123',
        'name' => 'Jan Jansen',
        'email' => 'jan@example.com',
    ]));

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'name' => 'Jan Jansen',
        'email' => 'jan@example.com',
        'google_id' => 'google-123',
    ]);

    $user = User::where('email', 'jan@example.com')->first();
    expect($user->password)->toBeNull();
    expect($user->hasVerifiedEmail())->toBeTrue();
});

test('google callback logs in existing user matched by google_id', function () {
    $user = User::factory()->withGoogle()->create([
        'google_id' => 'google-456',
        'email' => 'bestaand@example.com',
    ]);

    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-456',
        'name' => $user->name,
        'email' => 'bestaand@example.com',
    ]));

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);

    expect(User::count())->toBe(1);
});

test('google callback merges account when email already exists', function () {
    $user = User::factory()->create([
        'email' => 'merge@example.com',
        'password' => 'existing-password',
        'google_id' => null,
    ]);

    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-789',
        'name' => $user->name,
        'email' => 'merge@example.com',
    ]));

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);

    $user->refresh();
    expect($user->google_id)->toBe('google-789');
    expect($user->password)->not->toBeNull();
});

test('google callback handles error gracefully', function () {
    Socialite::fake('google');

    $response = $this->get(route('auth.google.callback').'?error=access_denied');

    $response->assertRedirect(route('login'));
});

test('authenticated user can link google account', function () {
    $user = User::factory()->create(['google_id' => null]);

    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-link-123',
        'name' => $user->name,
        'email' => $user->email,
    ]));

    $response = $this->actingAs($user)->get(route('auth.google.callback'));

    $response->assertRedirect(route('account.settings'));

    $user->refresh();
    expect($user->google_id)->toBe('google-link-123');
});

test('authenticated user cannot link google account already used by another user', function () {
    $otherUser = User::factory()->create(['google_id' => 'google-taken']);
    $user = User::factory()->create(['google_id' => null]);

    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-taken',
        'name' => $user->name,
        'email' => $user->email,
    ]));

    $response = $this->actingAs($user)->get(route('auth.google.callback'));

    $response->assertRedirect(route('account.settings'));

    $user->refresh();
    expect($user->google_id)->toBeNull();
});
