<?php
/**
 * Register custom taxonomies for the plugin
 *
 * @package Energy_Alabama_KB
 * @since   1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom taxonomies class
 */
class Energy_Alabama_KB_Taxonomies {

    /**
     * Register all custom taxonomies
     */
    public function register_taxonomies() {
        $this->register_kb_category();
        $this->register_kb_tag();
        $this->register_docket_jurisdiction();
        $this->create_default_terms();
    }

    /**
     * Register KB Category taxonomy
     */
    private function register_kb_category() {
        $labels = array(
            'name'                       => _x('KB Categories', 'Taxonomy General Name', 'energy-alabama-kb'),
            'singular_name'              => _x('KB Category', 'Taxonomy Singular Name', 'energy-alabama-kb'),
            'menu_name'                  => __('Categories', 'energy-alabama-kb'),
            'all_items'                  => __('All Categories', 'energy-alabama-kb'),
            'parent_item'                => __('Parent Category', 'energy-alabama-kb'),
            'parent_item_colon'          => __('Parent Category:', 'energy-alabama-kb'),
            'new_item_name'              => __('New Category Name', 'energy-alabama-kb'),
            'add_new_item'               => __('Add New Category', 'energy-alabama-kb'),
            'edit_item'                  => __('Edit Category', 'energy-alabama-kb'),
            'update_item'                => __('Update Category', 'energy-alabama-kb'),
            'view_item'                  => __('View Category', 'energy-alabama-kb'),
            'separate_items_with_commas' => __('Separate categories with commas', 'energy-alabama-kb'),
            'add_or_remove_items'        => __('Add or remove categories', 'energy-alabama-kb'),
            'choose_from_most_used'      => __('Choose from the most used', 'energy-alabama-kb'),
            'popular_items'              => __('Popular Categories', 'energy-alabama-kb'),
            'search_items'               => __('Search Categories', 'energy-alabama-kb'),
            'not_found'                  => __('Not Found', 'energy-alabama-kb'),
            'no_terms'                   => __('No categories', 'energy-alabama-kb'),
            'items_list'                 => __('Categories list', 'energy-alabama-kb'),
            'items_list_navigation'      => __('Categories list navigation', 'energy-alabama-kb'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => false,
            'show_in_rest'               => true,
            'rest_base'                  => 'kb-categories',
            'rest_controller_class'      => 'WP_REST_Terms_Controller',
            'query_var'                  => true,
            'rewrite'                    => array(
                'slug'         => 'kb-category',
                'with_front'   => false,
                'hierarchical' => true,
            ),
            'meta_box_cb'                => array($this, 'category_meta_box'),
        );

        register_taxonomy('kb_category', array('kb_article'), $args);
    }

    /**
     * Register KB Tag taxonomy
     */
    private function register_kb_tag() {
        $labels = array(
            'name'                       => _x('KB Tags', 'Taxonomy General Name', 'energy-alabama-kb'),
            'singular_name'              => _x('KB Tag', 'Taxonomy Singular Name', 'energy-alabama-kb'),
            'menu_name'                  => __('Tags', 'energy-alabama-kb'),
            'all_items'                  => __('All Tags', 'energy-alabama-kb'),
            'new_item_name'              => __('New Tag Name', 'energy-alabama-kb'),
            'add_new_item'               => __('Add New Tag', 'energy-alabama-kb'),
            'edit_item'                  => __('Edit Tag', 'energy-alabama-kb'),
            'update_item'                => __('Update Tag', 'energy-alabama-kb'),
            'view_item'                  => __('View Tag', 'energy-alabama-kb'),
            'separate_items_with_commas' => __('Separate tags with commas', 'energy-alabama-kb'),
            'add_or_remove_items'        => __('Add or remove tags', 'energy-alabama-kb'),
            'choose_from_most_used'      => __('Choose from the most used', 'energy-alabama-kb'),
            'popular_items'              => __('Popular Tags', 'energy-alabama-kb'),
            'search_items'               => __('Search Tags', 'energy-alabama-kb'),
            'not_found'                  => __('Not Found', 'energy-alabama-kb'),
            'no_terms'                   => __('No tags', 'energy-alabama-kb'),
            'items_list'                 => __('Tags list', 'energy-alabama-kb'),
            'items_list_navigation'      => __('Tags list navigation', 'energy-alabama-kb'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rest_base'                  => 'kb-tags',
            'rest_controller_class'      => 'WP_REST_Terms_Controller',
            'query_var'                  => true,
            'rewrite'                    => array(
                'slug'       => 'kb-tag',
                'with_front' => false,
            ),
        );

        register_taxonomy('kb_tag', array('kb_article', 'docket'), $args);
    }

    /**
     * Register Docket Jurisdiction taxonomy
     */
    private function register_docket_jurisdiction() {
        $labels = array(
            'name'                       => _x('Jurisdictions', 'Taxonomy General Name', 'energy-alabama-kb'),
            'singular_name'              => _x('Jurisdiction', 'Taxonomy Singular Name', 'energy-alabama-kb'),
            'menu_name'                  => __('Jurisdictions', 'energy-alabama-kb'),
            'all_items'                  => __('All Jurisdictions', 'energy-alabama-kb'),
            'parent_item'                => __('Parent Jurisdiction', 'energy-alabama-kb'),
            'parent_item_colon'          => __('Parent Jurisdiction:', 'energy-alabama-kb'),
            'new_item_name'              => __('New Jurisdiction Name', 'energy-alabama-kb'),
            'add_new_item'               => __('Add New Jurisdiction', 'energy-alabama-kb'),
            'edit_item'                  => __('Edit Jurisdiction', 'energy-alabama-kb'),
            'update_item'                => __('Update Jurisdiction', 'energy-alabama-kb'),
            'view_item'                  => __('View Jurisdiction', 'energy-alabama-kb'),
            'separate_items_with_commas' => __('Separate jurisdictions with commas', 'energy-alabama-kb'),
            'add_or_remove_items'        => __('Add or remove jurisdictions', 'energy-alabama-kb'),
            'choose_from_most_used'      => __('Choose from the most used', 'energy-alabama-kb'),
            'popular_items'              => __('Popular Jurisdictions', 'energy-alabama-kb'),
            'search_items'               => __('Search Jurisdictions', 'energy-alabama-kb'),
            'not_found'                  => __('Not Found', 'energy-alabama-kb'),
            'no_terms'                   => __('No jurisdictions', 'energy-alabama-kb'),
            'items_list'                 => __('Jurisdictions list', 'energy-alabama-kb'),
            'items_list_navigation'      => __('Jurisdictions list navigation', 'energy-alabama-kb'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => false,
            'show_in_rest'               => true,
            'rest_base'                  => 'jurisdictions',
            'rest_controller_class'      => 'WP_REST_Terms_Controller',
            'query_var'                  => true,
            'rewrite'                    => array(
                'slug'         => 'jurisdiction',
                'with_front'   => false,
                'hierarchical' => true,
            ),
        );

        register_taxonomy('docket_jurisdiction', array('docket'), $args);
    }

    /**
     * Custom meta box for categories with better UX
     */
    public function category_meta_box($post, $box) {
        $defaults = array('taxonomy' => 'kb_category');
        if (!isset($box['args']) || !is_array($box['args'])) {
            $args = array();
        } else {
            $args = $box['args'];
        }
        $r = wp_parse_args($args, $defaults);
        $tax_name = esc_attr($r['taxonomy']);
        $taxonomy = get_taxonomy($r['taxonomy']);
        ?>
<div id="taxonomy-<?php echo $tax_name; ?>" class="categorydiv">
    <ul id="<?php echo $tax_name; ?>-tabs" class="category-tabs">
        <li class="tabs"><a href="#<?php echo $tax_name; ?>-all"><?php echo $taxonomy->labels->all_items; ?></a></li>
    </ul>

    <div id="<?php echo $tax_name; ?>-all" class="tabs-panel">
        <?php
                $name = ($tax_name == 'category') ? 'post_category' : 'tax_input[' . $tax_name . ']';
                echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
                ?>
        <ul id="<?php echo $tax_name; ?>checklist" data-wp-lists="list:<?php echo $tax_name; ?>"
            class="categorychecklist form-no-clear">
            <?php wp_terms_checklist($post->ID, array('taxonomy' => $tax_name, 'popular_cats' => false)); ?>
        </ul>
    </div>
</div>
<?php
    }

    /**
     * Create default taxonomy terms
     */
    private function create_default_terms() {
        // Only create terms if they don't exist
        if (!term_exists('clean-energy-101', 'kb_category')) {
            // KB Categories based on the client's document
            $categories = array(
                'clean-energy-101' => array(
                    'name' => 'Clean Energy 101',
                    'description' => 'Basic information about clean energy technologies and concepts'
                ),
                'educator-resources' => array(
                    'name' => 'Educator Resources',
                    'description' => 'Materials for teachers and educational professionals'
                ),
                'legal-regulatory' => array(
                    'name' => 'Legal & Regulatory',
                    'description' => 'Legal documents, dockets, and regulatory information'
                ),
                'presentation-library' => array(
                    'name' => 'Presentation Library',
                    'description' => 'Downloadable presentations and slides'
                ),
                'faqs' => array(
                    'name' => 'FAQs',
                    'description' => 'Frequently asked questions about clean energy'
                )
            );

            foreach ($categories as $slug => $category) {
                wp_insert_term(
                    $category['name'],
                    'kb_category',
                    array(
                        'slug' => $slug,
                        'description' => $category['description']
                    )
                );
            }
        }

        // Create default jurisdictions
        if (!term_exists('alabama', 'docket_jurisdiction')) {
            $jurisdictions = array(
                'alabama' => array(
                    'name' => 'Alabama',
                    'description' => 'Alabama state regulatory documents'
                ),
                'federal' => array(
                    'name' => 'Federal',
                    'description' => 'Federal regulatory documents and dockets'
                )
            );

            foreach ($jurisdictions as $slug => $jurisdiction) {
                wp_insert_term(
                    $jurisdiction['name'],
                    'docket_jurisdiction',
                    array(
                        'slug' => $slug,
                        'description' => $jurisdiction['description']
                    )
                );
            }
        }

        // Create some default tags
        if (!term_exists('solar', 'kb_tag')) {
            $tags = array(
                'solar', 'wind', 'hydroelectric', 'biomass', 'geothermal',
                'energy-efficiency', 'renewable-energy', 'sustainability',
                'policy', 'regulations', 'utility', 'grid', 'storage'
            );

            foreach ($tags as $tag) {
                wp_insert_term($tag, 'kb_tag');
            }
        }
    }
}