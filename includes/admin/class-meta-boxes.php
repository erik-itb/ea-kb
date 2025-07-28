<?php
/**
 * Meta boxes for admin interface
 *
 * @package Energy_Alabama_KB
 * @since   1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Meta boxes class
 */
class Energy_Alabama_KB_Meta_Boxes {

    /**
     * Meta fields instance
     */
    private $meta_fields;

    /**
     * Initialize the class
     */
    public function __construct() {
        $this->meta_fields = Energy_Alabama_KB_Meta_Fields::get_instance();
        
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'), 10, 2);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Get instance (singleton pattern)
     */
    public static function get_instance() {
        static $instance = null;
        if (null === self::$instance) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Add meta boxes to edit screens
     */
    public function add_meta_boxes() {
        // KB Article meta boxes
        add_meta_box(
            'eakb_article_details',
            __('Article Details', 'energy-alabama-kb'),
            array($this, 'render_article_details_meta_box'),
            'kb_article',
            'normal',
            'high'
        );

        add_meta_box(
            'eakb_article_resources',
            __('Resources & Attachments', 'energy-alabama-kb'),
            array($this, 'render_article_resources_meta_box'),
            'kb_article',
            'normal',
            'default'
        );

        add_meta_box(
            'eakb_spanish_content',
            __('Spanish Content', 'energy-alabama-kb'),
            array($this, 'render_spanish_content_meta_box'),
            'kb_article',
            'side',
            'default'
        );

        // Docket meta boxes
        add_meta_box(
            'eakb_docket_details',
            __('Docket Information', 'energy-alabama-kb'),
            array($this, 'render_docket_details_meta_box'),
            'docket',
            'normal',
            'high'
        );

        add_meta_box(
            'eakb_docket_documents',
            __('Document Categories', 'energy-alabama-kb'),
            array($this, 'render_docket_documents_meta_box'),
            'docket',
            'normal',
            'default'
        );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if (!in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }

        global $post_type;
        if (!in_array($post_type, array('kb_article', 'docket'))) {
            return;
        }

        wp_enqueue_script(
            'eakb-meta-boxes',
            EAKB_PLUGIN_URL . 'assets/js/meta-boxes.js',
            array('jquery', 'jquery-ui-sortable'),
            EAKB_VERSION,
            true
        );

        wp_enqueue_style(
            'eakb-meta-boxes',
            EAKB_PLUGIN_URL . 'assets/css/meta-boxes.css',
            array(),
            EAKB_VERSION
        );

        wp_localize_script('eakb-meta-boxes', 'eakb_meta', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('eakb_meta_nonce'),
            'strings' => array(
                'remove_resource' => __('Remove Resource', 'energy-alabama-kb'),
                'add_document' => __('Add Document', 'energy-alabama-kb'),
                'remove_category' => __('Remove Category', 'energy-alabama-kb'),
                'confirm_remove' => __('Are you sure you want to remove this item?', 'energy-alabama-kb')
            )
        ));
    }

    /**
     * Render Article Details meta box
     */
    public function render_article_details_meta_box($post) {
        wp_nonce_field('eakb_article_details_nonce', 'eakb_article_details_nonce');

        $difficulty = get_post_meta($post->ID, '_eakb_difficulty_level', true) ?: 'beginner';
        $featured_icon = get_post_meta($post->ID, '_eakb_featured_icon', true);
        $read_time = get_post_meta($post->ID, '_eakb_read_time', true);
        ?>
        <table class="form-table eakb-meta-table">
            <tr>
                <th scope="row">
                    <label for="eakb_difficulty_level"><?php _e('Difficulty Level', 'energy-alabama-kb'); ?></label>
                </th>
                <td>
                    <select name="eakb_difficulty_level" id="eakb_difficulty_level" class="regular-text">
                        <option value="beginner" <?php selected($difficulty, 'beginner'); ?>><?php _e('Beginner', 'energy-alabama-kb'); ?></option>
                        <option value="intermediate" <?php selected($difficulty, 'intermediate'); ?>><?php _e('Intermediate', 'energy-alabama-kb'); ?></option>
                        <option value="advanced" <?php selected($difficulty, 'advanced'); ?>><?php _e('Advanced', 'energy-alabama-kb'); ?></option>
                    </select>
                    <p class="description"><?php _e('Select the appropriate difficulty level for this article.', 'energy-alabama-kb'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="eakb_featured_icon"><?php _e('Featured Icon', 'energy-alabama-kb'); ?></label>
                </th>
                <td>
                    <div class="eakb-icon-picker">
                        <input type="hidden" name="eakb_featured_icon" id="eakb_featured_icon" value="<?php echo esc_attr($featured_icon); ?>">
                        <div class="eakb-icon-preview">
                            <?php if ($featured_icon): ?>
                                <span class="eakb-icon" data-icon="<?php echo esc_attr($featured_icon); ?>">
                                    <?php echo $this->get_icon_svg($featured_icon); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="button eakb-choose-icon"><?php _e('Choose Icon', 'energy-alabama-kb'); ?></button>
                        <button type="button" class="button eakb-remove-icon" <?php echo $featured_icon ? '' : 'style="display:none;"'; ?>><?php _e('Remove Icon', 'energy-alabama-kb'); ?></button>
                    </div>
                    <p class="description"><?php _e('Optional icon to represent this article.', 'energy-alabama-kb'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="eakb_read_time"><?php _e('Estimated Read Time', 'energy-alabama-kb'); ?></label>
                </th>
                <td>
                    <input type="number" name="eakb_read_time" id="eakb_read_time" value="<?php echo esc_attr($read_time); ?>" class="small-text" min="1" max="60">
                    <span class="description"><?php _e('minutes (leave blank to auto-calculate)', 'energy-alabama-kb'); ?></span>
                    <p class="description"><?php _e('Estimated reading time will be calculated automatically if left blank.', 'energy-alabama-kb'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render Article Resources meta box
     */
    public function render_article_resources_meta_box($post) {
        wp_nonce_field('eakb_article_resources_nonce', 'eakb_article_resources_nonce');

        $resources = $this->meta_fields->get_article_resources($post->ID);
        ?>
        <div class="eakb-resources-container">
            <div class="eakb-resources-list">
                <?php if (!empty($resources)): ?>
                    <?php foreach ($resources as $index => $resource): ?>
                        <?php $this->render_resource_item($resource, $index); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="eakb-add-resource">
                <button type="button" class="button button-secondary eakb-add-resource-btn">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php _e('Add Resource', 'energy-alabama-kb'); ?>
                </button>
            </div>
        </div>

        <!-- Resource template (hidden) -->
        <script type="text/template" id="eakb-resource-template">
            <?php $this->render_resource_item(array(), '{{INDEX}}'); ?>
        </script>
        <?php
    }

    /**
     * Render individual resource item
     */
    private function render_resource_item($resource, $index) {
        $title = isset($resource['title']) ? $resource['title'] : '';
        $type = isset($resource['type']) ? $resource['type'] : 'external';
        $url = isset($resource['url']) ? $resource['url'] : '';
        $description = isset($resource['description']) ? $resource['description'] : '';
        $embed_preference = isset($resource['embed_preference']) ? $resource['embed_preference'] : 'link';
        ?>
        <div class="eakb-resource-item" data-index="<?php echo esc_attr($index); ?>">
            <div class="eakb-resource-header">
                <span class="eakb-resource-handle dashicons dashicons-menu"></span>
                <h4 class="eakb-resource-title-display">
                    <?php echo $title ? esc_html($title) : __('New Resource', 'energy-alabama-kb'); ?>
                </h4>
                <div class="eakb-resource-actions">
                    <button type="button" class="button-link eakb-toggle-resource">
                        <span class="dashicons dashicons-arrow-down"></span>
                    </button>
                    <button type="button" class="button-link eakb-remove-resource">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                </div>
            </div>
            
            <div class="eakb-resource-content" style="display: none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label><?php _e('Title', 'energy-alabama-kb'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   name="eakb_resources[<?php echo esc_attr($index); ?>][title]" 
                                   value="<?php echo esc_attr($title); ?>" 
                                   class="regular-text eakb-resource-title-input" 
                                   placeholder="<?php esc_attr_e('Resource title', 'energy-alabama-kb'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php _e('Type', 'energy-alabama-kb'); ?></label>
                        </th>
                        <td>
                            <select name="eakb_resources[<?php echo esc_attr($index); ?>][type]" class="regular-text">
                                <?php foreach ($this->get_resource_types() as $value => $label): ?>
                                    <option value="<?php echo esc_attr($value); ?>" <?php selected($type, $value); ?>><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php _e('URL or File', 'energy-alabama-kb'); ?></label>
                        </th>
                        <td>
                            <input type="url" 
                                   name="eakb_resources[<?php echo esc_attr($index); ?>][url]" 
                                   value="<?php echo esc_attr($url); ?>" 
                                   class="regular-text" 
                                   placeholder="<?php esc_attr_e('https://example.com/file.pdf', 'energy-alabama-kb'); ?>">
                            <button type="button" class="button eakb-upload-file"><?php _e('Upload File', 'energy-alabama-kb'); ?></button>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php _e('Description', 'energy-alabama-kb'); ?></label>
                        </th>
                        <td>
                            <textarea name="eakb_resources[<?php echo esc_attr($index); ?>][description]" 
                                      class="large-text" 
                                      rows="3" 
                                      placeholder="<?php esc_attr_e('Brief description of this resource', 'energy-alabama-kb'); ?>"><?php echo esc_textarea($description); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php _e('Display Preference', 'energy-alabama-kb'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="radio" 
                                       name="eakb_resources[<?php echo esc_attr($index); ?>][embed_preference]" 
                                       value="embed" 
                                       <?php checked($embed_preference, 'embed'); ?>>
                                <?php _e('Embed (show content inline)', 'energy-alabama-kb'); ?>
                            </label><br>
                            <label>
                                <input type="radio" 
                                       name="eakb_resources[<?php echo esc_attr($index); ?>][embed_preference]" 
                                       value="link" 
                                       <?php checked($embed_preference, 'link'); ?>>
                                <?php _e('Link (show as download/external link)', 'energy-alabama-kb'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * Render Spanish Content meta box
     */
    public function render_spanish_content_meta_box($post) {
        wp_nonce_field('eakb_spanish_content_nonce', 'eakb_spanish_content_nonce');

        $spanish_available = get_post_meta($post->ID, '_eakb_spanish_available', true);
        $spanish_post_id = get_post_meta($post->ID, '_eakb_spanish_post_id', true);
        ?>
        <div class="eakb-spanish-content">
            <p>
                <label>
                    <input type="checkbox" 
                           name="eakb_spanish_available" 
                           value="1" 
                           <?php checked($spanish_available, 1); ?> 
                           class="eakb-spanish-toggle">
                    <?php _e('Spanish version available', 'energy-alabama-kb'); ?>
                </label>
            </p>
            
            <div class="eakb-spanish-fields" style="<?php echo $spanish_available ? '' : 'display: none;'; ?>">
                <p>
                    <label for="eakb_spanish_post_id"><?php _e('Spanish Article', 'energy-alabama-kb'); ?></label>
                    <select name="eakb_spanish_post_id" id="eakb_spanish_post_id" class="widefat">
                        <option value=""><?php _e('Select Spanish Article', 'energy-alabama-kb'); ?></option>
                        <?php
                        $spanish_articles = get_posts(array(
                            'post_type' => 'kb_article',
                            'posts_per_page' => -1,
                            'post_status' => array('publish', 'draft'),
                            'exclude' => array($post->ID),
                            'orderby' => 'title',
                            'order' => 'ASC'
                        ));
                        
                        foreach ($spanish_articles as $article) {
                            printf(
                                '<option value="%d" %s>%s</option>',
                                $article->ID,
                                selected($spanish_post_id, $article->ID, false),
                                esc_html($article->post_title)
                            );
                        }
                        ?>
                    </select>
                </p>
                
                <p class="description">
                    <?php _e('Link to the Spanish version of this article.', 'energy-alabama-kb'); ?>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Render Docket Details meta box
     */
    public function render_docket_details_meta_box($post) {
        wp_nonce_field('eakb_docket_details_nonce', 'eakb_docket_details_nonce');

        $docket_number = get_post_meta($post->ID, '_eakb_docket_number', true);
        $status = get_post_meta($post->ID, '_eakb_docket_status', true) ?: 'pending';
        $filing_date = get_post_meta($post->ID, '_eakb_filing_date', true);
        $proceeding_type = get_post_meta($post->ID, '_eakb_proceeding_type', true);
        $related_dockets = get_post_meta($post->ID, '_eakb_related_dockets', true);
        ?>
        <table class="form-table eakb-meta-table">
            <tr>
                <th scope="row">
                    <label for="eakb_docket_number"><?php _e('Docket Number', 'energy-alabama-kb'); ?></label>
                </th>
                <td>
                    <input type="text" 
                           name="eakb_docket_number" 
                           id="eakb_docket_number" 
                           value="<?php echo esc_attr($docket_number); ?>" 
                           class="regular-text" 
                           placeholder="<?php esc_attr_e('e.g., UD-24-02', 'energy-alabama-kb'); ?>">
                    <p class="description"><?php _e('Official docket number assigned by the regulatory body.', 'energy-alabama-kb'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="eakb_docket_status"><?php _e('Status', 'energy-alabama-kb'); ?></label>
                </th>
                <td>
                    <select name="eakb_docket_status" id="eakb_docket_status" class="regular-text">
                        <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pending', 'energy-alabama-kb'); ?></option>
                        <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'energy-alabama-kb'); ?></option>
                        <option value="on-hold" <?php selected($status, 'on-hold'); ?>><?php _e('On Hold', 'energy-alabama-kb'); ?></option>
                        <option value="closed" <?php selected($status, 'closed'); ?>><?php _e('Closed', 'energy-alabama-kb'); ?></option>
                        <option value="appealed" <?php selected($status, 'appealed'); ?>><?php _e('Appealed', 'energy-alabama-kb'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="eakb_filing_date"><?php _e('Filing Date', 'energy-alabama-kb'); ?></label>
                </th>
                <td>
                    <input type="date" 
                           name="eakb_filing_date" 
                           id="eakb_filing_date" 
                           value="<?php echo esc_attr($filing_date); ?>" 
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="eakb_proceeding_type"><?php _e('Proceeding Type', 'energy-alabama-kb'); ?></label>
                </th>
                <td>
                    <input type="text" 
                           name="eakb_proceeding_type" 
                           id="eakb_proceeding_type" 
                           value="<?php echo esc_attr($proceeding_type); ?>" 
                           class="regular-text" 
                           placeholder="<?php esc_attr_e('e.g., Rate Case, Certificate', 'energy-alabama-kb'); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="eakb_related_dockets"><?php _e('Related Dockets', 'energy-alabama-kb'); ?></label>
                </th>
                <td>
                    <input type="text" 
                           name="eakb_related_dockets" 
                           id="eakb_related_dockets" 
                           value="<?php echo esc_attr($related_dockets); ?>" 
                           class="regular-text" 
                           placeholder="<?php esc_attr_e('Comma-separated docket numbers', 'energy-alabama-kb'); ?>">
                    <p class="description"><?php _e('Enter related docket numbers separated by commas.', 'energy-alabama-kb'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render Docket Documents meta box
     */
    public function render_docket_documents_meta_box($post) {
        wp_nonce_field('eakb_docket_documents_nonce', 'eakb_docket_documents_nonce');

        $categories = $this->meta_fields->get_docket_categories($post->ID);
        ?>
        <div class="eakb-docket-categories">
            <p class="description">
                <?php _e('Organize documents into categories. Each category will display as an accordion section on the docket page.', 'energy-alabama-kb'); ?>
            </p>
            
            <div class="eakb-categories-list">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $index => $category): ?>
                        <?php $this->render_docket_category($category, $index); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="eakb-add-category">
                <button type="button" class="button button-secondary eakb-add-category-btn">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php _e('Add Document Category', 'energy-alabama-kb'); ?>
                </button>
            </div>
        </div>

        <!-- Category template (hidden) -->
        <script type="text/template" id="eakb-category-template">
            <?php $this->render_docket_category(array(), '{{INDEX}}'); ?>
        </script>
        <?php
    }

    /**
     * Render individual docket category
     */
    private function render_docket_category($category, $index) {
        $name = isset($category['name']) ? $category['name'] : '';
        $description = isset($category['description']) ? $category['description'] : '';
        $documents = isset($category['documents']) ? $category['documents'] : array();
        $order = isset($category['order']) ? $category['order'] : 0;
        ?>
        <div class="eakb-category-item" data-index="<?php echo esc_attr($index); ?>">
            <div class="eakb-category-header">
                <span class="eakb-category-handle dashicons dashicons-menu"></span>
                <h4 class="eakb-category-title-display">
                    <?php echo $name ? esc_html($name) : __('New Category', 'energy-alabama-kb'); ?>
                </h4>
                <div class="eakb-category-actions">
                    <button type="button" class="button-link eakb-toggle-category">
                        <span class="dashicons dashicons-arrow-down"></span>
                    </button>
                    <button type="button" class="button-link eakb-remove-category">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                </div>
            </div>
            
            <div class="eakb-category-content" style="display: none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label><?php _e('Category Name', 'energy-alabama-kb'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   name="eakb_categories[<?php echo esc_attr($index); ?>][name]" 
                                   value="<?php echo esc_attr($name); ?>" 
                                   class="regular-text eakb-category-title-input" 
                                   placeholder="<?php esc_attr_e('e.g., Initial Filings', 'energy-alabama-kb'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php _e('Description', 'energy-alabama-kb'); ?></label>
                        </th>
                        <td>
                            <textarea name="eakb_categories[<?php echo esc_attr($index); ?>][description]" 
                                      class="large-text" 
                                      rows="2" 
                                      placeholder="<?php esc_attr_e('Optional description for this category', 'energy-alabama-kb'); ?>"><?php echo esc_textarea($description); ?></textarea>
                        </td>
                    </tr>
                </table>
                
                <div class="eakb-documents-section">
                    <h5><?php _e('Documents', 'energy-alabama-kb'); ?></h5>
                    <div class="eakb-documents-list">
                        <?php if (!empty($documents)): ?>
                            <?php foreach ($documents as $doc_index => $document): ?>
                                <?php $this->render_document_item($document, $index, $doc_index); ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="button button-small eakb-add-document">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php _e('Add Document', 'energy-alabama-kb'); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render individual document item
     */
    private function render_document_item($document, $category_index, $doc_index) {
        $title = isset($document['title']) ? $document['title'] : '';
        $url = isset($document['url']) ? $document['url'] : '';
        $type = isset($document['type']) ? $document['type'] : 'pdf';
        $date = isset($document['date']) ? $document['date'] : '';
        $file_size = isset($document['file_size']) ? $document['file_size'] : '';
        ?>
        <div class="eakb-document-item">
            <div class="eakb-document-fields">
                <input type="text" 
                       name="eakb_categories[<?php echo esc_attr($category_index); ?>][documents][<?php echo esc_attr($doc_index); ?>][title]" 
                       value="<?php echo esc_attr($title); ?>" 
                       placeholder="<?php esc_attr_e('Document title', 'energy-alabama-kb'); ?>" 
                       class="regular-text">
                
                <select name="eakb_categories[<?php echo esc_attr($category_index); ?>][documents][<?php echo esc_attr($doc_index); ?>][type]">
                    <?php foreach ($this->get_document_types() as $value => $label): ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected($type, $value); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <input type="url" 
                       name="eakb_categories[<?php echo esc_attr($category_index); ?>][documents][<?php echo esc_attr($doc_index); ?>][url]" 
                       value="<?php echo esc_attr($url); ?>" 
                       placeholder="<?php esc_attr_e('Document URL', 'energy-alabama-kb'); ?>" 
                       class="regular-text">
                
                <input type="date" 
                       name="eakb_categories[<?php echo esc_attr($category_index); ?>][documents][<?php echo esc_attr($doc_index); ?>][date]" 
                       value="<?php echo esc_attr($date); ?>" 
                       class="regular-text">
                
                <button type="button" class="button-link eakb-remove-document">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
        </div>
        <?php
    }

    /**
     * Save meta box data
     */
    public function save_meta_boxes($post_id, $post) {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check post type
        if (!in_array($post->post_type, array('kb_article', 'docket'))) {
            return;
        }

        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if ($post->post_type === 'kb_article') {
            $this->save_article_meta($post_id);
        } elseif ($post->post_type === 'docket') {
            $this->save_docket_meta($post_id);
        }
    }

    /**
     * Save KB Article meta data
     */
    private function save_article_meta($post_id) {
        // Verify nonces
        if (!wp_verify_nonce($_POST['eakb_article_details_nonce'] ?? '', 'eakb_article_details_nonce')) {
            return;
        }

        // Save article details
        if (isset($_POST['eakb_difficulty_level'])) {
            update_post_meta($post_id, '_eakb_difficulty_level', sanitize_text_field($_POST['eakb_difficulty_level']));
        }

        if (isset($_POST['eakb_featured_icon'])) {
            update_post_meta($post_id, '_eakb_featured_icon', sanitize_text_field($_POST['eakb_featured_icon']));
        }

        // Handle read time
        if (isset($_POST['eakb_read_time']) && !empty($_POST['eakb_read_time'])) {
            update_post_meta($post_id, '_eakb_read_time', absint($_POST['eakb_read_time']));
        } else {
            // Auto-calculate read time
            $post = get_post($post_id);
            $read_time = $this->meta_fields->calculate_read_time($post->post_content);
            update_post_meta($post_id, '_eakb_read_time', $read_time);
        }

        // Save resources
        if (wp_verify_nonce($_POST['eakb_article_resources_nonce'] ?? '', 'eakb_article_resources_nonce')) {
            $resources = array();
            if (isset($_POST['eakb_resources']) && is_array($_POST['eakb_resources'])) {
                foreach ($_POST['eakb_resources'] as $resource_data) {
                    if (!empty($resource_data['title']) || !empty($resource_data['url'])) {
                        $resources[] = array(
                            'title' => sanitize_text_field($resource_data['title'] ?? ''),
                            'type' => sanitize_text_field($resource_data['type'] ?? 'external'),
                            'url' => esc_url_raw($resource_data['url'] ?? ''),
                            'description' => sanitize_textarea_field($resource_data['description'] ?? ''),
                            'embed_preference' => sanitize_text_field($resource_data['embed_preference'] ?? 'link')
                        );
                    }
                }
            }
            update_post_meta($post_id, '_eakb_resources', wp_json_encode($resources));
        }

        // Save Spanish content
        if (wp_verify_nonce($_POST['eakb_spanish_content_nonce'] ?? '', 'eakb_spanish_content_nonce')) {
            $spanish_available = isset($_POST['eakb_spanish_available']);
            update_post_meta($post_id, '_eakb_spanish_available', $spanish_available);
            
            if ($spanish_available && isset($_POST['eakb_spanish_post_id'])) {
                update_post_meta($post_id, '_eakb_spanish_post_id', absint($_POST['eakb_spanish_post_id']));
            } else {
                delete_post_meta($post_id, '_eakb_spanish_post_id');
            }
        }

        // Update last modified by
        update_post_meta($post_id, '_eakb_last_updated_by', get_current_user_id());
    }

    /**
     * Save Docket meta data
     */
    private function save_docket_meta($post_id) {
        // Verify nonces
        if (!wp_verify_nonce($_POST['eakb_docket_details_nonce'] ?? '', 'eakb_docket_details_nonce')) {
            return;
        }

        // Save docket details
        $docket_fields = array(
            'eakb_docket_number' => '_eakb_docket_number',
            'eakb_docket_status' => '_eakb_docket_status',
            'eakb_filing_date' => '_eakb_filing_date',
            'eakb_proceeding_type' => '_eakb_proceeding_type',
            'eakb_related_dockets' => '_eakb_related_dockets'
        );

        foreach ($docket_fields as $field => $meta_key) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$field]));
            }
        }

        // Save document categories
        if (wp_verify_nonce($_POST['eakb_docket_documents_nonce'] ?? '', 'eakb_docket_documents_nonce')) {
            $categories = array();
            if (isset($_POST['eakb_categories']) && is_array($_POST['eakb_categories'])) {
                foreach ($_POST['eakb_categories'] as $category_data) {
                    if (!empty($category_data['name'])) {
                        $documents = array();
                        if (isset($category_data['documents']) && is_array($category_data['documents'])) {
                            foreach ($category_data['documents'] as $doc_data) {
                                if (!empty($doc_data['title']) || !empty($doc_data['url'])) {
                                    $documents[] = array(
                                        'title' => sanitize_text_field($doc_data['title'] ?? ''),
                                        'url' => esc_url_raw($doc_data['url'] ?? ''),
                                        'type' => sanitize_text_field($doc_data['type'] ?? 'pdf'),
                                        'date' => sanitize_text_field($doc_data['date'] ?? ''),
                                        'file_size' => sanitize_text_field($doc_data['file_size'] ?? '')
                                    );
                                }
                            }
                        }

                        $categories[] = array(
                            'name' => sanitize_text_field($category_data['name']),
                            'description' => sanitize_textarea_field($category_data['description'] ?? ''),
                            'documents' => $documents,
                            'order' => count($categories)
                        );
                    }
                }
            }
            update_post_meta($post_id, '_eakb_document_categories', wp_json_encode($categories));
        }
    }

    /**
     * Get resource types for dropdown
     */
    private function get_resource_types() {
        return array(
            'pdf' => __('PDF Document', 'energy-alabama-kb'),
            'doc' => __('Word Document', 'energy-alabama-kb'),
            'sheet' => __('Spreadsheet', 'energy-alabama-kb'),
            'presentation' => __('Presentation', 'energy-alabama-kb'),
            'video' => __('Video', 'energy-alabama-kb'),
            'external' => __('External Link', 'energy-alabama-kb'),
            'google-doc' => __('Google Document', 'energy-alabama-kb'),
            'google-sheet' => __('Google Sheet', 'energy-alabama-kb'),
            'google-slides' => __('Google Slides', 'energy-alabama-kb'),
            'youtube' => __('YouTube Video', 'energy-alabama-kb'),
            'vimeo' => __('Vimeo Video', 'energy-alabama-kb')
        );
    }

    /**
     * Get document types for dropdown
     */
    private function get_document_types() {
        return array(
            'pdf' => __('PDF', 'energy-alabama-kb'),
            'doc' => __('Word Doc', 'energy-alabama-kb'),
            'sheet' => __('Spreadsheet', 'energy-alabama-kb'),
            'presentation' => __('Presentation', 'energy-alabama-kb'),
            'external' => __('External Link', 'energy-alabama-kb')
        );
    }

    /**
     * Get icon SVG (placeholder - will implement icon system later)
     */
    private function get_icon_svg($icon_slug) {
        // Placeholder - return a simple icon for now
        return '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="8"/></svg>';
    }
}