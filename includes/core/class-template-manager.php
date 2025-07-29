<?php
/**
 * Template manager for custom templates
 *
 * @package Energy_Alabama_KB
 * @since   1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template manager class
 */
class Energy_Alabama_KB_Template_Manager {

    /**
     * Initialize the class
     */
    public function __construct() {
        // Direct debug - write to a custom log file to bypass WordPress logging issues
        file_put_contents(ABSPATH . 'template-debug.txt', date('Y-m-d H:i:s') . " - Template Manager initialized\n", FILE_APPEND);
        
        add_filter('template_include', array($this, 'load_custom_templates'));
        add_action('wp_head', array($this, 'add_structured_data'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_template_assets'));
    }

    /**
     * Get instance (singleton pattern)
     */
    public static function get_instance() {
        static $instance = null;
        if (null === $instance) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Load custom templates for our post types and pages
     */
    public function load_custom_templates($template) {
        global $post, $wp_query;

        // Direct debug - write to our custom log file
        file_put_contents(ABSPATH . 'template-debug.txt', date('Y-m-d H:i:s') . " - Template filter triggered\n", FILE_APPEND);
        file_put_contents(ABSPATH . 'template-debug.txt', date('Y-m-d H:i:s') . " - is_page(): " . (is_page() ? 'true' : 'false') . "\n", FILE_APPEND);
        file_put_contents(ABSPATH . 'template-debug.txt', date('Y-m-d H:i:s') . " - is_404(): " . (is_404() ? 'true' : 'false') . "\n", FILE_APPEND);
        file_put_contents(ABSPATH . 'template-debug.txt', date('Y-m-d H:i:s') . " - Request URI: " . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);
        
        if ($post) {
            file_put_contents(ABSPATH . 'template-debug.txt', date('Y-m-d H:i:s') . " - Post exists: " . $post->post_name . " (ID: " . $post->ID . ")\n", FILE_APPEND);
        } else {
            file_put_contents(ABSPATH . 'template-debug.txt', date('Y-m-d H:i:s') . " - No post object found\n", FILE_APPEND);
        }
        
        if (is_page() && $post) {
            file_put_contents(ABSPATH . 'template-debug.txt', date('Y-m-d H:i:s') . " - Page detected: " . $post->post_name . "\n", FILE_APPEND);
        }

        // Handle knowledge base landing page
        if (is_page() && $post && $post->post_name === 'knowledge-base') {
            // Try full template
            $custom_template = $this->get_template('page-knowledge-base.php');
            if ($custom_template) {
                return $custom_template;
            }
            
            // Fallback to simple template
            $custom_template = $this->get_template('page-knowledge-base-simple.php');
            if ($custom_template) {
                return $custom_template;
            }
        }

        // Handle single KB article
        if (is_singular('kb_article')) {
            $custom_template = $this->get_template('single-kb-article.php');
            if ($custom_template) {
                return $custom_template;
            }
        }

        // Handle single docket
        if (is_singular('docket')) {
            $custom_template = $this->get_template('single-docket.php');
            if ($custom_template) {
                return $custom_template;
            }
        }

        // Handle KB article archive
        if (is_post_type_archive('kb_article')) {
            $custom_template = $this->get_template('archive-kb-article.php');
            if ($custom_template) {
                return $custom_template;
            }
        }

        // Handle docket archive
        if (is_post_type_archive('docket')) {
            $custom_template = $this->get_template('archive-docket.php');
            if ($custom_template) {
                return $custom_template;
            }
        }

        // Handle specific taxonomy pages
        if (is_tax('kb_category')) {
            // Debug logging for taxonomy
            file_put_contents(ABSPATH . 'template-debug.txt', date('Y-m-d H:i:s') . " - KB Category taxonomy detected\n", FILE_APPEND);
            
            $custom_template = $this->get_template('taxonomy-kb-category.php');
            if ($custom_template) {
                file_put_contents(ABSPATH . 'template-debug.txt', date('Y-m-d H:i:s') . " - Found taxonomy-kb-category.php template\n", FILE_APPEND);
                return $custom_template;
            } else {
                file_put_contents(ABSPATH . 'template-debug.txt', date('Y-m-d H:i:s') . " - taxonomy-kb-category.php template NOT found\n", FILE_APPEND);
            }
        }

        if (is_tax('kb_tag')) {
            $custom_template = $this->get_template('taxonomy-kb-tag.php');
            if ($custom_template) {
                return $custom_template;
            }
            
            // Fallback to generic KB taxonomy template
            $custom_template = $this->get_template('taxonomy-kb.php');
            if ($custom_template) {
                return $custom_template;
            }
        }

        if (is_tax('docket_jurisdiction')) {
            $custom_template = $this->get_template('taxonomy-docket-jurisdiction.php');
            if ($custom_template) {
                return $custom_template;
            }
            
            // Fallback to generic taxonomy template
            $custom_template = $this->get_template('taxonomy-kb.php');
            if ($custom_template) {
                return $custom_template;
            }
        }

        return $template;
    }

    /**
     * Get template file path
     */
    private function get_template($template_name) {
        // Check in theme first (allows customization)
        $theme_template = locate_template(array(
            'energy-alabama-kb/' . $template_name,
            $template_name
        ));

        if ($theme_template) {
            return $theme_template;
        }

        // Use plugin template
        $plugin_template = EAKB_PLUGIN_DIR . 'templates/' . $template_name;
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }

        return false;
    }

    /**
     * Enqueue template-specific assets
     */
    public function enqueue_template_assets() {
        global $post;

        // Enqueue KB styles on KB pages
        if (is_page() && $post && $post->post_name === 'knowledge-base') {
            wp_enqueue_style(
                'eakb-landing-page',
                EAKB_PLUGIN_URL . 'assets/css/kb-landing.css',
                array(),
                EAKB_VERSION
            );
            
            wp_enqueue_script(
                'eakb-landing-page',
                EAKB_PLUGIN_URL . 'assets/js/kb-landing.js',
                array('jquery'),
                EAKB_VERSION,
                true
            );
        }

        // Enqueue on all KB content - use our frontend.css
        if (is_singular(array('kb_article', 'docket')) || 
            is_post_type_archive(array('kb_article', 'docket')) || 
            is_tax(array('kb_category', 'kb_tag', 'docket_jurisdiction'))) {
            
            // Use our consolidated frontend.css
            wp_enqueue_style(
                'eakb-frontend-css',
                EAKB_PLUGIN_URL . 'assets/css/frontend.css',
                array(),
                EAKB_VERSION
            );

            wp_enqueue_script(
                'eakb-frontend-js',
                EAKB_PLUGIN_URL . 'assets/js/frontend.js',
                array('jquery'),
                EAKB_VERSION,
                true
            );

            // Localize script for AJAX
            wp_localize_script('eakb-frontend-js', 'eakb_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('eakb_nonce'),
                'strings' => array(
                    'loading' => __('Loading...', 'energy-alabama-kb'),
                    'error' => __('An error occurred. Please try again.', 'energy-alabama-kb'),
                    'search_placeholder' => __('Search knowledge base...', 'energy-alabama-kb'),
                    'no_results' => __('No results found', 'energy-alabama-kb')
                )
            ));
        }
    }

    /**
     * Add structured data for KB articles
     */
    public function add_structured_data() {
        if (is_singular('kb_article')) {
            global $post;
            
            $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => get_the_title(),
                'description' => get_the_excerpt() ?: wp_trim_words(get_the_content(), 30),
                'datePublished' => get_the_date('c'),
                'dateModified' => get_the_modified_date('c'),
                'author' => array(
                    '@type' => 'Organization',
                    'name' => 'Energy Alabama'
                ),
                'publisher' => array(
                    '@type' => 'Organization',
                    'name' => 'Energy Alabama',
                    'url' => home_url()
                ),
                'articleSection' => 'Clean Energy Knowledge Base'
            );

            if (has_post_thumbnail()) {
                $schema['image'] = get_the_post_thumbnail_url($post, 'large');
            }

            // Add difficulty level if available
            $difficulty = get_post_meta($post->ID, '_eakb_difficulty_level', true);
            if ($difficulty) {
                $schema['educationalLevel'] = ucfirst($difficulty);
            }

            echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
        }
    }

    /**
     * Get categories for landing page display
     */
    public function get_kb_categories() {
        $categories = get_terms(array(
            'taxonomy' => 'kb_category',
            'hide_empty' => false,
            'orderby' => 'term_order',
            'order' => 'ASC'
        ));

        if (is_wp_error($categories)) {
            return array();
        }

        return $categories;
    }

    /**
     * Get recent articles for landing page
     */
    public function get_recent_articles($limit = 6) {
        $query = new WP_Query(array(
            'post_type' => 'kb_article',
            'posts_per_page' => $limit,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        return $query->posts;
    }

    /**
     * Get category article count
     */
    public function get_category_count($category_id) {
        return get_term_meta($category_id, 'article_count', true) ?: 0;
    }
}