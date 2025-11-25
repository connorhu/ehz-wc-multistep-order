<?php

declare(strict_types=1);

namespace Ehz\WcMultistepOrder\Support;

use Ehz\WcMultistepOrder\Core\Plugin;

class Assets
{
    public function __construct(
        private readonly MultiStepCheckout $checkout
    ) {
    }

    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue'], 20);
    }

    public function enqueue(): void
    {
        $pluginFile = dirname(__DIR__, 2) . '/ehz-wc-multistep-order.php';

        $this->enqueueCartOverlayAssets($pluginFile);

        if (! function_exists('is_checkout') || ! is_checkout()) {
            return;
        }

        $handle = 'ehz-wc-multistep-order';
        $scriptPath = plugins_url('assets/js/multistep.js', $pluginFile);
        wp_register_script($handle, $scriptPath, ['wc-checkout'], Plugin::VERSION, ['in_footer' => true]);

        $steps = $this->checkout->provideSteps();
        wp_localize_script($handle, 'ehzWcMultistepOrder', [
            'steps' => $steps,
        ]);

        wp_enqueue_script($handle);
    }

    private function enqueueCartOverlayAssets(string $pluginFile): void
    {
        $styleHandle = 'ehz-wc-cart-overlay';
        $stylePath = plugins_url('assets/css/cart-overlay.css', $pluginFile);
        wp_enqueue_style($styleHandle, $stylePath, [], Plugin::VERSION);

        $scriptHandle = 'ehz-wc-cart-overlay';
        $scriptPath = plugins_url('assets/js/cart-overlay.js', $pluginFile);
        wp_enqueue_script($scriptHandle, $scriptPath, [], Plugin::VERSION, ['in_footer' => true]);

        $checkoutUrl = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '#';

        wp_localize_script($scriptHandle, 'ehzWcCartOverlay', [
            'checkoutUrl' => $checkoutUrl,
        ]);
    }
}
