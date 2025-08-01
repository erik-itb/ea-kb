<?php
/**
 * Register custom post types for the plugin
 *
 * @package Energy_Alabama_KB
 * @since   1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom post types class
 */
class Energy_Alabama_KB_Post_Types {

    /**
     * Initialize the class
     */
    public function __construct() {
        add_action('init', array($this, 'register_post_types'));
        add_action('admin_menu', array($this, 'add_docket_submenu'), 999); // Run late to ensure proper order
        add_filter('post_updated_messages', array($this, 'updated_messages'));
        
        // Check if we need to flush rewrite rules
        add_action('init', array($this, 'maybe_flush_rewrite_rules'));
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
     * Register all custom post types
     */
    public function register_post_types() {
        $this->register_kb_article();
        $this->register_docket();
    }

    /**
     * Register Knowledge Base Article post type
     */
    private function register_kb_article() {
        $labels = array(
            'name'                  => _x('KB Articles', 'Post type general name', 'energy-alabama-kb'),
            'singular_name'         => _x('KB Article', 'Post type singular name', 'energy-alabama-kb'),
            'menu_name'             => _x('Knowledge Base', 'Admin Menu text', 'energy-alabama-kb'),
            'name_admin_bar'        => _x('KB Article', 'Add New on Toolbar', 'energy-alabama-kb'),
            'add_new'               => __('Add New', 'energy-alabama-kb'),
            'add_new_item'          => __('Add New KB Article', 'energy-alabama-kb'),
            'new_item'              => __('New KB Article', 'energy-alabama-kb'),
            'edit_item'             => __('Edit KB Article', 'energy-alabama-kb'),
            'view_item'             => __('View KB Article', 'energy-alabama-kb'),
            'all_items'             => __('All KB Articles', 'energy-alabama-kb'),
            'search_items'          => __('Search KB Articles', 'energy-alabama-kb'),
            'parent_item_colon'     => __('Parent KB Articles:', 'energy-alabama-kb'),
            'not_found'             => __('No KB articles found.', 'energy-alabama-kb'),
            'not_found_in_trash'    => __('No KB articles found in Trash.', 'energy-alabama-kb'),
            'featured_image'        => _x('Featured Image', 'Overrides the "Featured Image" phrase', 'energy-alabama-kb'),
            'set_featured_image'    => _x('Set featured image', 'Overrides the "Set featured image" phrase', 'energy-alabama-kb'),
            'remove_featured_image' => _x('Remove featured image', 'Overrides the "Remove featured image" phrase', 'energy-alabama-kb'),
            'use_featured_image'    => _x('Use as featured image', 'Overrides the "Use as featured image" phrase', 'energy-alabama-kb'),
            'archives'              => _x('KB Article archives', 'The post type archive label', 'energy-alabama-kb'),
            'insert_into_item'      => _x('Insert into KB article', 'Overrides the "Insert into post" phrase', 'energy-alabama-kb'),
            'uploaded_to_this_item' => _x('Uploaded to this KB article', 'Overrides the "Uploaded to this post" phrase', 'energy-alabama-kb'),
            'filter_items_list'     => _x('Filter KB articles list', 'Screen reader text for the filter links', 'energy-alabama-kb'),
            'items_list_navigation' => _x('KB articles list navigation', 'Screen reader text for the pagination', 'energy-alabama-kb'),
            'items_list'            => _x('KB articles list', 'Screen reader text for the items list', 'energy-alabama-kb'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array(
                'slug'       => 'knowledge-base',
                'with_front' => false
            ),
            'capability_type'    => 'post',
            'has_archive'        => 'kb-articles', // Changed from 'knowledge-base' to avoid conflict
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-book-alt',
            'supports'           => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail',
                'revisions',
                'custom-fields'
            ),
            'show_in_rest'       => true,
            'rest_base'          => 'kb-articles',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'taxonomies'         => array('kb_category', 'kb_tag'),
            'template'           => array(
                array('core/paragraph', array(
                    'placeholder' => __('Start writing your knowledge base article...', 'energy-alabama-kb')
                ))
            )
        );

        register_post_type('kb_article', $args);
    }

    /**
     * Register Docket post type
     */
    private function register_docket() {
        $labels = array(
            'name'                  => _x('Dockets', 'Post type general name', 'energy-alabama-kb'),
            'singular_name'         => _x('Docket', 'Post type singular name', 'energy-alabama-kb'),
            'menu_name'             => _x('Dockets', 'Admin Menu text', 'energy-alabama-kb'),
            'name_admin_bar'        => _x('Docket', 'Add New on Toolbar', 'energy-alabama-kb'),
            'add_new'               => __('Add New', 'energy-alabama-kb'),
            'add_new_item'          => __('Add New Docket', 'energy-alabama-kb'),
            'new_item'              => __('New Docket', 'energy-alabama-kb'),
            'edit_item'             => __('Edit Docket', 'energy-alabama-kb'),
            'view_item'             => __('View Docket', 'energy-alabama-kb'),
            'all_items'             => __('All Dockets', 'energy-alabama-kb'),
            'search_items'          => __('Search Dockets', 'energy-alabama-kb'),
            'parent_item_colon'     => __('Parent Dockets:', 'energy-alabama-kb'),
            'not_found'             => __('No dockets found.', 'energy-alabama-kb'),
            'not_found_in_trash'    => __('No dockets found in Trash.', 'energy-alabama-kb'),
            'featured_image'        => _x('Featured Image', 'Overrides the "Featured Image" phrase', 'energy-alabama-kb'),
            'set_featured_image'    => _x('Set featured image', 'Overrides the "Set featured image" phrase', 'energy-alabama-kb'),
            'remove_featured_image' => _x('Remove featured image', 'Overrides the "Remove featured image" phrase', 'energy-alabama-kb'),
            'use_featured_image'    => _x('Use as featured image', 'Overrides the "Use as featured image" phrase', 'energy-alabama-kb'),
            'archives'              => _x('Docket archives', 'The post type archive label', 'energy-alabama-kb'),
            'insert_into_item'      => _x('Insert into docket', 'Overrides the "Insert into post" phrase', 'energy-alabama-kb'),
            'uploaded_to_this_item' => _x('Uploaded to this docket', 'Overrides the "Uploaded to this post" phrase', 'energy-alabama-kb'),
            'filter_items_list'     => _x('Filter dockets list', 'Screen reader text for the filter links', 'energy-alabama-kb'),
            'items_list_navigation' => _x('Dockets list navigation', 'Screen reader text for the pagination', 'energy-alabama-kb'),
            'items_list'            => _x('Dockets list', 'Screen reader text for the items list', 'energy-alabama-kb'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => 'edit.php?post_type=kb_article',
            'show_in_admin_bar'  => true,
            'show_in_nav_menus'  => true,
            'can_export'         => true,
            'query_var'          => true,
            'rewrite'            => array(
                'slug'       => 'docket',
                'with_front' => false
            ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'supports'           => array(
                'title',
                'editor',
                'excerpt',
                'revisions',
                'custom-fields'
            ),
            'show_in_rest'       => true,
            'rest_base'          => 'dockets',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'taxonomies'         => array('docket_jurisdiction', 'kb_tag'),
            'template'           => array(
                array('core/paragraph', array(
                    'placeholder' => __('Enter docket description...', 'energy-alabama-kb')
                ))
            )
        );

        register_post_type('docket', $args);
    }

    /**
     * Add docket submenu items manually
     */
    public function add_docket_submenu() {
        global $submenu;
        
        // Add the "Add New Docket" submenu item after "All Dockets"
        add_submenu_page(
            'edit.php?post_type=kb_article',
            __('Add New Docket', 'energy-alabama-kb'),
            __('Add New Docket', 'energy-alabama-kb'),
            'edit_posts',
            'post-new.php?post_type=docket'
        );
        
        // Add jurisdictions submenu at the end
        add_submenu_page(
            'edit.php?post_type=kb_article',
            __('Jurisdictions', 'energy-alabama-kb'),
            __('Jurisdictions', 'energy-alabama-kb'),
            'manage_categories',
            'edit-tags.php?taxonomy=docket_jurisdiction&post_type=docket'
        );
        
        // Reorder the submenu items to get the desired order
        if (isset($submenu['edit.php?post_type=kb_article'])) {
            $kb_submenu = $submenu['edit.php?post_type=kb_article'];
            $reordered = array();
            
            // Expected order based on WordPress default positions:
            // All KB Articles (5)
            // Add New KB Article (10) 
            // Categories (15)
            // Tags (16)
            // All Dockets (appears automatically)
            // Add New Docket (we'll position this)
            // Jurisdictions (we'll position this)
            
            foreach ($kb_submenu as $position => $item) {
                if (strpos($item[2], 'post-new.php?post_type=docket') !== false) {
                    // Move "Add New Docket" to position after "All Dockets"
                    $reordered[25] = $item;
                } elseif (strpos($item[2], 'edit-tags.php?taxonomy=docket_jurisdiction') !== false) {
                    // Move "Jurisdictions" to the end
                    $reordered[30] = $item;
                } else {
                    // Keep other items in their original positions
                    $reordered[$position] = $item;
                }
            }
            
            // Sort by position and reassign
            ksort($reordered);
            $submenu['edit.php?post_type=kb_article'] = $reordered;
        }
    }

    /**
     * Maybe flush rewrite rules if needed
     */
    public function maybe_flush_rewrite_rules() {
        if (get_option('eakb_flush_rewrite_rules')) {
            flush_rewrite_rules();
            delete_option('eakb_flush_rewrite_rules');
        }
    }

    /**
     * Modify post type messages
     */
    public function updated_messages($messages) {
        $post = get_post();
        $post_type = get_post_type($post);
        $post_type_object = get_post_type_object($post_type);

        $messages['kb_article'] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => __('KB Article updated.', 'energy-alabama-kb'),
            2  => __('Custom field updated.', 'energy-alabama-kb'),
            3  => __('Custom field deleted.', 'energy-alabama-kb'),
            4  => __('KB Article updated.', 'energy-alabama-kb'),
            5  => isset($_GET['revision']) ? sprintf(__('KB Article restored to revision from %s', 'energy-alabama-kb'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6  => __('KB Article published.', 'energy-alabama-kb'),
            7  => __('KB Article saved.', 'energy-alabama-kb'),
            8  => __('KB Article submitted.', 'energy-alabama-kb'),
            9  => sprintf(
                __('KB Article scheduled for: <strong>%1$s</strong>.', 'energy-alabama-kb'),
                date_i18n(__('M j, Y @ G:i', 'energy-alabama-kb'), strtotime($post->post_date))
            ),
            10 => __('KB Article draft updated.', 'energy-alabama-kb')
        );

        $messages['docket'] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => __('Docket updated.', 'energy-alabama-kb'),
            2  => __('Custom field updated.', 'energy-alabama-kb'),
            3  => __('Custom field deleted.', 'energy-alabama-kb'),
            4  => __('Docket updated.', 'energy-alabama-kb'),
            5  => isset($_GET['revision']) ? sprintf(__('Docket restored to revision from %s', 'energy-alabama-kb'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6  => __('Docket published.', 'energy-alabama-kb'),
            7  => __('Docket saved.', 'energy-alabama-kb'),
            8  => __('Docket submitted.', 'energy-alabama-kb'),
            9  => sprintf(
                __('Docket scheduled for: <strong>%1$s</strong>.', 'energy-alabama-kb'),
                date_i18n(__('M j, Y @ G:i', 'energy-alabama-kb'), strtotime($post->post_date))
            ),
            10 => __('Docket draft updated.', 'energy-alabama-kb')
        );

        return $messages;
    }
}