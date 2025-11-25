<?php

declare(strict_types=1);

use Brain\Monkey\Functions;
use Ehz\WcMultistepOrder\Support\Assets;
use Ehz\WcMultistepOrder\Support\MultiStepCheckout;
use Mockery as m;

it('registers enqueue hook', function () {
    $checkout = m::mock(MultiStepCheckout::class);
    $assets = new Assets($checkout);

    Functions\expect('add_action')
        ->once()
        ->with('wp_enqueue_scripts', [$assets, 'enqueue'], 20);

    $assets->register();
});

it('skips assets outside checkout', function () {
    $checkout = m::mock(MultiStepCheckout::class);
    $assets = new Assets($checkout);

    Functions\when('is_checkout')->justReturn(false);

    Functions\expect('wp_register_script')->never();
    Functions\expect('wp_localize_script')->never();
    Functions\expect('wp_enqueue_script')->never();

    $assets->enqueue();
});
