<?php
/**
 * Main Index / Front Page Template for LiveRadioStream.net
 * Fully Optimized & High-Performance Version
 */

get_header();

// Özel Radyo Sorgusu
$paged = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : 1);
$radio_args = array(
    'post_type'                => 'radio',
    'posts_per_page'           => 24,
    'paged'                    => $paged,
    'update_post_meta_cache' => true,
    'update_post_term_cache' => true,
);
$radio_query = new WP_Query($radio_args);
$total_stations = isset($radio_query->found_posts) ? $radio_query->found_posts : 0;
?>

<style>
/* CSS Indicator Dots */
.title-dot {
    display: inline-block !important;
    width: 10px !important;
    height: 10px !important;
    border-radius: 50% !important;
    margin-right: 8px !important;
    vertical-align: middle !important;
}
.title-dot.green { background: #10b981 !important; box-shadow: 0 0 10px #10b981 !important; }
.title-dot.blue { background: #0ea5e9 !important; box-shadow: 0 0 10px #0ea5e9 !important; }
.title-dot.orange { background: #f97316 !important; box-shadow: 0 0 10px #f97316 !important; }

.main-layout-grid { display: flex !important; gap: 1.5rem !important; align-items: flex-start !important; }
.main-content-area { flex: 1 !important; min-width: 0 !important; }
.main-content-box { background: #0f172a !important; border: 1px solid #1e293b !important; border-radius: 12px !important; padding: 1.25rem !important; box-sizing: border-box !important; }

.main-box-header { 
    margin-bottom: 1.25rem !important; 
    padding-bottom: 0.75rem !important; 
    border-bottom: 1px solid #1e293b !important; 
    display: flex !important; 
    justify-content: space-between !important; 
    align-items: center !important; 
    flex-wrap: wrap !important; 
    gap: 12px !important; 
}

.main-box-title { color: #ffffff !important; font-size: 1.3rem !important; font-weight: 700 !important; display: flex !important; align-items: center !important; gap: 0.5rem !important; margin: 0 !important; }
.station-count-badge { color: #f97316 !important; font-size: 0.9rem !important; font-weight: 600 !important; }

.header-filter-group {
    display: flex !important;
    gap: 10px !important;
    align-items: center !important;
    flex-wrap: wrap !important;
}

.custom-filter-select {
    background-color: #111827 !important;
    color: #ffffff !important;
    border: 1px solid #1e293b !important;
    border-radius: 8px !important;
    padding: 8px 14px !important;
    font-size: 0.85rem !important;
    font-weight: 600 !important;
    outline: none !important;
    cursor: pointer !important;
    max-width: 220px !important;
}

.custom-filter-select:hover, 
.custom-filter-select:focus {
    border-color: #f97316 !important;
}

.custom-filter-select option {
    background-color: #111827 !important;
    color: #ffffff !important;
    padding: 8px !important;
}

.main-content-area .radio-grid { display: grid !important; grid-template-columns: repeat(4, minmax(0, 1fr)) !important; gap: 1rem !important; }

/* Kompakt ve þýk radyo kartý */
.radio-card { 
    background: #111827 !important; 
    border: 1px solid #1e293b !important; 
    border-radius: 12px !important; 
    padding: 0.65rem !important; 
    box-sizing: border-box !important; 
    overflow: hidden !important; 
    display: flex !important; 
    flex-direction: column !important; 
    align-items: center !important; 
    transition: all 0.25s ease !important; 
}
.radio-card:hover { border-color: #f97316 !important; transform: translateY(-3px); box-shadow: 0 8px 25px rgba(249, 115, 22, 0.15); }

/* Logo Alaný: Tam kare (1:1) */
.radio-thumb-wrap { 
    position: relative !important; 
    width: 100% !important; 
    aspect-ratio: 1 / 1 !important; 
    overflow: hidden !important; 
    border-radius: 50% !important; 
    background: #182235 !important; 
}

/* Dahili Kusursuz Daire Harfli Logo Kutusu */
.radio-letter-avatar {
    width: 100% !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 3.5rem !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    text-transform: uppercase !important;
    font-family: 'Montserrat', sans-serif !important;
}

.radio-thumb-overlay { position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; background: rgba(15, 23, 42, 0.8) !important; display: flex !important; align-items: center !important; justify-content: center !important; gap: 10px !important; opacity: 0 !important; transition: opacity 0.25s ease !important; border-radius: 50% !important; z-index: 2 !important; }
.radio-thumb-wrap:hover .radio-thumb-overlay { opacity: 1 !important; }

.btn-mini-play { width: 38px !important; height: 38px !important; border-radius: 50% !important; background: #f97316 !important; border: none !important; color: #ffffff !important; font-size: 15px !important; cursor: pointer !important; display: flex !important; align-items: center !important; justify-content: center !important; text-decoration: none !important; }
.btn-mini-info { width: 38px !important; height: 38px !important; border-radius: 50% !important; background: #0ea5e9 !important; border: none !important; color: #ffffff !important; font-size: 15px !important; font-weight: bold !important; cursor: pointer !important; display: flex !important; align-items: center !important; justify-content: center !important; text-decoration: none !important; }
.btn-mini-play:hover, .btn-mini-info:hover { transform: scale(1.1) !important; }

.radio-title { font-size: 0.9rem !important; font-weight: 700 !important; margin-top: 0.5rem !important; margin-bottom: 0.15rem !important; text-align: center !important; width: 100% !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; }
.radio-title a { color: #ffffff !important; text-decoration: none !important; }
.radio-genre-sub { font-size: 0.75rem !important; color: #94a3b8 !important; text-align: center !important; display: block !important; }

.sidebar-area { width: 320px !important; flex-shrink: 0 !important; }
.sidebar-box.green-accent-box { background: #0f172a !important; border: 1px solid #10b981 !important; border-radius: 12px !important; padding: 1.25rem !important; }
.sidebar-box.green-accent-box .sidebar-title { color: #10b981 !important; font-size: 1.1rem !important; font-weight: 700 !important; margin-top: 0 !important; margin-bottom: 0.8rem !important; padding-bottom: 0.6rem !important; border-bottom: 1px solid rgba(16, 185, 129, 0.2) !important; display: flex !important; align-items: center !important; gap: 8px !important; }
.sidebar-box.blue-accent-box { background: #0f172a !important; border: 1px solid #0ea5e9 !important; border-radius: 12px !important; padding: 1.25rem !important; }
.sidebar-box.blue-accent-box .sidebar-title { color: #0ea5e9 !important; font-size: 1.1rem !important; font-weight: 700 !important; margin-top: 0 !important; margin-bottom: 0.8rem !important; padding-bottom: 0.6rem !important; border-bottom: 1px solid rgba(14, 165, 233, 0.2) !important; display: flex !important; align-items: center !important; gap: 8px !important; }
.sidebar-box.countries-box { background: #0f172a !important; border: 1px solid #f97316 !important; border-radius: 12px !important; padding: 1.25rem !important; box-shadow: 0 4px 20px rgba(249, 115, 22, 0.08) !important; }
.sidebar-box.countries-box .sidebar-title { color: #f97316 !important; font-size: 1.1rem !important; font-weight: 700 !important; margin-top: 0 !important; margin-bottom: 1rem !important; padding-bottom: 0.6rem !important; border-bottom: 1px solid rgba(249, 115, 22, 0.2) !important; display: flex !important; align-items: center !important; }

ul#favorites-list, 
ul#history-list,
.sidebar-box.green-accent-box ul,
.sidebar-box.blue-accent-box ul {
    max-height: 280px !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    padding: 0 !important;
    margin: 0 !important;
    padding-right: 4px !important;
}

.sidebar-box.green-accent-box ul li, 
.sidebar-box.blue-accent-box ul li,
.sidebar-fav-list li, 
.sidebar-hist-list li {
    list-style: none !important;
    margin-bottom: 0.5rem !important;
}

.sidebar-box.green-accent-box ul li::before, 
.sidebar-box.blue-accent-box ul li::before,
.sidebar-fav-list li::before, 
.sidebar-hist-list li::before { 
    content: none !important; 
}

ul#favorites-list::-webkit-scrollbar,
.sidebar-box.green-accent-box ul::-webkit-scrollbar {
    width: 6px !important;
}
ul#favorites-list::-webkit-scrollbar-track,
.sidebar-box.green-accent-box ul::-webkit-scrollbar-track {
    background: rgba(15, 23, 42, 0.5) !important;
    border-radius: 4px !important;
}
ul#favorites-list::-webkit-scrollbar-thumb,
.sidebar-box.green-accent-box ul::-webkit-scrollbar-thumb {
    background: #10b981 !important;
    border-radius: 4px !important;
}

ul#history-list::-webkit-scrollbar,
.sidebar-box.blue-accent-box ul::-webkit-scrollbar {
    width: 6px !important;
}
ul#history-list::-webkit-scrollbar-track,
.sidebar-box.blue-accent-box ul::-webkit-scrollbar-track {
    background: rgba(15, 23, 42, 0.5) !important;
    border-radius: 4px !important;
}
ul#history-list::-webkit-scrollbar-thumb,
.sidebar-box.blue-accent-box ul::-webkit-scrollbar-thumb {
    background: #0ea5e9 !important;
    border-radius: 4px !important;
}

.sidebar-country-list { list-style: none !important; padding: 0 !important; margin: 0 !important; display: flex !important; flex-direction: column !important; gap: 0.5rem !important; }
.sidebar-country-list li a { display: flex !important; justify-content: space-between !important; align-items: center !important; background: #111827 !important; border: 1px solid #1e293b !important; border-radius: 8px !important; padding: 0.55rem 0.75rem !important; color: #f8fafc !important; text-decoration: none !important; font-size: 0.88rem !important; font-weight: 500 !important; }
.sidebar-country-list li a:hover { border-color: #f97316 !important; background: #1e293b !important; }
.sidebar-country-list .flag-badge { background: #f97316 !important; color: #ffffff !important; padding: 2px 6px !important; border-radius: 4px !important; font-weight: 700 !important; font-size: 0.75rem !important; margin-right: 8px !important; }
.sidebar-country-list .country-count-badge { background: rgba(249, 115, 22, 0.15) !important; color: #f97316 !important; border: 1px solid rgba(249, 115, 22, 0.3) !important; padding: 2px 8px !important; border-radius: 6px !important; font-weight: 700 !important; font-size: 0.8rem !important; }

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
        
        <!-- Sol Ana Ýçerik -->
        <main class="main-content-area">
            <div class="main-content-box">
                
                <!-- Baþlýk & Açýlýr Filtre Menüleri Barý -->
                <div class="main-box-header">
                    <h2 class="main-box-title">
                        <span><span class="title-dot orange"></span> Online Radio Channels</span>
                        <span class="station-count-badge">(<?php echo esc_html($total_stations); ?> Stations)</span>
                    </h2>

                    <!-- Filtre Menüleri -->
                    <div class="header-filter-group">
                        <!-- Ülke Filtresi -->
                        <select class="custom-filter-select" onchange="if (this.value) window.location.href=this.value;">
                            <option value="">All Countries</option>
                            <?php
                            $country_terms = get_terms(array(
                                'taxonomy'   => array('radio_country', 'country'),
                                'hide_empty' => false,
                                'orderby'    => 'name',
                                'order'      => 'ASC'
                            ));
                            if (!empty($country_terms) && !is_wp_error($country_terms)) {
                                foreach ($country_terms as $c) {
                                    $c_link = get_term_link($c);
                                    if (!is_wp_error($c_link)) {
                                        $c_name = trim(preg_replace('/^[\?\s\x00-\x1F\x7F-\x9F]+/u', '', $c->name));
                                        $c_name = trim(preg_replace('/^[^\p{L}\p{N}\s\-\.]+/u', '', $c_name));
                                        if (empty($c_name)) { $c_name = $c->slug; }
                                        echo '<option value="' . esc_url($c_link) . '">' . esc_html($c_name) . ' (' . esc_html($c->count) . ')</option>';
                                    }
                                }
                            }
                            ?>
                        </select>

                        <!-- Müzik Türü Filtresi -->
                        <select class="custom-filter-select" onchange="if (this.value) window.location.href=this.value;">
                            <option value="">All Genres</option>
                            <?php
                            $genre_terms = get_terms(array(
                                'taxonomy'   => array('radio_genre', 'genre'),
                                'hide_empty' => false,
                                'orderby'    => 'name',
                                'order'      => 'ASC'
                            ));
                            if (!empty($genre_terms) && !is_wp_error($genre_terms)) {
                                foreach ($genre_terms as $g) {
                                    $g_link = get_term_link($g);
                                    if (!is_wp_error($g_link)) {
                                        $g_name = trim(preg_replace('/^[\?\s\x00-\x1F\x7F-\x9F]+/u', '', $g->name));
                                        $g_name = trim(preg_replace('/^[^\p{L}\p{N}\s\-\.]+/u', '', $g_name));
                                        if (empty($g_name)) { $g_name = $g->slug; }
                                        echo '<option value="' . esc_url($g_link) . '">' . esc_html($g_name) . ' (' . esc_html($g->count) . ')</option>';
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- 4'lü Radyo Kart Izgarasý -->
                <div class="radio-grid">
                    <?php if ($radio_query->have_posts()) : while ($radio_query->have_posts()) : $radio_query->the_post();
                        global $post;
                        $post_id     = $post->ID;
                        $radio_title = get_the_title($post);
                        $post_link   = get_permalink($post);

                        // Stream URL performans optimizasyonu
                        $stream_url  = get_post_meta($post_id, '_radio_stream_url', true);
                        if (empty($stream_url)) {
                            $stream_url = get_post_meta($post_id, 'stream_url', true);
                        }

                        // Logo Tespiti (Öne Çýkan Görsel)
                        $has_logo  = has_post_thumbnail($post_id);
                        $thumb_url = $has_logo ? get_the_post_thumbnail_url($post_id, 'medium') : '';

                        // Renk ve harf üretimi (Fallback için)
                        $colors       = array('f97316', '3b82f6', '10b981', '8b5cf6', 'ec4899', '06b6d4', 'eab308', '6366f1');
                        $color_index  = abs(crc32($radio_title)) % count($colors);
                        $bg_color     = $colors[$color_index];
                        $first_letter = mb_substr(trim($radio_title), 0, 1);

                        // Tür (Genre) tespiti
                        $genre_name = 'General';
                        $genres = get_the_terms($post_id, 'radio_genre');
                        if (empty($genres) || is_wp_error($genres)) {
                            $genres = get_the_terms($post_id, 'genre');
                        }
                        if (!empty($genres) && !is_wp_error($genres) && isset($genres[0]->name)) {
                            $genre_name = $genres[0]->name;
                        }
                    ?>
                        <div class="radio-card">
                            <div class="radio-thumb-wrap force-circle" style="background-color: #<?php echo esc_attr($bg_color); ?> !important;">
                                <?php if ($has_logo) : ?>
                                    <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($radio_title); ?>" style="width: 100% !important; height: 100% !important; object-fit: cover !important; border-radius: 50% !important;">
                                <?php else : ?>
                                    <div class="radio-letter-avatar">
                                        <?php echo esc_html($first_letter); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="radio-thumb-overlay">
                                    <button class="btn-mini-play js-play-trigger" 
                                            data-stream="<?php echo esc_url($stream_url); ?>" 
                                            data-title="<?php echo esc_attr($radio_title); ?>" 
                                            data-img="<?php echo esc_url($thumb_url); ?>" 
                                            data-link="<?php echo esc_url($post_link); ?>" 
                                            data-id="<?php echo esc_attr($post_id); ?>" 
                                            title="Play Live">
                                        &#9654;
                                    </button>
                                    <a href="<?php echo esc_url($post_link); ?>" class="btn-mini-info" title="Station Info">
                                        &#8505;
                                    </a>
                                </div>
                            </div>

                            <h3 class="radio-title">
                                <a href="<?php echo esc_url($post_link); ?>"><?php echo esc_html($radio_title); ?></a>
                            </h3>
                            <span class="radio-genre-sub"><?php echo esc_html($genre_name); ?></span>
                        </div>
                    <?php endwhile; wp_reset_postdata(); else : ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem; color: #94a3b8;">
                            <p style="font-size: 1.1rem;">No radio stations found.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sayfalama -->
                <?php if (isset($radio_query->max_num_pages) && $radio_query->max_num_pages > 1) : ?>
                    <div class="pagination-wrap" style="margin-top: 1.5rem; text-align: center;">
                        <?php
                        echo paginate_links(array(
                            'total'   => $radio_query->max_num_pages,
                            'current' => $paged,
                            'prev_text' => '&laquo; Prev',
                            'next_text' => 'Next &raquo;',
                        ));
                        ?>
                    </div>
                <?php endif; ?>

            </div>
        </main>

        <!-- Sað Sütun: Sidebar Kutularý -->
        <aside class="sidebar-area">
            <!-- Favorite Stations -->
            <div class="sidebar-box green-accent-box" id="favorites" style="margin-bottom: 1.25rem;">
                <h3 class="sidebar-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#10b981" style="flex-shrink:0;"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    <span>Favorite Stations (<span id="fav-count">0</span>/50)</span>
                </h3>
                <ul id="favorites-list" class="sidebar-fav-list">
                    <li style="color: #94a3b8; font-size: 0.88rem; list-style: none; margin: 0; padding: 0;">No favorite stations added yet.</li>
                </ul>
            </div>

            <!-- Recently Listened -->
            <div class="sidebar-box blue-accent-box" id="history" style="margin-bottom: 1.25rem;">
                <h3 class="sidebar-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#0ea5e9" style="flex-shrink:0;"><path d="M13 3a9 9 0 0 0-9 9H1l3.89 3.89.07.14L9 12H6a7 7 0 1 1 7 7 7.07 7.07 0 0 1-6-3.42l-1.42 1.42A8.93 8.93 0 0 0 13 21a9 9 0 0 0 0-18zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
                    <span>Recently Listened</span>
                </h3>
                <ul id="history-list" class="sidebar-hist-list">
                    <li style="color: #94a3b8; font-size: 0.88rem; list-style: none; margin: 0; padding: 0;">Your recently played stations will appear here.</li>
                </ul>
            </div>

            <!-- Top Countries -->
            <div class="sidebar-box countries-box">
                <h3 class="sidebar-title"><span class="title-dot orange"></span> Top Countries</h3>
                <ul class="sidebar-country-list">
                    <?php
                    $top_countries = array();
                    $tax_countries_side = array('radio_country', 'country');
                    foreach ($tax_countries_side as $tax_name) {
                        if (taxonomy_exists($tax_name)) {
                            $terms = get_terms(array(
                                'taxonomy'   => $tax_name,
                                'number'     => 10,
                                'orderby'    => 'count',
                                'order'      => 'DESC',
                                'hide_empty' => false
                            ));
                            if (!empty($terms) && !is_wp_error($terms)) {
                                foreach ($terms as $t) {
                                    $top_countries[$t->term_id] = $t;
                                }
                            }
                        }
                    }

                    if (!empty($top_countries)) :
                        uasort($top_countries, function($a, $b) {
                            $cnt_a = (isset($a->count)) ? (int)$a->count : 0;
                            $cnt_b = (isset($b->count)) ? (int)$b->count : 0;
                            return $cnt_b - $cnt_a;
                        });
                        $top_countries = array_slice($top_countries, 0, 10);
                        
                        foreach ($top_countries as $c_term) :
                            $term_link = get_term_link($c_term);
                            if (is_wp_error($term_link)) continue;
                            
                            $c_name = trim(preg_replace('/^[\?\s\x00-\x1F\x7F-\x9F]+/u', '', $c_term->name));
                            $c_name = trim(preg_replace('/^[^\p{L}\p{N}\s\-\.]+/u', '', $c_name));
                            if (empty($c_name)) $c_name = $c_term->slug;
                            $flag = function_exists('liveradiostream_get_country_flag') ? liveradiostream_get_country_flag($c_term->slug) : 'US';
                    ?>
                        <li>
                            <a href="<?php echo esc_url($term_link); ?>">
                                <span><strong class="flag-badge"><?php echo esc_html($flag); ?></strong> <?php echo esc_html($c_name); ?></span>
                                <span class="country-count-badge"><?php echo esc_html($c_term->count); ?></span>
                            </a>
                        </li>
                    <?php 
                        endforeach;
                    else :
                    ?>
                        <li><a href="<?php echo esc_url(home_url('/country/united-states/')); ?>"><span><strong class="flag-badge">US</strong> The United States Of America</span> <span class="country-count-badge">40</span></a></li>
                        <li><a href="<?php echo esc_url(home_url('/country/germany/')); ?>"><span><strong class="flag-badge">DE</strong> Germany</span> <span class="country-count-badge">10</span></a></li>
                        <li><a href="<?php echo esc_url(home_url('/country/united-kingdom/')); ?>"><span><strong class="flag-badge">GB</strong> The United Kingdom</span> <span class="country-count-badge">10</span></a></li>
                        <li><a href="<?php echo esc_url(home_url('/country/nigeria/')); ?>"><span><strong class="flag-badge">NG</strong> Nigeria</span> <span class="country-count-badge">10</span></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </aside>

    </div>
</div>

<?php
get_footer();