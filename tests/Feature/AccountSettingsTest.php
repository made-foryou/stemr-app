<?php

use App\Livewire\AccountSettings;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('account settings page is not accessible for guests', function () {
    $this->get(route('account.settings'))->assertRedirect(route('login'));
});

test('account settings page can be rendered', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('account.settings'))->assertOk();
});

// --- Password instellen (SSO-only user) ---

test('sso user can set a password', function () {
    $user = User::factory()->withGoogle()->create();

    Livewire::actingAs($user)
        ->test(AccountSettings::class)
        ->set('password', 'newpassword')
        ->set('password_confirmation', 'newpassword')
        ->call('updatePassword')
        ->assertHasNoErrors()
        ->assertDispatched('password-updated');

    expect(Hash::check('newpassword', $user->fresh()->password))->toBeTrue();
});

test('password must be at least 8 characters', function () {
    $user = User::factory()->withGoogle()->create();

    Livewire::actingAs($user)
        ->test(AccountSettings::class)
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('updatePassword')
        ->assertHasErrors(['password']);
});

test('password confirmation must match', function () {
    $user = User::factory()->withGoogle()->create();

    Livewire::actingAs($user)
        ->test(AccountSettings::class)
        ->set('password', 'newpassword')
        ->set('password_confirmation', 'different')
        ->call('updatePassword')
        ->assertHasErrors(['password']);
});

// --- Wachtwoord wijzigen (user met bestaand wachtwoord) ---

test('user with password must provide current password', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(AccountSettings::class)
        ->set('password', 'newpassword')
        ->set('password_confirmation', 'newpassword')
        ->call('updatePassword')
        ->assertHasErrors(['current_password']);
});

test('wrong current password is rejected', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(AccountSettings::class)
        ->set('current_password', 'wrong-password')
        ->set('password', 'newpassword')
        ->set('password_confirmation', 'newpassword')
        ->call('updatePassword')
        ->assertHasErrors(['current_password']);
});

test('correct current password allows password change', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(AccountSettings::class)
        ->set('current_password', 'password')
        ->set('password', 'newpassword')
        ->set('password_confirmation', 'newpassword')
        ->call('updatePassword')
        ->assertHasNoErrors()
        ->assertDispatched('password-updated');

    expect(Hash::check('newpassword', $user->fresh()->password))->toBeTrue();
});

// --- Google loskoppelen ---

test('user with password can disconnect google', function () {
    $user = User::factory()->create(['google_id' => 'google-123']);

    Livewire::actingAs($user)
        ->test(AccountSettings::class)
        ->call('disconnectGoogle')
        ->assertHasNoErrors()
        ->assertDispatched('provider-disconnected');

    expect($user->fresh()->google_id)->toBeNull();
});

test('user with apple linked can disconnect google without password', function () {
    $user = User::factory()->withGoogle()->create(['apple_id' => 'apple-123']);

    Livewire::actingAs($user)
        ->test(AccountSettings::class)
        ->call('disconnectGoogle')
        ->assertHasNoErrors();

    expect($user->fresh()->google_id)->toBeNull();
});

test('user with only google and no password cannot disconnect', function () {
    $user = User::factory()->withGoogle()->create();

    Livewire::actingAs($user)
        ->test(AccountSettings::class)
        ->call('disconnectGoogle')
        ->assertHasErrors(['google']);
});

// --- Apple loskoppelen ---

test('user with password can disconnect apple', function () {
    $user = User::factory()->create(['apple_id' => 'apple-123']);

    Livewire::actingAs($user)
        ->test(AccountSettings::class)
        ->call('disconnectApple')
        ->assertHasNoErrors()
        ->assertDispatched('provider-disconnected');

    expect($user->fresh()->apple_id)->toBeNull();
});

test('user with google linked can disconnect apple without password', function () {
    $user = User::factory()->withApple()->create(['google_id' => 'google-123']);

    Livewire::actingAs($user)
        ->test(AccountSettings::class)
        ->call('disconnectApple')
        ->assertHasNoErrors();

    expect($user->fresh()->apple_id)->toBeNull();
});

test('user with only apple and no password cannot disconnect', function () {
    $user = User::factory()->withApple()->create();

    Livewire::actingAs($user)
        ->test(AccountSettings::class)
        ->call('disconnectApple')
        ->assertHasErrors(['apple']);
});

// --- User model helpers ---

test('hasPassword returns correct value', function () {
    $userWithPassword = User::factory()->create();
    $userWithoutPassword = User::factory()->withGoogle()->create();

    expect($userWithPassword->hasPassword())->toBeTrue();
    expect($userWithoutPassword->hasPassword())->toBeFalse();
});

test('hasGoogleLinked returns correct value', function () {
    $userWithGoogle = User::factory()->create(['google_id' => 'google-123']);
    $userWithoutGoogle = User::factory()->create();

    expect($userWithGoogle->hasGoogleLinked())->toBeTrue();
    expect($userWithoutGoogle->hasGoogleLinked())->toBeFalse();
});

test('hasAppleLinked returns correct value', function () {
    $userWithApple = User::factory()->create(['apple_id' => 'apple-123']);
    $userWithoutApple = User::factory()->create();

    expect($userWithApple->hasAppleLinked())->toBeTrue();
    expect($userWithoutApple->hasAppleLinked())->toBeFalse();
});

test('canDisconnectProvider returns correct value', function () {
    // User with only Google, no password → cannot disconnect
    $googleOnly = User::factory()->withGoogle()->create();
    expect($googleOnly->canDisconnectProvider('google'))->toBeFalse();

    // User with Google + password → can disconnect
    $googleWithPassword = User::factory()->create(['google_id' => 'google-123']);
    expect($googleWithPassword->canDisconnectProvider('google'))->toBeTrue();

    // User with Google + Apple, no password → can disconnect Google
    $googleAndApple = User::factory()->withGoogle()->create(['apple_id' => 'apple-123']);
    expect($googleAndApple->canDisconnectProvider('google'))->toBeTrue();

    // Unknown provider → false
    expect($googleOnly->canDisconnectProvider('facebook'))->toBeFalse();
});
