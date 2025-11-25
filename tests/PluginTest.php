<?php

declare(strict_types=1);

use Brain\Monkey\Functions;
use Ehz\WcMultistepOrder\Core\Plugin;
use Ehz\WcMultistepOrder\Support\Assets;
use Ehz\WcMultistepOrder\Support\CartOverlay;
use Ehz\WcMultistepOrder\Support\MultiStepCheckout;
use Ehz\WcMultistepOrder\Support\ShippingStep;
use Mockery as m;

it('registers the bootstrap hook', function () {
    $checkout = m::mock(MultiStepCheckout::class);
    $assets = m::mock(Assets::class);
    $shipping = m::mock(ShippingStep::class);
    $plugin = new Plugin($checkout, $assets, m::mock(CartOverlay::class), $shipping);

    Functions\expect('add_action')
        ->once()
        ->with('plugins_loaded', [$plugin, 'boot']);

    $plugin->register();
});

it('boots only when WooCommerce is present', function () {
    $checkout = m::mock(MultiStepCheckout::class);
    $assets = m::mock(Assets::class);
    $shipping = m::mock(ShippingStep::class);
    $plugin = new Plugin($checkout, $assets, m::mock(CartOverlay::class), $shipping);

    Functions\when('class_exists')->justReturn(false);

    $checkout->shouldNotReceive('register');
    $assets->shouldNotReceive('register');
    $shipping->shouldNotReceive('register');

    $plugin->boot();
});

it('boots all services when WooCommerce is active', function () {
    $checkout = m::mock(MultiStepCheckout::class);
    $assets = m::mock(Assets::class);
    $shipping = m::mock(ShippingStep::class);
    $plugin = new Plugin($checkout, $assets, m::mock(CartOverlay::class), $shipping);

    Functions\when('class_exists')->justReturn(true);

    $checkout->shouldReceive('register')->once();
    $assets->shouldReceive('register')->once();
    $shipping->shouldReceive('register')->once();

    $plugin->boot();
});
