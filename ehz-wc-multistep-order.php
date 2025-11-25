<?php
/**
 * Plugin Name:       EHZ WooCommerce Multistep Order
 * Plugin URI:        https://example.com/plugins/ehz-wc-multistep-order
 * Description:       Modern boilerplate for a multistep WooCommerce checkout experience.
 * Version:           0.1.0
 * Requires PHP:      8.1
 * Requires at least: 6.5
 * Author:            Example Studio
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ehz-wc-multistep-order
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

// Bootstrap the plugin immediately to keep the file lean.
Ehz\WcMultistepOrder\bootstrap();
