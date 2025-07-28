<?php
/**
 * Template for single KB Article
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
$difficulty = get_post_meta(get_the_ID(), '_eakb_difficulty_level', true);
$read_time = get_post_meta(get_the_ID(), '_eakb_read_time', true);
$resources = $meta_fields->get_article_resources(get_the_ID());
$spanish_available = get_post_meta(get_the_ID(), '_eakb_spanish_available', true);
$spanish_post_id = get_post_meta(get_the_ID(), '_eakb_spanish_post_id', true);
?>

<div class="eakb-single-article">
    
    <?php while (have_posts()) : the_post(); ?>
        
        <!-- Hero Section with Featured Image -->
        <section class="eakb-article-hero">
            <div class="eakb-container">
                <?php if (has_post_thumbnail()): ?>
                    <div class="eakb-article-featured-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>
                
                <div class="eakb-article-header">
                    <div class="eakb-article-meta">
                        <?php
                        $categories = get_the_terms(get_the_ID(), 'kb_category');
                        if ($categories && !is_wp_error($categories)):
                        ?>
                            <span class="eakb-article-category">
                                <a href="<?php echo esc_url(get_term_link($categories[0])); ?>">
                                    <?php echo esc_html($categories[0]->name); ?>
                                </a>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($difficulty): ?>
                            <span class="eakb-article-difficulty eakb-difficulty-<?php echo esc_attr($difficulty); ?>">
                                <?php echo esc_html(ucfirst($difficulty)); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($read_time): ?>
                            <span class="eakb-article-read-time">
                                <?php printf(__('%d min read', 'energy-alabama-kb'), $read_time); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <h1 class="eakb-article-title"><?php the_title(); ?></h1>
                    
                    <?php if (has_excerpt()): ?>
                        <div class="eakb-article-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="eakb-article-details">
                        <span class="eakb-article-date">
                            <?php printf(__('Published: %s', 'energy-alabama-kb'), get_the_date()); ?>
                        </span>
                        
                        <?php if ($spanish_available && $spanish_post_id): ?>
                            <a href="<?php echo esc_url(get_permalink($spanish_post_id)); ?>" class="eakb-spanish-link">
                                <?php _e('Ver en español', 'energy-alabama-kb'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Article Content -->
        <section class="eakb-article-content">
            <div class="eakb-container">
                <div class="eakb-content-wrapper">
                    <div class="eakb-main-content">
                        <?php the_content(); ?>
                    </div>
                    
                    <!-- Resources Section -->
                    <?php if (!empty($resources)): ?>
                        <div class="eakb-resources-section">
                            <h3><?php _e('Resources & Downloads', 'energy-alabama-kb'); ?></h3>
                            
                            <div class="eakb-resources-list">
                                <?php foreach ($resources as $resource): ?>
                                    <div class="eakb-resource-item">
                                        <div class="eakb-resource-icon">
                                            <?php echo $this->get_resource_icon($resource['type']); ?>
                                        </div>
                                        
                                        <div class="eakb-resource-content">
                                            <h4 class="eakb-resource-title">
                                                <a href="<?php echo esc_url($resource['url']); ?>" target="_blank" rel="noopener">
                                                    <?php echo esc_html($resource['title']); ?>
                                                </a>
                                            </h4>
                                            
                                            <?php if (!empty($resource['description'])): ?>
                                                <p class="eakb-resource-description">
                                                    <?php echo esc_html($resource['description']); ?>
                                                </p>
                                            <?php endif; ?>
                                            
                                            <span class="eakb-resource-type">
                                                <?php echo esc_html($meta_fields->get_resource_type_display_name($resource['type'])); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="eakb-resource-actions">
                                            <a href="<?php echo esc_url($resource['url']); ?>" target="_blank" class="eakb-resource-download">
                                                <?php _e('Download', 'energy-alabama-kb'); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Tags -->
        <?php if (has_term('', 'kb_tag')): ?>
            <section class="eakb-article-tags">
                <div class="eakb-container">
                    <h3><?php _e('Tags', 'energy-alabama-kb'); ?></h3>
                    <div class="eakb-tags-list">
                        <?php
                        $tags = get_the_terms(get_the_ID(), 'kb_tag');
                        if ($tags && !is_wp_error($tags)):
                            foreach ($tags as $tag):
                        ?>
                            <a href="<?php echo esc_url(get_term_link($tag)); ?>" class="eakb-tag">
                                <?php echo esc_html($tag->name); ?>
                            </a>
                        <?php 
                            endforeach;
                        endif; 
                        ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

    <?php endwhile; ?>
    
</div>

<?php
// Helper function for resource icons
function get_resource_icon($type) {
    $icons = array(
        'pdf' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>',
        'doc' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>',
        'sheet' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>',
        'video' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M8,5.14V19.14L19,12.14L8,5.14Z"/></svg>',
        'external' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z"/></svg>'
    );
    
    return isset($icons[$type]) ? $icons[$type] : $icons['external'];
}
?>

<?php get_footer(); ?>