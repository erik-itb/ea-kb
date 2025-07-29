<?php
/**
 * Plugin Name: Energy Alabama Knowledge Base
 * Plugin URI: https://energyalabama.org
 * Description: Comprehensive knowledge base system for clean energy resources, dockets, and educational materials.
 * Version: 1.0.9
 * Author: ehanson
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
define('EAKB_VERSION', '1.0.9');
define('EAKB_PLUGIN_FILE', __FILE__);
define('EAKB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EAKB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EAKB_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Enqueue admin assets
 */
function eakb_enqueue_admin_assets($hook) {
    // Get current screen
    $screen = get_current_screen();
    
    // Only enqueue on KB-related admin pages
    if ($screen && (
        $screen->post_type === 'kb_article' || 
        $screen->post_type === 'docket' ||
        strpos($hook, 'energy-alabama-kb') !== false
    )) {
        // Enqueue admin CSS
        wp_enqueue_style(
            'eakb-admin-css',
            EAKB_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            EAKB_VERSION
        );
        
        // Enqueue admin JS
        wp_enqueue_script(
            'eakb-admin-js',
            EAKB_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-util'),
            EAKB_VERSION,
            true
        );
        
        // Localize admin script
        wp_localize_script('eakb-admin-js', 'eakb_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('eakb_admin_nonce'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this item?', 'energy-alabama-kb'),
                'saving' => __('Saving...', 'energy-alabama-kb'),
                'saved' => __('Saved!', 'energy-alabama-kb'),
            )
        ));
    }
}
add_action('admin_enqueue_scripts', 'eakb_enqueue_admin_assets');

/**
 * Load plugin textdomain for translations
 */
function eakb_load_textdomain() {
    load_plugin_textdomain(
        'energy-alabama-kb',
        false,
        dirname(EAKB_PLUGIN_BASENAME) . '/languages'
    );
}
add_action('plugins_loaded', 'eakb_load_textdomain');

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

/**
 * Add plugin action links
 */
function eakb_plugin_action_links($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=energy-alabama-kb') . '">' . __('Settings', 'energy-alabama-kb') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . EAKB_PLUGIN_BASENAME, 'eakb_plugin_action_links');

/**
 * Check if required PHP version is met
 */
function eakb_check_php_version() {
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>';
            printf(
                __('Energy Alabama Knowledge Base requires PHP 7.4 or higher. You are running PHP %s. Please update PHP to use this plugin.', 'energy-alabama-kb'),
                PHP_VERSION
            );
            echo '</p></div>';
        });
        return false;
    }
    return true;
}

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'activate_energy_alabama_kb');
register_deactivation_hook(__FILE__, 'deactivate_energy_alabama_kb');

// Check PHP version before starting plugin
if (eakb_check_php_version()) {
    // Start the plugin
    run_energy_alabama_kb();
}