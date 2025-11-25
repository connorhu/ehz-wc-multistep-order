<?php

declare(strict_types=1);

namespace Ehz\WcMultistepOrder\Core;

use Ehz\WcMultistepOrder\Support\Assets;
use Ehz\WcMultistepOrder\Support\CartOverlay;
use Ehz\WcMultistepOrder\Support\MultiStepCheckout;

class Plugin
{
    public const VERSION = '0.1.0';

    public function __construct(
        private readonly MultiStepCheckout $checkout,
        private readonly Assets $assets,
        private readonly CartOverlay $cartOverlay
    ) {
    }

    public function register(): void
    {
        add_action('plugins_loaded', [$this, 'boot']);
    }

    public function boot(): void
    {
        if (! $this->isWooCommerceActive()) {
            return;
        }

        $this->checkout->register();
        $this->assets->register();
        $this->cartOverlay->register();
    }

    private function isWooCommerceActive(): bool
    {
        return class_exists('WooCommerce');
    }
}
