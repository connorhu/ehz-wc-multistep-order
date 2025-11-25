<?php

declare(strict_types=1);

namespace Ehz\WcMultistepOrder\Support;

class MultiStepCheckout
{
    /**
     * @var array<int, array{ id: string, label: string, description?: string }>
     */
    private array $defaultSteps = [
        [
            'id' => 'customer',
            'label' => 'Customer',
            'description' => 'Account and contact details.',
        ],
        [
            'id' => 'shipping',
            'label' => 'Shipping',
            'description' => 'Delivery preferences.',
        ],
        [
            'id' => 'payment',
            'label' => 'Payment',
            'description' => 'Select a payment method.',
        ],
        [
            'id' => 'review',
            'label' => 'Review',
            'description' => 'Verify and place the order.',
        ],
    ];

    public function register(): void
    {
        add_action('woocommerce_checkout_before_customer_details', [$this, 'renderProgress'], 5);
        add_filter('ehz_wc_multistep_steps', [$this, 'provideSteps']);
        add_filter('woocommerce_available_payment_gateways', [$this, 'exposeStepAwareGateways']);
    }

    public function provideSteps(array $steps = []): array
    {
        $merged = array_values($steps ?: $this->defaultSteps);

        return array_map(static function (array $step, int $index): array {
            $step['position'] = $index + 1;
            return $step;
        }, $merged, array_keys($merged));
    }

    public function renderProgress(): void
    {
        $steps = $this->provideSteps();

        echo '<nav class="ehz-wc-multistep" aria-label="Checkout steps">';
        foreach ($steps as $step) {
            $label = esc_html($step['label']);
            $position = (int) $step['position'];
            $id = esc_attr($step['id']);
            echo "<span class=\"ehz-step\" data-step=\"{$id}\">{$position}. {$label}</span>";
        }
        echo '</nav>';
    }

    public function exposeStepAwareGateways(array $gateways): array
    {
        foreach ($gateways as $gateway) {
            if (is_object($gateway)) {
                $gateway->supports[] = 'ehz-multistep';
            }
        }

        return $gateways;
    }
}
