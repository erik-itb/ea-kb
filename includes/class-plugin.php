<?php
/**
 * The core plugin class
 *
 * @package Energy_Alabama_KB
 * @since   1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin class
 */
class Energy_Alabama_KB {

    /**
     * The loader that's responsible for maintaining and registering all hooks
     *
     * @var Energy_Alabama_KB_Loader
     */
    protected $loader;

    /**
     * The unique identifier of this plugin
     *
     * @var string
     */
    protected $plugin_name;

    /**
     * The current version of the plugin
     *
     * @var string
     */
    protected $version;

    /**
     * Define the core functionality of the plugin
     */
    public function __construct() {
        $this->version = EAKB_VERSION;
        $this->plugin_name = 'energy-alabama-kb';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin
     */
    private function load_dependencies() {
        // The class responsible for orchestrating the actions and filters
        require_once EAKB_PLUGIN_DIR . 'includes/class-loader.php';

        // The class responsible for defining internationalization functionality
        require_once EAKB_PLUGIN_DIR . 'includes/class-i18n.php';

        // Core functionality (only load files that exist)
        require_once EAKB_PLUGIN_DIR . 'includes/core/class-post-types.php';
        require_once EAKB_PLUGIN_DIR . 'includes/core/class-taxonomies.php';

        // TODO: Load these as we create them
        // require_once EAKB_PLUGIN_DIR . 'includes/core/class-meta-fields.php';
        // require_once EAKB_PLUGIN_DIR . 'includes/core/class-template-manager.php';
        // require_once EAKB_PLUGIN_DIR . 'includes/core/class-search-handler.php';

        // TODO: Admin functionality (will create these next)
        // require_once EAKB_PLUGIN_DIR . 'includes/admin/class-admin.php';
        // require_once EAKB_PLUGIN_DIR . 'includes/admin/class-meta-boxes.php';
        // require_once EAKB_PLUGIN_DIR . 'includes/admin/class-settings.php';
        // require_once EAKB_PLUGIN_DIR . 'includes/admin/class-icon-manager.php';

        // TODO: Frontend functionality (will create these next)
        // require_once EAKB_PLUGIN_DIR . 'includes/frontend/class-frontend.php';
        // require_once EAKB_PLUGIN_DIR . 'includes/frontend/class-ajax-handlers.php';

        // TODO: Utilities (will create these next)
        // require_once EAKB_PLUGIN_DIR . 'includes/utils/class-helpers.php';

        $this->loader = new Energy_Alabama_KB_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization
     */
    private function set_locale() {
        $plugin_i18n = new Energy_Alabama_KB_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     */
    private function define_admin_hooks() {
        // Post types and taxonomies
        $post_types = new Energy_Alabama_KB_Post_Types();
        $taxonomies = new Energy_Alabama_KB_Taxonomies();
        
        $this->loader->add_action('init', $post_types, 'register_post_types');
        $this->loader->add_action('init', $taxonomies, 'register_taxonomies');

        // TODO: Add these hooks as we create the classes
        /*
        // Core admin functionality
        $plugin_admin = new Energy_Alabama_KB_Admin($this->get_plugin_name(), $this->get_version());
        
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // Meta boxes and fields
        $meta_boxes = new Energy_Alabama_KB_Meta_Boxes();
        $meta_fields = new Energy_Alabama_KB_Meta_Fields();
        
        $this->loader->add_action('add_meta_boxes', $meta_boxes, 'add_meta_boxes');
        $this->loader->add_action('save_post', $meta_boxes, 'save_meta_boxes');
        $this->loader->add_action('init', $meta_fields, 'register_meta_fields');

        // Settings page
        $settings = new Energy_Alabama_KB_Settings();
        $this->loader->add_action('admin_menu', $settings, 'add_options_page');
        $this->loader->add_action('admin_init', $settings, 'register_settings');

        // Icon manager
        $icon_manager = new Energy_Alabama_KB_Icon_Manager();
        $this->loader->add_action('wp_ajax_eakb_get_icons', $icon_manager, 'ajax_get_icons');
        */
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     */
    private function define_public_hooks() {
        // TODO: Add these hooks as we create the classes
        /*
        // Frontend functionality
        $plugin_public = new Energy_Alabama_KB_Frontend($this->get_plugin_name(), $this->get_version());
        
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Template manager
        $template_manager = new Energy_Alabama_KB_Template_Manager();
        $this->loader->add_filter('template_include', $template_manager, 'load_custom_templates');
        $this->loader->add_action('wp_head', $template_manager, 'add_structured_data');

        // Search functionality
        $search_handler = new Energy_Alabama_KB_Search_Handler();
        $this->loader->add_action('wp_ajax_eakb_search', $search_handler, 'ajax_search');
        $this->loader->add_action('wp_ajax_nopriv_eakb_search', $search_handler, 'ajax_search');

        // AJAX handlers
        $ajax_handlers = new Energy_Alabama_KB_Ajax_Handlers();
        $this->loader->add_action('wp_ajax_eakb_load_resources', $ajax_handlers, 'load_resources');
        $this->loader->add_action('wp_ajax_nopriv_eakb_load_resources', $ajax_handlers, 'load_resources');
        */
    }

    /**
     * Run the loader to execute all of the hooks with WordPress
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it
     *
     * @return string
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks
     *
     * @return Energy_Alabama_KB_Loader
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin
     *
     * @return string
     */
    public function get_version() {
        return $this->version;
    }
}