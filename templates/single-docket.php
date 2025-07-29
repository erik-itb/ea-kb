<?php
/**
 * Template for single Docket
 *
 * @package Energy_Alabama_KB
 * @since   1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get meta fields instance
$meta_fields = Energy_Alabama_KB_Meta_Fields::get_instance();
$docket_number = get_post_meta(get_the_ID(), '_eakb_docket_number', true);
$docket_status = get_post_meta(get_the_ID(), '_eakb_docket_status', true);
$filing_date = get_post_meta(get_the_ID(), '_eakb_filing_date', true);
$proceeding_type = get_post_meta(get_the_ID(), '_eakb_proceeding_type', true);
$document_categories = $meta_fields->get_docket_categories(get_the_ID());
$related_dockets = get_post_meta(get_the_ID(), '_eakb_related_dockets', true);

// Helper function for docket status styling
function eakb_get_docket_status_class($status) {
    $classes = array(
        'active' => 'eakb-status-active',
        'closed' => 'eakb-status-closed',
        'pending' => 'eakb-status-pending',
        'on-hold' => 'eakb-status-on-hold',
        'appealed' => 'eakb-status-appealed'
    );
    
    return isset($classes[$status]) ? $classes[$status] : 'eakb-status-pending';
}

// Helper function for document icons
function eakb_get_document_icon($type) {
    $icons = array(
        'pdf' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>',
        'doc' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>',
        'external' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z"/></svg>'
    );
    
    return isset($icons[$type]) ? $icons[$type] : $icons['external'];
}
?>

<div class="eakb-single-docket">
    
    <?php while (have_posts()) : the_post(); ?>
        
    </div> <!-- Close any container before hero -->
        
        <!-- Hero Section with Large Background Image (Full Width) -->
        <section class="eakb-docket-hero">
            <?php 
            // Get featured image or use a default legal/regulatory background
            $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
            $default_image = get_template_directory_uri() . '/assets/images/default-docket-hero.jpg';
            $hero_image = $featured_image ? $featured_image : $default_image;
            ?>
            
            <div class="eakb-hero-background" style="background-image: url('<?php echo esc_url($hero_image); ?>');"></div>
            <div class="eakb-hero-overlay"></div>
            
            <div class="eakb-hero-content">
                <div class="eakb-docket-header">
                    <h1 class="eakb-docket-title"><?php the_title(); ?></h1>
                    
                    <?php if (has_excerpt()): ?>
                        <div class="eakb-docket-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Docket Meta Information (below hero) -->
        <section class="eakb-docket-meta-section">
            <div class="eakb-container">
                <!-- Docket Info Pills -->
                <div class="eakb-docket-meta">
                    <?php if ($docket_number): ?>
                        <span class="eakb-docket-number">
                            Docket #<?php echo esc_html($docket_number); ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($docket_status): ?>
                        <span class="eakb-docket-status <?php echo esc_attr(eakb_get_docket_status_class($docket_status)); ?>">
                            <?php echo esc_html(ucfirst(str_replace('-', ' ', $docket_status))); ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($proceeding_type): ?>
                        <span class="eakb-proceeding-type">
                            <?php echo esc_html($proceeding_type); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="eakb-docket-details">
                    <?php if ($filing_date): ?>
                        <span class="eakb-filing-date">
                            <strong><?php _e('Filed:', 'energy-alabama-kb'); ?></strong> 
                            <?php echo esc_html(date('M j, Y', strtotime($filing_date))); ?>
                        </span>
                    <?php endif; ?>
                    
                    <span class="eakb-filing-date">
                        <strong><?php _e('Last Updated:', 'energy-alabama-kb'); ?></strong> 
                        <?php echo get_the_modified_date('M j, Y'); ?>
                    </span>
                </div>
            </div>
        </section>
    
    <div class="eakb-container"> <!-- Reopen container for rest of content -->

        <!-- Docket Content -->
        <section class="eakb-docket-content">
            <div class="eakb-main-content">
                <?php the_content(); ?>
            </div>
            
            <!-- Document Categories Section -->
            <?php if (!empty($document_categories)): ?>
                <div class="eakb-documents-section">
                    <h3><?php _e('Documents & Filings', 'energy-alabama-kb'); ?></h3>
                    
                    <?php
                    // Sort categories by order field
                    usort($document_categories, function($a, $b) {
                        $order_a = isset($a['order']) ? (int)$a['order'] : 0;
                        $order_b = isset($b['order']) ? (int)$b['order'] : 0;
                        return $order_a - $order_b;
                    });
                    
                    foreach ($document_categories as $category): 
                        if (!empty($category['documents']) && is_array($category['documents'])):
                    ?>
                        <div class="eakb-document-category">
                            <div class="eakb-category-header">
                                <h4 class="eakb-category-title">
                                    <?php echo esc_html($category['name']); ?>
                                </h4>
                                <?php if (!empty($category['description'])): ?>
                                    <p class="eakb-category-description">
                                        <?php echo esc_html($category['description']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="eakb-documents-list">
                                <?php foreach ($category['documents'] as $document): ?>
                                    <div class="eakb-document-item">
                                        <div class="eakb-document-icon">
                                            <?php echo eakb_get_document_icon($document['type']); ?>
                                        </div>
                                        
                                        <div class="eakb-document-content">
                                            <h5 class="eakb-document-title">
                                                <a href="<?php echo esc_url($document['url']); ?>" target="_blank" rel="noopener">
                                                    <?php echo esc_html($document['title']); ?>
                                                </a>
                                            </h5>
                                            
                                            <div class="eakb-document-meta">
                                                <?php if (!empty($document['date'])): ?>
                                                    <span class="eakb-document-date">
                                                        <strong><?php _e('Date:', 'energy-alabama-kb'); ?></strong>
                                                        <?php echo esc_html(date('M j, Y', strtotime($document['date']))); ?>
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($document['file_size'])): ?>
                                                    <span class="eakb-document-size">
                                                        <strong><?php _e('Size:', 'energy-alabama-kb'); ?></strong>
                                                        <?php echo esc_html($document['file_size']); ?>
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <span class="eakb-document-type">
                                                    <?php echo esc_html($meta_fields->get_resource_type_display_name($document['type'])); ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="eakb-document-actions">
                                            <a href="<?php echo esc_url($document['url']); ?>" target="_blank" class="eakb-document-download">
                                                <?php _e('View', 'energy-alabama-kb'); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Related Dockets -->
        <?php 
        if (!empty($related_dockets)):
            $related_ids = array_map('trim', explode(',', $related_dockets));
            $related_query = new WP_Query(array(
                'post_type' => 'docket',
                'post__in' => $related_ids,
                'posts_per_page' => -1,
                'post_status' => 'publish'
            ));
            
            if ($related_query->have_posts()):
        ?>
            <section class="eakb-related-dockets">
                <div class="eakb-container">
                    <h3><?php _e('Related Dockets', 'energy-alabama-kb'); ?></h3>
                    <div class="eakb-related-list">
                        <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                            <div class="eakb-related-item">
                                <a href="<?php the_permalink(); ?>">
                                    <h4><?php the_title(); ?></h4>
                                    <p><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></p>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </section>
        <?php 
            endif;
            wp_reset_postdata();
        endif; 
        ?>

    </div> <!-- Close container -->

    <?php endwhile; ?>
    
</div>

<?php get_footer(); ?>