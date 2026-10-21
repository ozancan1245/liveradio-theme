<?php
/**
 * Main Index / Front Page Template for LiveRadioStream.net
 * Optimized Speed & Full Accessibility Build
 */

get_header();

$paged = max(1, get_query_var('paged'), get_query_var('page'));

$radio_args = array(
    'post_type'      => 'radio',
    'posts_per_page' => 24,
    'paged'          => $paged
);

if (isset($_GET['radio_genre']) && !empty($_GET['radio_genre'])) {
    $radio_args['tax_query'] = array(
        array(
            'taxonomy' => 'radio_genre',
            'field'    => 'name',
            'terms'    => sanitize_text_field(urldecode($_GET['radio_genre'])),
        ),
    );
}

$radio_query = new WP_Query($radio_args);
$total_stations = isset($radio_query->found_posts) ? $radio_query->found_posts : 0;
?>

<style>
.title-dot { display: inline-block !important; width: 10px !important; height: 10px !important; border-radius: 50% !important; margin-right: 8px !important; vertical-align: middle !important; }
.title-dot.orange { background: #f97316 !important; box-shadow: 0 0 10px #f97316 !important; }

.main-layout-grid { display: flex !important; gap: 1.5rem !important; align-items: flex-start !important; }
.main-content-area { flex: 1 !important; min-width: 0 !important; }
.main-content-box { background: #0f172a !important; border: 1px solid #1e293b !important; border-radius: 12px !important; padding: 1.25rem !important; box-sizing: border-box !important; }

.main-box-header { 
    margin-bottom: 1.25rem !important; padding-bottom: 0.75rem !important; 
    border-bottom: 1px solid #1e293b !important; display: flex !important; 
    justify-content: space-between !important; align-items: center !important; 
    flex-wrap: wrap !important; gap: 12px !important; 
}
.main-box-title { color: #ffffff !important; font-size: 1.3rem !important; font-weight: 700 !important; display: flex !important; align-items: center !important; gap: 0.5rem !important; margin: 0 !important; }
.station-count-badge { color: #f97316 !important; font-size: 0.9rem !important; font-weight: 600 !important; }

.header-filter-group { display: flex !important; gap: 10px !important; align-items: center !important; flex-wrap: wrap !important; }
.custom-filter-select {
    background-color: #111827 !important; color: #ffffff !important; border: 1px solid #1e293b !important;
    border-radius: 8px !important; padding: 8px 14px !important; font-size: 0.85rem !important; font-weight: 600 !important;
    outline: none !important; cursor: pointer !important; max-width: 220px !important;
}
.custom-filter-select option { background-color: #111827 !important; color: #ffffff !important; padding: 8px !important; }

.main-content-area .radio-grid { display: grid !important; grid-template-columns: repeat(4, minmax(0, 1fr)) !important; gap: 1rem !important; }

.radio-card { 
    background: #111827 !important; border: 1px solid #1e293b !important; border-radius: 12px !important; 
    padding: 0.65rem !important; box-sizing: border-box !important; overflow: hidden !important; 
    display: flex !important; flex-direction: column !important; align-items: center !important; transition: all 0.25s ease !important; 
}
.radio-card:hover { border-color: #f97316 !important; transform: translateY(-3px); box-shadow: 0 8px 25px rgba(249, 115, 22, 0.15); }

.radio-thumb-wrap { position: relative !important; width: 100% !important; aspect-ratio: 1 / 1 !important; overflow: hidden !important; border-radius: 50% !important; background: #182235 !important; display: flex !important; align-items: center !important; justify-content: center !important; }
.radio-thumb-wrap img { width: 100% !important; height: 100% !important; object-fit: contain !important; padding: 4px !important; display: block !important; border-radius: 50% !important; }

.radio-thumb-overlay { position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; background: rgba(15, 23, 42, 0.8) !important; display: flex !important; align-items: center !important; justify-content: center !important; gap: 10px !important; opacity: 0 !important; transition: opacity 0.25s ease !important; border-radius: 50% !important; z-index: 2 !important; }
.radio-thumb-wrap:hover .radio-thumb-overlay { opacity: 1 !important; }

.btn-mini-play { width: 38px !important; height: 38px !important; border-radius: 50% !important; background: #ea580c !important; border: none !important; color: #ffffff !important; font-size: 15px !important; cursor: pointer !important; display: flex !important; align-items: center !important; justify-content: center !important; text-decoration: none !important; }
.btn-mini-info { width: 38px !important; height: 38px !important; border-radius: 50% !important; background: #0284c7 !important; border: none !important; color: #ffffff !important; font-size: 15px !important; font-weight: bold !important; cursor: pointer !important; display: flex !important; align-items: center !important; justify-content: center !important; text-decoration: none !important; }

.radio-title { font-size: 0.9rem !important; font-weight: 700 !important; margin-top: 0.5rem !important; margin-bottom: 0.15rem !important; text-align: center !important; width: 100% !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; }
.radio-title a { color: #ffffff !important; text-decoration: none !important; }
.radio-genre-sub { font-size: 0.75rem !important; color: #cbd5e1 !important; text-align: center !important; display: block !important; }

.sidebar-area { width: 320px !important; flex-shrink: 0 !important; }
.sidebar-box { background: #0f172a !important; border-radius: 12px !important; padding: 1.25rem !important; margin-bottom: 1.25rem !important; }
.sidebar-box.green-accent-box { border: 1px solid #10b981 !important; }
.sidebar-box.blue-accent-box { border: 1px solid #0ea5e9 !important; }
.sidebar-box.countries-box { border: 1px solid #f97316 !important; }
.sidebar-title { font-size: 1.1rem !important; font-weight: 700 !important; margin-top: 0 !important; margin-bottom: 1rem !important; padding-bottom: 0.6rem !important; display: flex !important; align-items: center !important; gap: 8px !important; }
.sidebar-box.green-accent-box .sidebar-title { color: #10b981 !important; border-bottom: 1px solid rgba(16, 185, 129, 0.2) !important; }
.sidebar-box.blue-accent-box .sidebar-title { color: #0ea5e9 !important; border-bottom: 1px solid rgba(14, 165, 233, 0.2) !important; }
.sidebar-box.countries-box .sidebar-title { color: #f97316 !important; border-bottom: 1px solid rgba(249, 115, 22, 0.2) !important; }

.sidebar-country-list { list-style: none !important; padding: 0 !important; margin: 0 !important; display: flex !important; flex-direction: column !important; gap: 0.5rem !important; }
.sidebar-country-list li a { display: flex !important; justify-content: space-between !important; align-items: center !important; background: #111827 !important; border: 1px solid #1e293b !important; border-radius: 8px !important; padding: 0.55rem 0.75rem !important; color: #ffffff !important; text-decoration: none !important; font-size: 0.88rem !important; }
.sidebar-country-list li a:hover { border-color: #f97316 !important; background: #1e293b !important; }

/* Yüksek Kontrastlý Bayrak ve Sayý Rozetleri (Google Kontrast Uyumlu) */
.flag-badge { background: #c2410c !important; color: #ffffff !important; padding: 2px 6px !important; border-radius: 4px !important; font-weight: 700 !important; font-size: 0.75rem !important; margin-right: 8px !important; display: inline-block !important; }
.country-count-badge { background: #1e293b !important; color: #f97316 !important; border: 1px solid #f97316 !important; padding: 2px 8px !important; border-radius: 6px !important; font-weight: 700 !important; font-size: 0.8rem !important; }

@media (max-width: 1024px) {
    .main-layout-grid { flex-direction: column !important; }
    .sidebar-area { width: 100% !important; }
    .main-content-area .radio-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
}
@media (max-width: 640px) {
    .main-content-area .radio-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
}
</style>

<div class="container" style="margin-top: 1.5rem; margin-bottom: 3rem;">
    <div class="main-layout-grid">
        
        <main class="main-content-area">
            <div class="main-content-box">
                
                <div class="main-box-header">
                    <h2 class="main-box-title">
                        <span><span class="title-dot orange"></span> Online Radio Channels</span>
                        <span class="station-count-badge">(<?php echo esc_html($total_stations); ?> Stations)</span>
                    </h2>

                    <div class="header-filter-group">
                        <select class="custom-filter-select" aria-label="Filter radios by country" onchange="if (this.value) window.location.href=this.value;">
                            <option value="<?php echo esc_url(home_url('/')); ?>">All Countries</option>
                            <?php
                            $country_terms = get_terms(array(
                                'taxonomy'   => 'radio_country',
                                'hide_empty' => false,
                                'orderby'    => 'name',
                                'order'      => 'ASC'
                            ));
                            if (!empty($country_terms) && !is_wp_error($country_terms)) {
                                foreach ($country_terms as $c) {
                                    $c_link = get_term_link($c);
                                    if (!is_wp_error($c_link)) {
                                        $selected = (is_tax('radio_country', $c->term_id)) ? 'selected' : '';
                                        echo '<option value="' . esc_url($c_link) . '" ' . $selected . '>' . esc_html($c->name) . ' (' . esc_html($c->count) . ')</option>';
                                    }
                                }
                            }
                            ?>
                        </select>

                        <select class="custom-filter-select" aria-label="Filter radios by genre" onchange="if (this.value) window.location.href=this.value;">
                            <option value="<?php echo esc_url(home_url('/')); ?>">All Genres</option>
                            <?php
                            global $wpdb;
                            $genre_results = get_transient('liveradio_genres_sql_cache');
                            if (false === $genre_results) {
                                $genres_sql = "
                                    SELECT t.term_id, t.name, tt.count 
                                    FROM {$wpdb->terms} t
                                    INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
                                    WHERE tt.taxonomy = 'radio_genre' AND tt.count > 0
                                    ORDER BY t.name ASC
                                ";
                                $genre_results = $wpdb->get_results($genres_sql);
                                set_transient('liveradio_genres_sql_cache', $genre_results, 12 * HOUR_IN_SECONDS);
                            }
                            if (!empty($genre_results)) {
                                foreach ($genre_results as $g) {
                                    $g_link = home_url('/?radio_genre=' . urlencode($g->name));
                                    $selected = (isset($_GET['radio_genre']) && urldecode($_GET['radio_genre']) === $g->name) ? 'selected' : '';
                                    echo '<option value="' . esc_url($g_link) . '" ' . $selected . '>' . esc_html($g->name) . ' (' . intval($g->count) . ')</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="radio-grid">
                    <?php 
                    $card_index = 0;
                    if ($radio_query->have_posts()) : while ($radio_query->have_posts()) : $radio_query->the_post();
                        $card_index++;
                        $post_id     = get_the_ID();
                        $radio_title = get_the_title();
                        $stream_url  = get_post_meta($post_id, '_radio_stream_url', true) ?: get_post_meta($post_id, 'stream_url', true);
                        
                        $thumb_url = get_post_meta($post_id, '_radio_logo', true);
                        if (empty($thumb_url)) { $thumb_url = get_post_meta($post_id, 'logo_url', true); }
                        if (empty($thumb_url)) { $thumb_url = get_the_post_thumbnail_url($post_id, 'full'); }

                        $thumb_url = trim($thumb_url);
                        if (empty($thumb_url) || strpos($thumb_url, 'placeholder') !== false || strpos($thumb_url, 'default') !== false || strpos($thumb_url, 'http') !== 0) {
                            $thumb_url = '';
                        }

                        $colors       = ['#ea580c', '#2563eb', '#059669', '#7c3aed', '#db2777', '#0891b2', '#ca8a04', '#4f46e5'];
                        $color_index  = abs(crc32($radio_title)) % count($colors);
                        $bg_color     = $colors[$color_index];
                        $first_letter = mb_strtoupper(mb_substr(trim($radio_title), 0, 1, 'UTF-8'), 'UTF-8');

                        $post_link  = get_permalink();
                        $genre_name = 'General';
                        $genres = get_the_terms($post_id, 'radio_genre') ?: get_the_terms($post_id, 'genre');
                        if (!empty($genres) && !is_wp_error($genres) && isset($genres[0]->name)) {
                            $genre_name = $genres[0]->name;
                        }

                        $loading_attr  = ($card_index <= 8) ? 'eager' : 'lazy';
                        $priority_attr = ($card_index <= 8) ? 'fetchpriority="high"' : '';
                    ?>
                        <div class="radio-card">
                            <div class="radio-thumb-wrap">
                                <?php if (empty($thumb_url)) : ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="100%" height="100%" style="border-radius: 50%; display: block;">
                                        <rect width="256" height="256" fill="<?php echo esc_attr($bg_color); ?>"/>
                                        <text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" fill="#ffffff" font-family="sans-serif" font-size="120" font-weight="bold"><?php echo esc_html($first_letter); ?></text>
                                    </svg>
                                <?php else : ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="100%" height="100%" style="border-radius: 50%; display: block; position: absolute; top: 0; left: 0; z-index: 1;">
                                        <rect width="256" height="256" fill="<?php echo esc_attr($bg_color); ?>"/>
                                        <text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" fill="#ffffff" font-family="sans-serif" font-size="120" font-weight="bold"><?php echo esc_html($first_letter); ?></text>
                                    </svg>
                                    <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title_attribute(); ?>" loading="<?php echo $loading_attr; ?>" <?php echo $priority_attr; ?> data-skip-lazy="1" decoding="async" onload="this.style.opacity='1';" style="opacity:0; transition: opacity 0.25s ease; position: relative; z-index: 2;" onerror="this.style.display='none';">                                <?php endif; ?>

                                <div class="radio-thumb-overlay">
                                    <button class="btn-mini-play js-play-trigger" 
                                            data-stream="<?php echo esc_url($stream_url); ?>" 
                                            data-title="<?php echo esc_attr($radio_title); ?>" 
                                            data-img="<?php echo esc_url($thumb_url); ?>" 
                                            data-link="<?php echo esc_url($post_link); ?>" 
                                            data-id="<?php echo esc_attr($post_id); ?>" 
                                            aria-label="Play <?php echo esc_attr($radio_title); ?>"
                                            title="Play">&#9654;</button>
                                    <a href="<?php echo esc_url($post_link); ?>" class="btn-mini-info" aria-label="Info about <?php echo esc_attr($radio_title); ?>" title="Info">&#8505;</a>
                                </div>
                            </div>
                            <h3 class="radio-title"><a href="<?php echo esc_url($post_link); ?>"><?php echo esc_html($radio_title); ?></a></h3>
                            <span class="radio-genre-sub"><?php echo esc_html($genre_name); ?></span>
                        </div>
                    <?php endwhile; wp_reset_postdata(); else : ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem; color: #cbd5e1;">
                            <p style="font-size: 1.1rem;">No radio stations found.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (isset($radio_query->max_num_pages) && $radio_query->max_num_pages > 1) : ?>
                    <div class="pagination-wrap" style="margin-top: 1.5rem; text-align: center;">
                        <?php
                        echo paginate_links(array(
                            'total'     => $radio_query->max_num_pages,
                            'current'   => $paged,
                            'prev_text' => '&laquo; Prev',
                            'next_text' => 'Next &raquo;',
                        ));
                        ?>
                    </div>
                <?php endif; ?>

            </div>
        </main>

        <aside class="sidebar-area">
            <div class="sidebar-box green-accent-box" id="favorites">
                <h3 class="sidebar-title"><span>Favorite Stations (<span id="fav-count">0</span>/50)</span></h3>
                <ul id="favorites-list"><li style="color: #cbd5e1; font-size: 0.88rem;">No favorites added yet.</li></ul>
            </div>

            <div class="sidebar-box blue-accent-box" id="history">
                <h3 class="sidebar-title"><span>Recently Listened</span></h3>
                <ul id="history-list"><li style="color: #cbd5e1; font-size: 0.88rem;">Your recently played stations will appear here.</li></ul>
            </div>

            <div class="sidebar-box countries-box">
                <h3 class="sidebar-title"><span class="title-dot orange"></span> Top Countries</h3>
                <ul class="sidebar-country-list">
                    <?php
                    $top_countries = get_transient('liveradio_top_countries_cache');
                    if (false === $top_countries) {
                        $top_countries = get_terms(array(
                            'taxonomy'   => 'radio_country',
                            'number'     => 10,
                            'orderby'    => 'count',
                            'order'      => 'DESC',
                            'hide_empty' => false
                        ));
                        set_transient('liveradio_top_countries_cache', $top_countries, 12 * HOUR_IN_SECONDS);
                    }
                    if (!empty($top_countries) && !is_wp_error($top_countries)) {
                        foreach ($top_countries as $c_term) {
                            $term_link = get_term_link($c_term);
                            if (is_wp_error($term_link)) continue;
                            $c_name = trim($c_term->name);
                            $flag = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $c_term->slug), 0, 2));
                            if (strlen($flag) < 2) { $flag = 'US'; }
                            ?>
                            <li>
                                <a href="<?php echo esc_url($term_link); ?>">
                                    <span><strong class="flag-badge"><?php echo esc_html($flag); ?></strong> <?php echo esc_html($c_name); ?></span>
                                    <span class="country-count-badge"><?php echo esc_html($c_term->count); ?></span>
                                </a>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </aside>

    </div>
</div>

<?php get_footer(); ?>