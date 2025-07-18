<?php
/**
 * Plugin Name: Energy Alabama Knowledge Base
 * Plugin URI: https://energyalabama.org
 * Description: A comprehensive knowledge base system for Energy Alabama with custom post types, taxonomies, and search functionality.
 * Version: 1.0.0
 * Author: Energy Alabama
 * License: GPL v2 or later
 * Text Domain: energy-alabama-kb
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 */
define('ENERGY_ALABAMA_KB_VERSION', '1.0.0');
define('ENERGY_ALABAMA_KB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ENERGY_ALABAMA_KB_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_energy_alabama_kb() {
    require_once ENERGY_ALABAMA_KB_PLUGIN_DIR . 'includes/class-activator.php';
    Energy_Alabama_KB_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_energy_alabama_kb() {
    require_once ENERGY_ALABAMA_KB_PLUGIN_DIR . 'includes/class-deactivator.php';
    Energy_Alabama_KB_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_energy_alabama_kb');
register_deactivation_hook(__FILE__, 'deactivate_energy_alabama_kb');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require ENERGY_ALABAMA_KB_PLUGIN_DIR . 'includes/class-plugin.php';

/**
 * Begins execution of the plugin.
 */
function run_energy_alabama_kb() {
    $plugin = new Energy_Alabama_KB_Plugin();
    $plugin->run();
}
run_energy_alabama_kb();
