<?php

declare(strict_types=1);

namespace Ehz\WcMultistepOrder\Core;

use Ehz\WcMultistepOrder\Support\Assets;
use Ehz\WcMultistepOrder\Support\CartOverlay;
use Ehz\WcMultistepOrder\Support\MultiStepCheckout;
use Ehz\WcMultistepOrder\Support\ShippingStep;

class PluginFactory
{
    public function make(): Plugin
    {
        $checkout = new MultiStepCheckout();
        $cartOverlay = new CartOverlay();
        $shippingStep = new ShippingStep();
        $assets = new Assets($checkout);

        return new Plugin($checkout, $assets, $cartOverlay, $shippingStep);
    }
}
