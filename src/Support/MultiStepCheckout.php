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
            'id' => 'billing',
            'label' => 'Számlázási adatok',
            'description' => 'Töltse ki a számlázási adatokat.',
        ],
        [
            'id' => 'shipping',
            'label' => 'Szállítási adatok',
            'description' => 'Válassza ki a szállítás részleteit.',
        ],
        [
            'id' => 'payment',
            'label' => 'Fizetés módja',
            'description' => 'Adja meg a fizetési módot.',
        ],
        [
            'id' => 'summary',
            'label' => 'Összesítés',
            'description' => 'Ellenőrizze a rendelését.',
        ],
    ];

    public function register(): void
    {
        add_action('woocommerce_before_checkout_form', [$this, 'renderHeading'], 5);
        add_action('woocommerce_checkout_before_customer_details', [$this, 'renderProgress'], 5);
        add_action('woocommerce_after_checkout_billing_form', [$this, 'renderBillingActions']);
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

    public function renderHeading(): void
    {
        echo '<h1 class="ehz-wc-multistep__title">' . esc_html__('Megrendelés', 'ehz-wc-multistep-order') . '</h1>';
    }

    public function renderProgress(): void
    {
        $steps = $this->provideSteps();

        echo '<nav class="ehz-wc-multistep" aria-label="Checkout steps">';
        foreach ($steps as $step) {
            $label = esc_html($step['label']);
            $position = (int) $step['position'];
            $id = esc_attr($step['id']);
            echo "<span class=\"ehz-step\" id=\"ehz-step-{$id}\" data-step=\"{$id}\">{$position}. {$label}</span>";
        }
        echo '</nav>';
    }

    public function renderBillingActions(): void
    {
        $cartUrl = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '';

        echo '<div class="ehz-wc-multistep__actions">';
        echo '  <button type="button" class="button alt" data-ehz-step-next="shipping">' . esc_html__('Tovább a Szállítási adatokhoz', 'ehz-wc-multistep-order') . '</button>';
        echo '  <a class="button" href="' . esc_url($cartUrl) . '">' . esc_html__('Vissza a kosárhoz', 'ehz-wc-multistep-order') . '</a>';
        echo '</div>';
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
