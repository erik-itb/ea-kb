<?php
/**
 * Plugin Name: Energy Alabama Knowledge Base
 * Plugin URI: https://energyalabama.org
 * Description: Comprehensive knowledge base system for clean energy resources, dockets, and educational materials.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: energy-alabama-kb
 * Domain Path: /languages
 * 
 * @package Energy_Alabama_KB
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('EAKB_VERSION', '1.0.0');
define('EAKB_PLUGIN_FILE', __FILE__);
define('EAKB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EAKB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EAKB_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Load core plugin class
 */
require_once EAKB_PLUGIN_DIR . 'includes/class-plugin.php';

/**
 * Initialize the plugin
 */
function run_energy_alabama_kb() {
    $plugin = new Energy_Alabama_KB();
    $plugin->run();
}

/**
 * Plugin activation hook
 */
function activate_energy_alabama_kb() {
    require_once EAKB_PLUGIN_DIR . 'includes/class-activator.php';
    Energy_Alabama_KB_Activator::activate();
}

/**
 * Plugin deactivation hook
 */
function deactivate_energy_alabama_kb() {
    require_once EAKB_PLUGIN_DIR . 'includes/class-deactivator.php';
    Energy_Alabama_KB_Deactivator::deactivate();
}

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'activate_energy_alabama_kb');
register_deactivation_hook(__FILE__, 'deactivate_energy_alabama_kb');

// Start the plugin
run_energy_alabama_kb();