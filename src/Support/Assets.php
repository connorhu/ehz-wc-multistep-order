<?php

declare(strict_types=1);

namespace Ehz\WcMultistepOrder\Support;

use Ehz\WcMultistepOrder\Core\Plugin;

class Assets
{
    public function __construct(private readonly MultiStepCheckout $checkout)
    {
    }

    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue'], 20);
    }

    public function enqueue(): void
    {
        if (! function_exists('is_checkout') || ! is_checkout()) {
            return;
        }

        $handle = 'ehz-wc-multistep-order';
        $pluginFile = dirname(__DIR__, 2) . '/ehz-wc-multistep-order.php';
        $scriptPath = plugins_url('assets/js/multistep.js', $pluginFile);
        wp_register_script($handle, $scriptPath, ['wc-checkout'], Plugin::VERSION, ['in_footer' => true]);

        $steps = $this->checkout->provideSteps();
        wp_localize_script($handle, 'ehzWcMultistepOrder', [
            'steps' => $steps,
        ]);

        wp_enqueue_script($handle);
    }
}
