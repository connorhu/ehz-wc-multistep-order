# EHZ WooCommerce Multistep Order

Modern WooCommerce plugin boilerplate for building a guided, multi-step checkout experience. The project favors current WordPress tooling, PSR-4 autoloading, and fast feedback via automated tests.

## Features
- Namespaced, PSR-4 autoloaded plugin code with lightweight bootstrapper.
- Checkout step model that can be filtered via `ehz_wc_multistep_steps`.
- Asset loader that only runs on the checkout page and exposes step data to JavaScript.
- Pest + Brain Monkey test suite to validate WordPress integrations without a full runtime.

## Getting started
1. Ensure you have PHP 8.1+ and Composer installed.
2. Install dependencies:

   ```bash
   composer install
   ```

3. Run the test suite:

   ```bash
   composer test
   ```

4. Activate the plugin in WordPress, then visit the WooCommerce checkout to see the multi-step header rendered above customer details.

## Development notes
- Hooks and render output are isolated in small classes to keep the plugin testable.
- Extend or reorder the checkout steps using the `ehz_wc_multistep_steps` filter; each step receives a `position` value automatically.
- Assets are enqueued only when `is_checkout()` is `true` to avoid unnecessary frontend weight.
