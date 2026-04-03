<?php

it('displays the landing page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Samen kiezen')
        ->assertSee('Maak een gratis poll');
});

it('has correct meta tags for SEO', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<meta property="og:title"', false)
        ->assertSee('<meta name="description"', false)
        ->assertSee('Vota', false);
});

it('displays the footer with Made attribution', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Gemaakt door')
        ->assertSee('https://made-foryou.nl');
});
