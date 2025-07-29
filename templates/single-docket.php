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

<style>
/* Docket Specific Styles */
.eakb-single-docket {
    background: #fff;
    position: relative;
    z-index: 1;
}

.eakb-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Hero Section - Full Width with Background Image */
.eakb-docket-hero {
    position: relative;
    width: 100vw;
    margin-left: calc(-50vw + 50%);
    min-height: 70vh;
    display: flex;
    align-items: center;
    overflow: hidden;
    background: #1a1a1a; /* Fallback color */
    border-radius: 0px 0px 120px 0px;
    padding: 150px 0;
}

.eakb-hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.eakb-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.4) 100%);
}

.eakb-hero-content {
    position: relative;
    z-index: 2;
    width: 100%;
    padding: 0;
}

.eakb-docket-header {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.eakb-docket-title {
    font-size: 4rem;
    font-weight: 700;
    margin: 0 0 1.5rem 0;
    line-height: 1.1;
    color: white;
    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8);
    letter-spacing: -0.02em;
}

.eakb-docket-excerpt {
    font-size: 1.3rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 0;
    line-height: 1.6;
    max-width: 800px;
    text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.6);
}

/* Docket Meta Information Section */
.eakb-docket-meta-section {
    background: #f8fafc;
    padding: 2rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.eakb-docket-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 1.5rem;
}

.eakb-docket-number,
.eakb-docket-status,
.eakb-proceeding-type {
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
}

.eakb-docket-number {
    background: #dbeafe;
    color: #1e40af;
}

.eakb-proceeding-type {
    background: #f3f4f6;
    color: #374151;
}

/* Status-specific styling */
.eakb-status-active {
    background: #dcfce7;
    color: #166534;
}

.eakb-status-closed {
    background: #f3f4f6;
    color: #6b7280;
}

.eakb-status-pending {
    background: #fef3c7;
    color: #92400e;
}

.eakb-status-on-hold {
    background: #fee2e2;
    color: #dc2626;
}

.eakb-status-appealed {
    background: #ede9fe;
    color: #7c3aed;
}

.eakb-docket-details {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.eakb-filing-date {
    color: #64748b;
    font-size: 15px;
    font-weight: 500;
}

.eakb-filing-date strong {
    color: #1f2937;
}

.eakb-docket-content {
    padding: 60px 0;
}

.eakb-main-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    font-size: 1.1rem;
    line-height: 1.7;
    color: #374151;
}

.eakb-main-content h2,
.eakb-main-content h3,
.eakb-main-content h4 {
    margin-top: 2em;
    margin-bottom: 1em;
    color: #1f2937;
}

.eakb-main-content p {
    margin-bottom: 1.5em;
}

/* Document Categories Section */
.eakb-documents-section {
    max-width: 1200px;
    margin: 60px auto 0;
    padding: 60px 20px 0;
    border-top: 2px solid #e5e7eb;
}

.eakb-documents-section h3 {
    font-size: 1.75rem;
    font-weight: 600;
    margin-bottom: 30px;
    color: #1f2937;
}

.eakb-document-category {
    margin-bottom: 40px;
    background: #f9fafb;
    border-radius: 12px;
    padding: 30px;
    border: 1px solid #e5e7eb;
}

.eakb-category-header {
    margin-bottom: 20px;
}

.eakb-category-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 8px 0;
}

.eakb-category-description {
    color: #6b7280;
    font-size: 0.95rem;
    margin: 0;
}

.eakb-documents-list {
    display: grid;
    gap: 15px;
}

.eakb-document-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 20px;
    background: white;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.eakb-document-item:hover {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border-color: #1e40af;
}

.eakb-document-icon {
    width: 40px;
    height: 40px;
    background: #dbeafe;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1e40af;
    flex-shrink: 0;
}

.eakb-document-content {
    flex: 1;
}

.eakb-document-title {
    margin: 0 0 8px 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.eakb-document-title a {
    color: #1f2937;
    text-decoration: none;
}

.eakb-document-title a:hover {
    color: #1e40af;
    text-decoration: underline;
}

.eakb-document-meta {
    display: flex;
    gap: 15px;
    align-items: center;
    font-size: 0.875rem;
    color: #6b7280;
}

.eakb-document-date,
.eakb-document-size {
    display: flex;
    align-items: center;
    gap: 5px;
}

.eakb-document-actions {
    flex-shrink: 0;
}

.eakb-document-download {
    padding: 8px 16px;
    background: #1e40af;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background-color 0.2s;
}

.eakb-document-download:hover {
    background: #1d4ed8;
    color: white;
}

/* Related Dockets Section */
.eakb-related-dockets {
    background: #f9fafb;
    padding: 40px 0;
}

.eakb-related-dockets h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 20px;
    color: #1f2937;
}

.eakb-related-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.eakb-related-item {
    background: white;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.eakb-related-item:hover {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.eakb-related-item a {
    color: #1f2937;
    text-decoration: none;
}

.eakb-related-item a:hover {
    color: #1e40af;
}

.eakb-related-item h4 {
    margin: 0 0 8px 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.eakb-related-item p {
    margin: 0;
    color: #6b7280;
    font-size: 0.9rem;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .eakb-docket-title {
        font-size: 3.5rem;
    }
}

@media (max-width: 768px) {
    .eakb-docket-title {
        font-size: 2.5rem;
    }
    
    .eakb-docket-excerpt {
        font-size: 1.2rem;
    }
    
    .eakb-docket-hero {
        padding: 100px 0;
        border-radius: 0px 0px 60px 0px;
    }
    
    .eakb-docket-details {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .eakb-document-item {
        flex-direction: column;
        text-align: center;
    }
    
    .eakb-document-icon {
        align-self: center;
    }
    
    .eakb-related-list {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .eakb-docket-title {
        font-size: 2rem;
    }
    
    .eakb-docket-excerpt {
        font-size: 1.1rem;
    }
    
    .eakb-docket-meta {
        gap: 10px;
    }
}
</style>

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