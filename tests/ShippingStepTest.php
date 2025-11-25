<?php

declare(strict_types=1);

use Brain\Monkey\Functions;
use Ehz\WcMultistepOrder\Support\ShippingStep;

beforeEach(function () {
    $_POST = [];
    Functions\when('esc_html__')->returnArg(0);
    Functions\when('wc_clean')->returnArg(0);
});

it('registers checkout hooks', function () {
    $shipping = new ShippingStep();

    Functions\expect('add_action')
        ->once()
        ->with('woocommerce_review_order_after_shipping', [$shipping, 'renderFields']);

    Functions\expect('add_action')
        ->once()
        ->with('woocommerce_checkout_process', [$shipping, 'validateSubmission']);

    $shipping->register();
});

it('accepts valid hungarian mobile numbers for Foxpost lockers', function () {
    $shipping = new ShippingStep();

    $_POST['shipping_method'] = ['0' => 'foxpost_automata:1'];
    $_POST['ehz_shipping_notification_phone'] = '0036 20 1234567';

    Functions\expect('wc_add_notice')->never();

    $shipping->validateSubmission();

    expect($_POST['shipping_phone'])->toBe('+36201234567');
});

it('rejects invalid Foxpost notification numbers', function () {
    $shipping = new ShippingStep();

    $_POST['shipping_method'] = ['0' => 'foxpost_automat:1'];
    $_POST['ehz_shipping_notification_phone'] = '12345';

    Functions\expect('wc_add_notice')
        ->once()
        ->with(
            esc_html__('Kérjük, adjon meg egy magyar 20-as, 30-as vagy 70-es számot Foxpost értesítéshez.', 'ehz-wc-multistep-order'),
            'error'
        );

    $shipping->validateSubmission();
});

it('requires address details for Posta methods', function () {
    $shipping = new ShippingStep();

    $_POST['shipping_method'] = ['0' => 'posta:2'];

    Functions\expect('wc_add_notice')
        ->once()
        ->with(
            esc_html__('Kérjük, adja meg a szállítási címét.', 'ehz-wc-multistep-order'),
            'error'
        );

    $shipping->validateSubmission();
});
