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

<style>
/* KB Article Specific Styles */
.eakb-single-article {
    background: #fff;
    position: relative;
    z-index: 1;
}

.eakb-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Updated Hero Section - Full Width with Background Image */
.eakb-article-hero {
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
    padding: 0; /* Remove extra padding since hero now has 150px */
}

.eakb-article-header {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.eakb-article-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 2rem;
}

.eakb-article-category a,
.eakb-article-difficulty,
.eakb-article-read-time {
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.eakb-article-category a {
    background: rgba(59, 130, 246, 0.3);
    color: #93c5fd;
}

.eakb-difficulty-beginner {
    background: rgba(34, 197, 94, 0.3);
    color: #86efac;
}

.eakb-difficulty-intermediate {
    background: rgba(251, 191, 36, 0.3);
    color: #fde047;
}

.eakb-difficulty-advanced {
    background: rgba(239, 68, 68, 0.3);
    color: #fca5a5;
}

.eakb-article-read-time {
    background: rgba(255, 255, 255, 0.2);
    color: rgba(255, 255, 255, 0.9);
}

.eakb-article-title {
    font-size: 4rem;
    font-weight: 700;
    margin: 0 0 1.5rem 0;
    line-height: 1.1;
    color: white;
    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8);
    letter-spacing: -0.02em;
}

.eakb-article-excerpt {
    font-size: 1.3rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 0;
    line-height: 1.6;
    max-width: 800px;
    text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.6);
}

/* Meta Information Section (below hero) */
.eakb-article-meta-section {
    background: #f8fafc;
    padding: 2rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.eakb-article-details {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Author information styling */
.eakb-author-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.eakb-author-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.eakb-author-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 15px;
}

.eakb-article-date {
    color: #64748b;
    font-size: 15px;
    font-weight: 500;
}

.eakb-spanish-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.eakb-spanish-link:hover {
    text-decoration: underline;
}

.eakb-spanish-link::before {
    content: "🇪🇸";
    font-size: 18px;
}

.eakb-article-content {
    padding: 60px 0;
}

.eakb-main-content {
    max-width: 800px;
    margin: 0 auto;
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

.eakb-resources-section {
    max-width: 800px;
    margin: 60px auto 0;
    padding-top: 60px;
    border-top: 2px solid #e5e7eb;
}

.eakb-resources-section h3 {
    font-size: 1.75rem;
    font-weight: 600;
    margin-bottom: 30px;
    color: #1f2937;
}

.eakb-resources-list {
    display: grid;
    gap: 20px;
}

.eakb-resource-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 20px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.eakb-resource-item:hover {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border-color: #1e40af;
}

.eakb-resource-icon {
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

.eakb-resource-content {
    flex: 1;
}

.eakb-resource-title {
    margin: 0 0 8px 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.eakb-resource-title a {
    color: #1f2937;
    text-decoration: none;
}

.eakb-resource-title a:hover {
    color: #1e40af;
    text-decoration: underline;
}

.eakb-resource-description {
    margin: 0 0 8px 0;
    color: #6b7280;
    font-size: 0.95rem;
}

.eakb-resource-type {
    font-size: 0.875rem;
    color: #9ca3af;
    font-weight: 500;
}

.eakb-resource-actions {
    flex-shrink: 0;
}

.eakb-resource-download {
    padding: 8px 16px;
    background: #1e40af;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background-color 0.2s;
}

.eakb-resource-download:hover {
    background: #1d4ed8;
    color: white;
}

.eakb-article-tags {
    padding: 40px 0;
    background: #f9fafb;
}

.eakb-article-tags h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 20px;
    color: #1f2937;
}

.eakb-tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.eakb-tag {
    padding: 6px 12px;
    background: #e5e7eb;
    color: #374151;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
}

.eakb-tag:hover {
    background: #1e40af;
    color: white;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .eakb-article-title {
        font-size: 3.5rem;
    }
}

@media (max-width: 768px) {
    .eakb-article-title {
        font-size: 2.5rem;
    }
    
    .eakb-article-excerpt {
        font-size: 1.2rem;
    }
    
    .eakb-article-hero {
        padding: 100px 0; /* Reduce padding on mobile */
        border-radius: 0px 0px 60px 0px; /* Smaller radius on mobile */
    }
    
    .eakb-article-details {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .eakb-resource-item {
        flex-direction: column;
        text-align: center;
    }
    
    .eakb-resource-icon {
        align-self: center;
    }
}

@media (max-width: 480px) {
    .eakb-article-title {
        font-size: 2rem;
    }
    
    .eakb-article-excerpt {
        font-size: 1.1rem;
    }
    
    .eakb-article-meta {
        gap: 10px;
    }
}
</style>

<div class="eakb-single-article">
    
    <?php while (have_posts()) : the_post(); ?>
        
    </div> <!-- Close any container before hero -->
    
        <!-- Hero Section with Large Background Image (Full Width) -->
        <section class="eakb-article-hero">
            <?php 
            // Get featured image or use a default background
            $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
            $default_image = get_template_directory_uri() . '/assets/images/default-kb-hero.jpg';
            $hero_image = $featured_image ? $featured_image : $default_image;
            ?>
            
            <div class="eakb-hero-background" style="background-image: url('<?php echo esc_url($hero_image); ?>');"></div>
            <div class="eakb-hero-overlay"></div>
            
            <div class="eakb-hero-content">
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
                </div>
            </div>
        </section>

        <!-- Article Meta Information (below hero) -->
        <section class="eakb-article-meta-section">
            <div class="eakb-container">
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
    
    <div class="eakb-container"> <!-- Reopen container for rest of content -->

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