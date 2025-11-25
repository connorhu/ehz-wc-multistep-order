<?php

declare(strict_types=1);

namespace Ehz\WcMultistepOrder\Support;

class ShippingStep
{
    public function register(): void
    {
        add_action('woocommerce_review_order_after_shipping', [$this, 'renderFields']);
        add_action('woocommerce_checkout_process', [$this, 'validateSubmission']);
    }

    public function renderFields(): void
    {
        echo '<div class="ehz-shipping-extras" data-ehz-shipping-extras>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '  <div class="ehz-shipping-extras__phone" data-ehz-foxpost-phone hidden>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        if (function_exists('woocommerce_form_field')) {
            $value = isset($_POST['shipping_phone']) ? wc_clean((string) $_POST['shipping_phone']) : '';
            woocommerce_form_field('ehz_shipping_notification_phone', [
                'type' => 'tel',
                'label' => esc_html__('Foxpost értesítési telefonszám', 'ehz-wc-multistep-order'),
                'required' => false,
                'input_class' => ['input-text'],
                'custom_attributes' => [
                    'autocomplete' => 'tel',
                    'inputmode' => 'tel',
                    'data-ehz-foxpost-phone-input' => 'true',
                ],
                'description' => esc_html__('Csak magyar 20, 30 vagy 70-es szám adható meg.', 'ehz-wc-multistep-order'),
            ], $value);
        }
        echo '  </div>';
        echo '</div>';
    }

    public function validateSubmission(): void
    {
        $method = $this->getSelectedShippingMethod();

        if ($this->needsFoxpostNotificationPhone($method)) {
            $this->validateNotificationPhone();
        }

        if ($this->needsShippingAddress($method)) {
            $this->validateShippingAddress();
        }
    }

    private function validateNotificationPhone(): void
    {
        $input = isset($_POST['ehz_shipping_notification_phone']) ? (string) $_POST['ehz_shipping_notification_phone'] : '';
        $normalized = $this->normalizeHungarianMobile($input);

        if ($normalized === null) {
            wc_add_notice(esc_html__('Kérjük, adjon meg egy magyar 20-as, 30-as vagy 70-es számot Foxpost értesítéshez.', 'ehz-wc-multistep-order'), 'error');
            return;
        }

        $_POST['shipping_phone'] = $normalized;
    }

    private function validateShippingAddress(): void
    {
        $required = ['shipping_country', 'shipping_postcode', 'shipping_city', 'shipping_address_1'];

        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                wc_add_notice(esc_html__('Kérjük, adja meg a szállítási címét.', 'ehz-wc-multistep-order'), 'error');
                break;
            }
        }
    }

    private function getSelectedShippingMethod(): string
    {
        $methods = $_POST['shipping_method'] ?? [];

        if (! is_array($methods) || $methods === []) {
            return '';
        }

        $first = reset($methods);
        if (! is_string($first)) {
            return '';
        }

        [$methodId] = explode(':', $first);

        return strtolower($methodId);
    }

    private function needsFoxpostNotificationPhone(string $methodId): bool
    {
        if ($methodId === '' || ! str_contains($methodId, 'foxpost')) {
            return false;
        }

        return $this->containsAny($methodId, ['automat', 'automata', 'locker']);
    }

    private function needsShippingAddress(string $methodId): bool
    {
        if ($methodId === '') {
            return false;
        }

        if (str_contains($methodId, 'foxpost') && $this->containsAny($methodId, ['home', 'hazhoz'])) {
            return true;
        }

        if (str_contains($methodId, 'posta')) {
            return true;
        }

        return false;
    }

    private function normalizeHungarianMobile(string $input): ?string
    {
        $compact = preg_replace('/[\s-]/', '', trim($input));
        if (! is_string($compact)) {
            return null;
        }

        if (! preg_match('/^(?:\+?36|0036|06)(20|30|70)(\d{7})$/', $compact, $matches)) {
            return null;
        }

        return '+36' . $matches[1] . $matches[2];
    }

    /**
     * @param array<int, string> $needles
     */
    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
