<?php

declare(strict_types=1);

use Brain\Monkey\Functions;
use Ehz\WcMultistepOrder\Support\MultiStepCheckout;

it('registers checkout hooks', function () {
    $checkout = new MultiStepCheckout();

    Functions\expect('add_action')
        ->once()
        ->with('woocommerce_checkout_before_customer_details', [$checkout, 'renderProgress'], 5);

    Functions\expect('add_filter')
        ->once()
        ->with('ehz_wc_multistep_steps', [$checkout, 'provideSteps'])
        ->andReturnTrue();

    Functions\expect('add_filter')
        ->once()
        ->with('woocommerce_available_payment_gateways', [$checkout, 'exposeStepAwareGateways'])
        ->andReturnTrue();

    $checkout->register();
});

it('enriches steps with positions', function () {
    $checkout = new MultiStepCheckout();

    $steps = $checkout->provideSteps([
        ['id' => 'first', 'label' => 'First'],
        ['id' => 'second', 'label' => 'Second'],
    ]);

    expect($steps[0]['position'])->toBe(1);
    expect($steps[1]['position'])->toBe(2);
});

it('adds multistep support flag to gateways', function () {
    $checkout = new MultiStepCheckout();

    $gateway = (object) ['supports' => []];
    $processed = $checkout->exposeStepAwareGateways(['cod' => $gateway]);

    expect($processed['cod']->supports)->toContain('ehz-multistep');
});
