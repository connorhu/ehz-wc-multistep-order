<?php

declare(strict_types=1);

namespace Ehz\WcMultistepOrder;

use Ehz\WcMultistepOrder\Core\Plugin;
use Ehz\WcMultistepOrder\Core\PluginFactory;

if (! function_exists(__NAMESPACE__ . '\\bootstrap')) {
    function bootstrap(): void
    {
        static $plugin;

        if ($plugin instanceof Plugin) {
            return;
        }

        $plugin = (new PluginFactory())->make();
        $plugin->register();
    }
}
