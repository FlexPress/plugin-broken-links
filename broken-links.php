<?php

/**
 * Plugin Name: Broken Links Plugin
 * Plugin URI: https://github.com/FlexPress/plugin-broken-links
 * Description: FlexPress based plugin
 * Version: 1.0.0
 * Author: FlexPress
 * Author URI: https://github.com/FlexPress
 * License: MIT
 */

use FlexPress\Plugins\BrokenLinks\DependencyInjection\DependencyInjectionContainer;

// Include autoloader if installed on it's own.
$autoloadFile = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadFile)) {
    require_once($autoloadFile);
}

// Dependency Injection
$dic = new DependencyInjectionContainer();
$dic->init();

// Run app
$dic['BrokenLinks']->init(__FILE__);
