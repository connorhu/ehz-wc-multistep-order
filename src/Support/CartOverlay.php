<?php

declare(strict_types=1);

namespace Ehz\WcMultistepOrder\Support;

class CartOverlay
{
    public function register(): void
    {
        add_action('wp_footer', [$this, 'render']);
    }

    public function render(): void
    {
        if (is_admin() || ! function_exists('WC')) {
            return;
        }

        $cart = WC()->cart;
        if (! $cart) {
            return;
        }

        $items = $this->buildItems($cart->get_cart());
        $total = $this->calculateGrossTotal($items);
        $checkoutUrl = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '#';

        echo '<div class="ehz-cart-overlay" hidden>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '  <div class="ehz-cart-overlay__backdrop" data-ehz-cart-close></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '  <section class="ehz-cart-overlay__panel" role="dialog" aria-modal="true" aria-labelledby="ehz-cart-overlay-title" tabindex="-1">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '    <header class="ehz-cart-overlay__header">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '      <h2 id="ehz-cart-overlay-title">' . esc_html__('Kosár', 'ehz-wc-multistep-order') . '</h2>';
        echo '      <button type="button" class="ehz-cart-overlay__close" data-ehz-cart-close aria-label="' . esc_attr__('Bezárás', 'ehz-wc-multistep-order') . '">×</button>';
        echo '    </header>';

        if (empty($items)) {
            echo '    <p class="ehz-cart-overlay__empty">' . esc_html__('A kosár üres.', 'ehz-wc-multistep-order') . '</p>';
        } else {
            echo '    <div class="ehz-cart-overlay__table">';
            echo '      <table>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '        <thead>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '          <tr><th>' . esc_html__('Termék', 'ehz-wc-multistep-order') . '</th><th>' . esc_html__('Mennyiség', 'ehz-wc-multistep-order') . '</th><th>' . esc_html__('Bruttó ár', 'ehz-wc-multistep-order') . '</th></tr>';
            echo '        </thead>';
            echo '        <tbody>';
            foreach ($items as $item) {
                $name = esc_html($item['name']);
                $quantity = (int) $item['quantity'];
                $price = wp_kses_post($item['price']);
                echo '<tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo "  <td>{$name}</td>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo '  <td><input type="number" class="ehz-cart-overlay__quantity" min="1" value="' . esc_attr((string) $quantity) . '" readonly></td>';
                echo "  <td>{$price}</td>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo '</tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
            echo '        </tbody>';
            echo '        <tfoot>';
            echo '          <tr><th colspan="2" scope="row">' . esc_html__('Összesen', 'ehz-wc-multistep-order') . '</th><th>' . wp_kses_post(wc_price($total)) . '</th></tr>';
            echo '        </tfoot>';
            echo '      </table>';
            echo '    </div>';
            echo '    <div class="ehz-cart-overlay__actions">';
            echo '      <button type="button" class="ehz-cart-overlay__continue" data-ehz-cart-continue data-ehz-cart-target="' . esc_url($checkoutUrl) . '">' . esc_html__('Tovább a megrendeléshez', 'ehz-wc-multistep-order') . '</button>';
            echo '    </div>';
        }

        echo '  </section>';
        echo '</div>';
        echo '<button type="button" class="ehz-cart-overlay__trigger" data-ehz-cart-trigger aria-expanded="false">' . esc_html__('Kosár', 'ehz-wc-multistep-order') . '</button>';
    }

    /**
     * @param array<string, mixed> $cartItems
     * @return array<int, array{name: string, quantity: int, price: string, subtotal: float}>
     */
    private function buildItems(array $cartItems): array
    {
        $items = [];

        foreach ($cartItems as $item) {
            if (! isset($item['data']) || ! is_object($item['data'])) {
                continue;
            }

            $product = $item['data'];
            if (! method_exists($product, 'get_name')) {
                continue;
            }

            $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 0;
            $grossPrice = $this->getGrossPrice($product);
            $items[] = [
                'name' => $product->get_name(),
                'quantity' => $quantity,
                'price' => wc_price($grossPrice),
                'subtotal' => $grossPrice * $quantity,
            ];
        }

        return $items;
    }

    /**
     * @param object $product
     */
    private function getGrossPrice(object $product): float
    {
        if (function_exists('wc_get_price_including_tax')) {
            return (float) wc_get_price_including_tax($product);
        }

        if (method_exists($product, 'get_price')) {
            return (float) $product->get_price();
        }

        return 0.0;
    }

    /**
     * @param array<int, array{subtotal: float}> $items
     */
    private function calculateGrossTotal(array $items): float
    {
        return array_reduce($items, static function (float $carry, array $item): float {
            return $carry + ($item['subtotal'] ?? 0.0);
        }, 0.0);
    }
}
