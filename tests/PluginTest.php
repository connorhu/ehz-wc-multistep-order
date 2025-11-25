<?php

declare(strict_types=1);

use Brain\Monkey\Functions;
use Ehz\WcMultistepOrder\Core\Plugin;
use Ehz\WcMultistepOrder\Support\Assets;
use Ehz\WcMultistepOrder\Support\MultiStepCheckout;
use Mockery as m;

it('registers the bootstrap hook', function () {
    $checkout = m::mock(MultiStepCheckout::class);
    $assets = m::mock(Assets::class);
    $plugin = new Plugin($checkout, $assets);

    Functions\expect('add_action')
        ->once()
        ->with('plugins_loaded', [$plugin, 'boot']);

    $plugin->register();
});

it('boots only when WooCommerce is present', function () {
    $checkout = m::mock(MultiStepCheckout::class);
    $assets = m::mock(Assets::class);
    $plugin = new Plugin($checkout, $assets);

    Functions\when('class_exists')->justReturn(false);

    $checkout->shouldNotReceive('register');
    $assets->shouldNotReceive('register');

    $plugin->boot();
});
