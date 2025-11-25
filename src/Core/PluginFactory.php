<?php

declare(strict_types=1);

namespace Ehz\WcMultistepOrder\Core;

use Ehz\WcMultistepOrder\Support\Assets;
use Ehz\WcMultistepOrder\Support\MultiStepCheckout;

class PluginFactory
{
    public function make(): Plugin
    {
        $checkout = new MultiStepCheckout();
        $assets = new Assets($checkout);

        return new Plugin($checkout, $assets);
    }
}
