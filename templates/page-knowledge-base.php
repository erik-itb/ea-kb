<?php
/**
 * Template for Knowledge Base Landing Page
 *
 * @package Energy_Alabama_KB
 * @since   1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Helper class for icons - only declare if it doesn't exist
if (!class_exists('EAKB_Landing_Template_Helpers')) {
    class EAKB_Landing_Template_Helpers {
        
        public function get_category_icon($slug) {
            $icons = array(
                'clean-energy-101' => 'energy',
                'educator-resources' => 'education',
                'legal-regulatory' => 'legal',
                'presentation-library' => 'presentation',
                'faqs' => 'help'
            );
            
            return isset($icons[$slug]) ? $icons[$slug] : 'default';
        }
        
        public function render_category_icon($slug) {
            $icon_type = $this->get_category_icon($slug);
            
            $icons = array(
                'energy' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>',
                'education' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>',
                'legal' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14,2 14,8 20,8"></polyline></svg>',
                'presentation' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="12" rx="2"></rect><path d="M12 16v4"></path><path d="M8 20h8"></path></svg>',
                'help' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
                'default' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>'
            );
            
            return isset($icons[$icon_type]) ? $icons[$icon_type] : $icons['default'];
        }
    }
}

// Get template manager instance for helper functions
$template_manager = Energy_Alabama_KB_Template_Manager::get_instance();
$categories = $template_manager->get_kb_categories();
$recent_articles = $template_manager->get_recent_articles(6);
$helpers = new EAKB_Landing_Template_Helpers();
?>

<div class="eakb-landing-page">
    
    <!-- Hero Section with Search -->
    <section class="eakb-hero">
        <div class="eakb-container">
            <div class="eakb-hero-content">
                <h1 class="eakb-hero-title">
                    <?php _e('Energy Alabama Knowledge Base', 'energy-alabama-kb'); ?>
                </h1>
                <p class="eakb-hero-description">
                    <?php _e('Find comprehensive information about clean energy, educational resources, and regulatory documents.', 'energy-alabama-kb'); ?>
                </p>
                
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

    <!-- Categories Section -->
    <section class="eakb-categories">
        <div class="eakb-container">
            <h2 class="eakb-section-title">
                <?php _e('Browse by Category', 'energy-alabama-kb'); ?>
            </h2>
            
            <?php if (!empty($categories)) : ?>
                <div class="eakb-category-grid">
                    <?php foreach ($categories as $category) : 
                        $category_link = get_term_link($category);
                        $article_count = $category->count;
                    ?>
                        <div class="eakb-category-card">
                            <a href="<?php echo esc_url($category_link); ?>" class="eakb-category-link">
                                <div class="eakb-category-icon">
                                    <?php echo $helpers->render_category_icon($category->slug); ?>
                                </div>
                                <h3 class="eakb-category-title"><?php echo esc_html($category->name); ?></h3>
                                <p class="eakb-category-description"><?php echo esc_html($category->description); ?></p>
                                <span class="eakb-category-count">
                                    <?php 
                                    printf(
                                        _n('%s article', '%s articles', $article_count, 'energy-alabama-kb'),
                                        number_format_i18n($article_count)
                                    ); 
                                    ?>
                                </span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="eakb-no-categories">
                    <?php _e('No categories found. Please add some knowledge base categories.', 'energy-alabama-kb'); ?>
                </p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Recent Articles Section -->
    <?php if (!empty($recent_articles)) : ?>
        <section class="eakb-recent-articles">
            <div class="eakb-container">
                <h2 class="eakb-section-title">
                    <?php _e('Recent Articles', 'energy-alabama-kb'); ?>
                </h2>
                
                <div class="eakb-articles-grid">
                    <?php foreach ($recent_articles as $article) : 
                        $article_link = get_permalink($article->ID);
                        $excerpt = wp_trim_words($article->post_content, 20);
                        $categories = get_the_terms($article->ID, 'kb_category');
                        $difficulty = get_post_meta($article->ID, '_eakb_difficulty_level', true);
                    ?>
                        <article class="eakb-article-card">
                            <a href="<?php echo esc_url($article_link); ?>" class="eakb-article-link">
                                
                                <?php if (has_post_thumbnail($article->ID)) : ?>
                                    <div class="eakb-article-image">
                                        <?php echo get_the_post_thumbnail($article->ID, 'medium', array('alt' => get_the_title($article->ID))); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="eakb-article-content">
                                    <h3 class="eakb-article-title"><?php echo esc_html($article->post_title); ?></h3>
                                    
                                    <?php if ($excerpt) : ?>
                                        <p class="eakb-article-excerpt"><?php echo esc_html($excerpt); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="eakb-article-meta">
                                        <?php if ($categories && !is_wp_error($categories)) : ?>
                                            <span class="eakb-article-category">
                                                <?php echo esc_html($categories[0]->name); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($difficulty) : ?>
                                            <span class="eakb-article-difficulty eakb-difficulty-<?php echo esc_attr($difficulty); ?>">
                                                <?php echo esc_html(ucfirst($difficulty)); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
                
                <div class="eakb-view-all">
                    <a href="<?php echo esc_url(get_post_type_archive_link('kb_article')); ?>" class="eakb-button eakb-button-secondary">
                        <?php _e('View All Articles', 'energy-alabama-kb'); ?>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Quick Links Section -->
    <section class="eakb-quick-links">
        <div class="eakb-container">
            <h2 class="eakb-section-title">
                <?php _e('Quick Links', 'energy-alabama-kb'); ?>
            </h2>
            
            <div class="eakb-quick-links-grid">
                <a href="<?php echo esc_url(get_term_link(get_term_by('slug', 'legal-regulatory', 'kb_category'))); ?>" class="eakb-quick-link">
                    <div class="eakb-quick-link-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14,2 14,8 20,8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10,9 9,9 8,9"></polyline>
                        </svg>
                    </div>
                    <span><?php _e('Legal & Regulatory Documents', 'energy-alabama-kb'); ?></span>
                </a>
                
                <a href="<?php echo esc_url(get_post_type_archive_link('docket')); ?>" class="eakb-quick-link">
                    <div class="eakb-quick-link-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <span><?php _e('Browse Dockets', 'energy-alabama-kb'); ?></span>
                </a>
                
                <a href="<?php echo esc_url(get_term_link(get_term_by('slug', 'educator-resources', 'kb_category'))); ?>" class="eakb-quick-link">
                    <div class="eakb-quick-link-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                    </div>
                    <span><?php _e('Educator Resources', 'energy-alabama-kb'); ?></span>
                </a>
                
                <a href="<?php echo esc_url(get_term_link(get_term_by('slug', 'faqs', 'kb_category'))); ?>" class="eakb-quick-link">
                    <div class="eakb-quick-link-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <span><?php _e('Frequently Asked Questions', 'energy-alabama-kb'); ?></span>
                </a>
            </div>
        </div>
    </section>

</div>

<?php get_footer(); ?>