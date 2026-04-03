<?php

use App\Livewire\Actions\Logout;
use App\Models\User;

test('livewire logout action logs out and redirects', function () {
    $user = User::factory()->create();

    $this->actingAs($user);
    $this->assertAuthenticated();

    $logout = new Logout;
    $response = $logout();

    $this->assertGuest();
    expect($response->getTargetUrl())->toContain('/');
});
