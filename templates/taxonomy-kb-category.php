<?php
/**
 * Template for KB Category Archive
 *
 * @package Energy_Alabama_KB
 * @since   1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get current category
$current_category = get_queried_object();
$category_name = $current_category->name;
$category_description = $current_category->description;
$category_slug = $current_category->slug;

// Get category icon mapping
function eakb_get_category_icon($slug) {
    $icons = array(
        'clean-energy-101' => '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>',
        'educator-resources' => '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
        'legal-regulatory' => '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10,9 9,9 8,9"/></svg>',
        'presentation-library' => '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
        'faqs' => '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'
    );
    
    return isset($icons[$slug]) ? $icons[$slug] : '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
}

// Get category color scheme
function eakb_get_category_color($slug) {
    $colors = array(
        'clean-energy-101' => '#3b82f6',
        'educator-resources' => '#10b981', 
        'legal-regulatory' => '#6366f1',
        'presentation-library' => '#8b5cf6',
        'faqs' => '#f59e0b'
    );
    
    return isset($colors[$slug]) ? $colors[$slug] : '#6b7280';
}

$category_color = eakb_get_category_color($category_slug);
?>

<div class="eakb-category-archive">
    
    <!-- Hero Section - EXACT COPY from main Knowledge Base page -->
    <section class="eakb-hero">
        <div class="eakb-container">
            <div class="eakb-hero-content">
                <h1 class="eakb-hero-title">
                    <?php echo esc_html($category_name); ?>
                </h1>
                <p class="eakb-hero-description">
                    <?php 
                    if ($category_description) {
                        echo esc_html($category_description);
                    } else {
                        printf(__('Browse all articles in the %s category', 'energy-alabama-kb'), esc_html($category_name));
                    }
                    ?>
                </p>
                
                <!-- Search Form -->
                <div class="eakb-search-container">
                    <form class="eakb-search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="eakb-search-wrapper">
                            <input type="search" 
                                    class="eakb-search-input" 
                                    placeholder="<?php printf(__('Search %s...', 'energy-alabama-kb'), esc_attr($category_name)); ?>"
                                    value="<?php echo get_search_query(); ?>" 
                                    name="s" 
                                    autocomplete="off"
                                    aria-label="<?php printf(__('Search %s', 'energy-alabama-kb'), esc_attr($category_name)); ?>">
                            <input type="hidden" name="post_type" value="kb_article">
                            <input type="hidden" name="kb_category" value="<?php echo esc_attr($category_slug); ?>">
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

    <!-- Category Content -->
    <section class="eakb-category-content">
        <div class="eakb-container">
            
            <!-- Category Stats -->
            <div class="eakb-category-stats" style="text-align: center; margin-bottom: 40px; color: #6b7280;">
                <?php
                $total_posts = $wp_query->found_posts;
                printf(
                    _n('%d article available', '%d articles available', $total_posts, 'energy-alabama-kb'),
                    $total_posts
                );
                ?>
            </div>
            
            <!-- Filters and Sort Controls -->
            <div class="eakb-category-filters">
                <div class="eakb-filter-controls">
                    <select class="eakb-difficulty-filter" onchange="eakb_filterByDifficulty(this.value)">
                        <option value=""><?php _e('All Difficulty Levels', 'energy-alabama-kb'); ?></option>
                        <option value="beginner"><?php _e('Beginner', 'energy-alabama-kb'); ?></option>
                        <option value="intermediate"><?php _e('Intermediate', 'energy-alabama-kb'); ?></option>
                        <option value="advanced"><?php _e('Advanced', 'energy-alabama-kb'); ?></option>
                    </select>
                    
                    <select class="eakb-sort-filter" onchange="eakb_sortArticles(this.value)">
                        <option value="date"><?php _e('Sort by Date', 'energy-alabama-kb'); ?></option>
                        <option value="title"><?php _e('Sort by Title', 'energy-alabama-kb'); ?></option>
                        <option value="difficulty"><?php _e('Sort by Difficulty', 'energy-alabama-kb'); ?></option>
                    </select>
                </div>
            </div>

            <!-- Articles Grid -->
            <div class="eakb-articles-grid" id="eakb-articles-container">
                <?php if (have_posts()) : ?>
                    
                    <?php while (have_posts()) : the_post(); ?>
                        <?php
                        // Get article meta
                        $difficulty = get_post_meta(get_the_ID(), '_eakb_difficulty_level', true);
                        $read_time = get_post_meta(get_the_ID(), '_eakb_read_time', true);
                        $featured_icon = get_post_meta(get_the_ID(), '_eakb_featured_icon', true);
                        ?>
                        
                        <article class="eakb-article-card" data-difficulty="<?php echo esc_attr($difficulty); ?>">
                            <div class="eakb-card-header">
                                <?php if (has_post_thumbnail()): ?>
                                    <div class="eakb-card-image">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium', array('loading' => 'lazy')); ?>
                                        </a>
                                    </div>
                                <?php elseif ($featured_icon): ?>
                                    <div class="eakb-card-icon">
                                        <span class="eakb-icon-<?php echo esc_attr($featured_icon); ?>"></span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="eakb-card-meta">
                                    <?php if ($difficulty): ?>
                                        <span class="eakb-difficulty-badge eakb-difficulty-<?php echo esc_attr($difficulty); ?>">
                                            <?php echo esc_html(ucfirst($difficulty)); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($read_time): ?>
                                        <span class="eakb-read-time">
                                            <?php printf(__('%d min read', 'energy-alabama-kb'), $read_time); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="eakb-card-content">
                                <h3 class="eakb-card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <div class="eakb-card-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                </div>
                                
                                <div class="eakb-card-footer">
                                    <time class="eakb-card-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                        <?php echo get_the_date('M j, Y'); ?>
                                    </time>
                                    
                                    <a href="<?php the_permalink(); ?>" class="eakb-read-more">
                                        <?php _e('Read More', 'energy-alabama-kb'); ?>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="7" y1="17" x2="17" y2="7"/>
                                            <polyline points="7,7 17,7 17,17"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                        
                    <?php endwhile; ?>
                    
                <?php else : ?>
                    
                    <div class="eakb-no-articles">
                        <div class="eakb-no-articles-icon">
                            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                        </div>
                        <h3><?php _e('No articles found', 'energy-alabama-kb'); ?></h3>
                        <p><?php printf(__('There are currently no articles in the %s category.', 'energy-alabama-kb'), esc_html($category_name)); ?></p>
                        <a href="<?php echo esc_url(home_url('/knowledge-base/')); ?>" class="eakb-back-link">
                            <?php _e('← Back to Knowledge Base', 'energy-alabama-kb'); ?>
                        </a>
                    </div>
                    
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($wp_query->max_num_pages > 1) : ?>
                <div class="eakb-pagination">
                    <?php
                    echo paginate_links(array(
                        'prev_text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15,18 9,12 15,6"/></svg> ' . __('Previous', 'energy-alabama-kb'),
                        'next_text' => __('Next', 'energy-alabama-kb') . ' <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9,18 15,12 9,6"/></svg>',
                        'mid_size' => 2,
                        'end_size' => 1
                    ));
                    ?>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- Related Categories -->
    <section class="eakb-related-categories">
        <div class="eakb-container">
            <h3><?php _e('Explore Other Categories', 'energy-alabama-kb'); ?></h3>
            
            <div class="eakb-categories-grid">
                <?php
                $all_categories = get_terms(array(
                    'taxonomy' => 'kb_category',
                    'hide_empty' => true,
                    'exclude' => array($current_category->term_id)
                ));
                
                if ($all_categories && !is_wp_error($all_categories)) :
                    foreach ($all_categories as $category) :
                        $cat_color = eakb_get_category_color($category->slug);
                        $cat_count = $category->count;
                ?>
                    <div class="eakb-category-card" style="--category-color: <?php echo esc_attr($cat_color); ?>;">
                        <a href="<?php echo esc_url(get_term_link($category)); ?>">
                            <div class="eakb-category-card-icon">
                                <?php echo eakb_get_category_icon($category->slug); ?>
                            </div>
                            <h4><?php echo esc_html($category->name); ?></h4>
                            <p><?php echo wp_trim_words($category->description, 15, '...'); ?></p>
                            <span class="eakb-category-count">
                                <?php printf(_n('%d article', '%d articles', $cat_count, 'energy-alabama-kb'), $cat_count); ?>
                            </span>
                        </a>
                    </div>
                <?php 
                    endforeach;
                endif; 
                ?>
            </div>
        </div>
    </section>

</div>

<script>
// Simple filtering functions for the category page
function eakb_filterByDifficulty(difficulty) {
    const articles = document.querySelectorAll('.eakb-article-card');
    
    articles.forEach(function(article) {
        if (difficulty === '' || article.getAttribute('data-difficulty') === difficulty) {
            article.style.display = 'block';
        } else {
            article.style.display = 'none';
        }
    });
}

function eakb_sortArticles(sortBy) {
    const container = document.getElementById('eakb-articles-container');
    const articles = Array.from(container.querySelectorAll('.eakb-article-card'));
    
    articles.sort(function(a, b) {
        if (sortBy === 'title') {
            const titleA = a.querySelector('.eakb-card-title a').textContent.toLowerCase();
            const titleB = b.querySelector('.eakb-card-title a').textContent.toLowerCase();
            return titleA.localeCompare(titleB);
        } else if (sortBy === 'difficulty') {
            const difficultyOrder = { 'beginner': 1, 'intermediate': 2, 'advanced': 3 };
            const diffA = difficultyOrder[a.getAttribute('data-difficulty')] || 0;
            const diffB = difficultyOrder[b.getAttribute('data-difficulty')] || 0;
            return diffA - diffB;
        }
        // Default: sort by date (newest first)
        return 0; // Keep original order for date sorting
    });
    
    // Re-append sorted articles
    articles.forEach(function(article) {
        container.appendChild(article);
    });
}
</script>

<?php get_footer(); ?>