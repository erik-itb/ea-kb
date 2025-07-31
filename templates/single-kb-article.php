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

// Helper function for resource icons
function eakb_get_resource_icon($type) {
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

<div class="eakb-single-article">
    
    <?php while (have_posts()) : the_post(); ?>

        <!-- Hero Section - EXACT COPY from main Knowledge Base page -->
        <section class="eakb-hero">
            <div class="eakb-container">
                <div class="eakb-hero-content">
                    <h1 class="eakb-hero-title"><?php the_title(); ?></h1>
                    
                    <?php if (has_excerpt()): ?>
                        <p class="eakb-hero-description">
                            <?php the_excerpt(); ?>
                        </p>
                    <?php endif; ?>
                    
                    <!-- Search Form -->
                    <div class="eakb-search-container">
                        <form class="eakb-search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                            <div class="eakb-search-wrapper">
                                <input type="search" 
                                        class="eakb-search-input" 
                                        placeholder="<?php esc_attr_e('Search knowledge base...', 'energy-alabama-kb'); ?>"
                                        value="<?php echo get_search_query(); ?>" 
                                        name="s" 
                                        autocomplete="off"
                                        aria-label="<?php esc_attr_e('Search knowledge base', 'energy-alabama-kb'); ?>">
                                <input type="hidden" name="post_type" value="kb_article">
                                <button type="submit" class="eakb-search-button" aria-label="<?php esc_attr_e('Search', 'energy-alabama-kb'); ?>">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="m21 21-4.35-4.35"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="eakb-search-results" style="display: none;"></div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Article Meta Information (below hero) -->
        <section class="eakb-article-meta-section">
            <div class="eakb-container">
                <!-- Pills Section -->
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
                
                <div class="eakb-article-details">
                    <?php
                    // Get author information
                    $author_id = get_the_author_meta('ID');
                    $author_name = get_the_author_meta('display_name');
                    $author_avatar = get_avatar_url($author_id, array('size' => 40));
                    ?>
                    
                    <div class="eakb-author-info">
                        <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" class="eakb-author-avatar">
                        <span class="eakb-author-name"><?php echo esc_html($author_name); ?></span>
                    </div>
                    
                    <span class="eakb-article-date">
                        <?php echo get_the_date('M j, Y'); ?>
                    </span>
                    
                    <?php if ($spanish_available && $spanish_post_id): ?>
                        <a href="<?php echo esc_url(get_permalink($spanish_post_id)); ?>" class="eakb-spanish-link">
                            <?php _e('Ver en español', 'energy-alabama-kb'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Article Content -->
        <section class="eakb-article-content">
            <div class="eakb-container">
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
                                        <?php echo eakb_get_resource_icon($resource['type']); ?>
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

<?php get_footer(); ?>