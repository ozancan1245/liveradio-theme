<?php
if (!defined('ABSPATH')) exit;

/**
 * LiveRadioStream.net Theme Setup
 */
if (!function_exists('liveradiostream_setup')) {
    function liveradiostream_setup() {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
        
        register_nav_menus(array(
            'primary' => __('Main Navigation', 'liveradiostream'),
            'footer'  => __('Footer Navigation', 'liveradiostream')
        ));
        
        add_image_size('radio-thumb', 300, 300, true);
    }
    add_action('after_setup_theme', 'liveradiostream_setup');
}

/**
 * Enqueue Styles and Scripts
 */
if (!function_exists('liveradiostream_scripts')) {
    function liveradiostream_scripts() {
        $css_file = get_stylesheet_directory() . '/style.css';
        $js_file  = get_template_directory() . '/assets/js/player.js';

        $css_version = file_exists($css_file) ? filemtime($css_file) : '1.0.0';
        $js_version  = file_exists($js_file) ? filemtime($js_file) : '1.0.0';

        wp_enqueue_style('liveradiostream-style', get_stylesheet_uri(), array(), $css_version);
        if (file_exists($js_file)) {
            wp_enqueue_script('liveradiostream-player', get_template_directory_uri() . '/assets/js/player.js', array(), $js_version, true);
            wp_localize_script('liveradiostream-player', 'RadioApp', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'userId'  => get_current_user_id()
            ));
        }
    }
    add_action('wp_enqueue_scripts', 'liveradiostream_scripts');
}

/**
 * Register Radio CPT & Taxonomies
 */
if (!function_exists('liveradiostream_register_cpt')) {
    function liveradiostream_register_cpt() {
// Radyo içerik tipine yorum desteğini zorla tanımla
add_action('init', 'add_comments_to_radio_cpt', 99);
function add_comments_to_radio_cpt() {
    add_post_type_support('radio', 'comments');
}
        register_post_type('radio', array(
            'labels' => array(
                'name'               => 'Radio Stations',
                'singular_name'      => 'Radio Station',
                'add_new'            => 'Add New Station',
                'add_new_item'       => 'Add New Radio Station',
                'edit_item'          => 'Edit Radio Station',
                'new_item'           => 'New Radio Station',
                'view_item'          => 'View Radio Station',
                'search_items'       => 'Search Radio Stations',
                'not_found'          => 'No Radio Stations Found',
                'not_found_in_trash' => 'No Stations Found in Trash'
            ),
            'public'             => true,
            'has_archive'        => true,
            'publicly_queryable' => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'radio', 'with_front' => false),
            'menu_icon'          => 'dashicons-format-audio',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
            'show_in_rest'       => true
        ));

        $post_types = array('radio', 'post');

        register_taxonomy('radio_country', $post_types, array(
            'hierarchical'      => true,
            'labels'            => array('name' => 'Countries', 'singular_name' => 'Country'),
            'rewrite'           => array('slug' => 'country', 'with_front' => false),
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'public'            => true
        ));

        register_taxonomy('radio_genre', $post_types, array(
            'hierarchical'      => true,
            'labels'            => array('name' => 'Genres', 'singular_name' => 'Genre'),
            'rewrite'           => array('slug' => 'genre', 'with_front' => false),
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'public'            => true
        ));

        register_taxonomy('radio_language', $post_types, array(
            'hierarchical'      => true,
            'labels'            => array('name' => 'Languages', 'singular_name' => 'Language'),
            'rewrite'           => array('slug' => 'language', 'with_front' => false),
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'public'            => true
        ));
    }
    add_action('init', 'liveradiostream_register_cpt', 0);
}

/**
 * ISO Country Flag / Code Helper
 */
if (!function_exists('liveradiostream_get_country_flag')) {
    function liveradiostream_get_country_flag($term_slug = '') {
        $flags = array(
            'united-states' => 'US',
            'the-united-states-of-america' => 'US',
            'germany'       => 'DE',
            'russia'        => 'RU',
            'france'        => 'FR',
            'turkey'        => 'TR',
            'united-kingdom'=> 'GB',
            'the-united-kingdom-of-great-britain-and-northern-ireland' => 'GB',
            'greece'        => 'GR',
            'spain'         => 'ES',
            'italy'         => 'IT',
            'canada'        => 'CA',
            'brazil'        => 'BR',
            'netherlands'   => 'NL',
            'poland'        => 'PL',
            'ukraine'       => 'UA',
            'china'         => 'CN',
            'japan'         => 'JP',
            'nigeria'       => 'NG'
        );
        return (is_string($term_slug) && isset($flags[$term_slug])) ? $flags[$term_slug] : 'US';
    }
}

/**
 * Global Ana Sorgu ve Sayfalama Düzelticisi
 */
add_action('pre_get_posts', function($query) {
    if (!is_admin() && $query->is_main_query()) {
        if ($query->is_home() || $query->is_archive() || is_tax('radio_country') || is_tax('radio_genre') || is_tax('radio_language') || is_tax('genre') || is_tax('country') || is_tax('language')) {
            $query->set('post_type', array('radio', 'post'));
            $query->set('posts_per_page', 24);
            
            if (get_query_var('paged')) {
                $query->set('paged', get_query_var('paged'));
            } elseif (get_query_var('page')) {
                $query->set('paged', get_query_var('page'));
            }
        }

        if (isset($_GET['radio_genre']) && !empty($_GET['radio_genre'])) {
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'radio_genre',
                    'field'    => 'name',
                    'terms'    => sanitize_text_field(urldecode($_GET['radio_genre'])),
                ),
            ));
        }
    }
});

/**
 * Clean Emoji Detection Scripts
 */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

/* ============================================================
   RADYO OYLAMA SİSTEMİ (AJAX HANDLER)
   ============================================================ */
add_action('wp_ajax_radio_rate_station', 'radio_handle_rating');
add_action('wp_ajax_nopriv_radio_rate_station', 'radio_handle_rating');

function radio_handle_rating() {
    if (!isset($_POST['post_id']) || !isset($_POST['rating'])) {
        wp_send_json_error(array('message' => 'Invalid data.'));
    }

    $post_id = intval($_POST['post_id']);
    $new_rating = floatval($_POST['rating']);

    if ($new_rating < 1 || $new_rating > 5) {
        wp_send_json_error(array('message' => 'Invalid rating value.'));
    }

    $current_avg = floatval(get_post_meta($post_id, '_radio_rating_avg', true)) ?: 5.0;
    $votes_count = intval(get_post_meta($post_id, '_radio_votes_count', true)) ?: 1;

    $total_score = ($current_avg * $votes_count) + $new_rating;
    $votes_count++;
    $new_avg = round($total_score / $votes_count, 1);

    update_post_meta($post_id, '_radio_rating_avg', $new_avg);
    update_post_meta($post_id, '_radio_votes_count', $votes_count);

    wp_send_json_success(array(
        'rating_avg' => $new_avg,
        'votes_count' => $votes_count
    ));
}

/* ============================================================
   MOST PLAYED - DİNLENME SAYACI
   ============================================================ */
add_action('wp_ajax_radio_increment_play', 'radio_handle_increment_play');
add_action('wp_ajax_nopriv_radio_increment_play', 'radio_handle_increment_play');

function radio_handle_increment_play() {
    if (!isset($_POST['post_id'])) {
        wp_send_json_error();
    }
    $post_id = intval($_POST['post_id']);
    $current_count = intval(get_post_meta($post_id, '_radio_play_count', true));
    update_post_meta($post_id, '_radio_play_count', $current_count + 1);
    wp_send_json_success(array('count' => $current_count + 1));
}

/* ============================================================
   RANDOM PLAY - RASTGELE RADYO SEÇİCİ
   ============================================================ */
add_action('wp_ajax_radio_random_play', 'radio_handle_random_play');
add_action('wp_ajax_nopriv_radio_random_play', 'radio_handle_random_play');

function radio_handle_random_play() {
    $random_query = new WP_Query(array(
        'post_type'      => 'radio',
        'posts_per_page' => 1,
        'orderby'        => 'rand',
        'no_found_rows'  => true,
    ));

    if ($random_query->have_posts()) {
        while ($random_query->have_posts()) {
            $random_query->the_post();
            $post_id = get_the_ID();
            $stream_url = get_post_meta($post_id, '_radio_stream_url', true);
            $backup_url = get_post_meta($post_id, '_radio_backup_stream_url', true);
            $title = get_the_title();
            $permalink = get_permalink();
            $thumb_url = get_the_post_thumbnail_url($post_id, 'radio-thumb') ?: 'https://via.placeholder.com/300x300/151e32/ffffff?text=' . urlencode($title);

            wp_send_json_success(array(
                'id'            => $post_id,
                'title'         => $title,
                'stream'        => $stream_url,
                'backup_stream' => $backup_url,
                'img'           => $thumb_url,
                'link'          => $permalink
            ));
        }
        wp_reset_postdata();
    }

    wp_send_json_error(array('message' => 'No stations found.'));
}

/* ============================================================
   CANLI ARAMA HANDLER
   ============================================================ */
add_action('wp_ajax_liveradiostream_search', 'liveradiostream_ajax_search_radios');
add_action('wp_ajax_nopriv_liveradiostream_search', 'liveradiostream_ajax_search_radios');

function liveradiostream_ajax_search_radios() {
    global $wpdb;

    $search_term = '';
    if (isset($_POST['query'])) $search_term = $_POST['query'];
    elseif (isset($_GET['query'])) $search_term = $_GET['query'];
    elseif (isset($_POST['keyword'])) $search_term = $_POST['keyword'];
    elseif (isset($_GET['keyword'])) $search_term = $_GET['keyword'];
    elseif (isset($_GET['s'])) $search_term = $_GET['s'];

    $search_term = trim(sanitize_text_field($search_term));

    if (empty($search_term)) {
        wp_send_json_success(array());
        wp_die();
    }

    $like = '%' . $wpdb->esc_like($search_term) . '%';

    $sql1 = $wpdb->prepare("
        SELECT DISTINCT ID, post_title 
        FROM {$wpdb->posts} 
        WHERE post_status = 'publish' 
          AND post_type IN ('radio', 'post')
          And (post_title LIKE %s OR post_content LIKE %s)
        LIMIT 12
    ", $like, $like);

    $results1 = $wpdb->get_results($sql1);

    $sql2 = $wpdb->prepare("
        SELECT DISTINCT p.ID, p.post_title 
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
        WHERE p.post_status = 'publish' 
          AND p.post_type IN ('radio', 'post')
          AND t.name LIKE %s 
        LIMIT 12
    ", $like);

    $results2 = $wpdb->get_results($sql2);

    $merged = array();
    if (!empty($results1)) {
        foreach ($results1 as $row) {
            $merged[$row->ID] = $row;
        }
    }
    if (!empty($results2)) {
        foreach ($results2 as $row) {
            $merged[$row->ID] = $row;
        }
    }

    if (empty($merged)) {
        wp_send_json_success(array());
        wp_die();
    }

    $data = array();
    foreach ($merged as $post_obj) {
        $post_id   = $post_obj->ID;
        $title     = get_the_title($post_id);
        
        $title     = preg_replace('/^[^\p{L}\p{N}]+/u', '', $title);
        $title     = trim($title);
        $link      = get_permalink($post_id);

        $thumb_url = get_the_post_thumbnail_url($post_id, 'radio-thumb') 
            ?: (get_the_post_thumbnail_url($post_id, 'thumbnail') 
            ?: 'https://via.placeholder.com/100/151e32/ffffff?text=Radio');
            
        $stream = get_post_meta($post_id, '_radio_stream_url', true) 
            ?: get_post_meta($post_id, 'stream_url', true);
        $backup_stream = get_post_meta($post_id, '_radio_backup_stream_url', true);

        $genre_name = 'General';
        $genres = get_the_terms($post_id, 'radio_genre') ?: get_the_terms($post_id, 'genre');
        if (!empty($genres) && !is_wp_error($genres) && isset($genres[0]->name)) {
            $genre_name = $genres[0]->name;
        }

        $data[] = array(
            'id'            => $post_id,
            'title'         => $title,
            'link'          => $link,
            'url'           => $link,
            'img'           => $thumb_url,
            'thumb'         => $thumb_url,
            'stream'        => $stream,
            'backup_stream' => $backup_stream,
            'genre'         => $genre_name
        );
    }

    wp_send_json_success($data);
    wp_die();
}

/* ============================================================
   GÜVENLİ CANLI ARAMA HANDLER
   ============================================================ */
add_action('wp_ajax_radio_live_search', 'liveradiostream_safe_live_search_handler');
add_action('wp_ajax_nopriv_radio_live_search', 'liveradiostream_safe_live_search_handler');

function liveradiostream_safe_live_search_handler() {
    global $wpdb;

    $search_term = '';
    if (isset($_GET['term'])) {
        $search_term = trim(sanitize_text_field($_GET['term']));
    }

    if (empty($search_term) || strlen($search_term) < 1) {
        wp_send_json_success(array());
        wp_die();
    }

    $like = '%' . $wpdb->esc_like($search_term) . '%';

    $sql = $wpdb->prepare("
        SELECT DISTINCT ID, post_title 
        FROM {$wpdb->posts} 
        WHERE post_status = 'publish' 
          AND post_type = 'radio'
          AND post_title LIKE %s
        LIMIT 10
    ", $like);

    $results = $wpdb->get_results($sql);

    if (empty($results)) {
        $sql_post = $wpdb->prepare("
            SELECT DISTINCT ID, post_title 
            FROM {$wpdb->posts} 
            WHERE post_status = 'publish' 
              AND post_type = 'post'
              AND post_title LIKE %s
            LIMIT 10
        ", $like);
        $results = $wpdb->get_results($sql_post);
    }

    if (empty($results)) {
        wp_send_json_success(array());
        wp_die();
    }

    $data = array();
    foreach ($results as $post_obj) {
        $post_id   = $post_obj->ID;
        $title     = get_the_title($post_id);
        $link      = get_permalink($post_id);
        
        $thumb_url = get_the_post_thumbnail_url($post_id, 'thumbnail');
        if (!$thumb_url) {
            $thumb_url = get_the_post_thumbnail_url($post_id, 'medium');
        }
        if (!$thumb_url) {
            $thumb_url = 'https://via.placeholder.com/80/151e32/ffffff?text=Radio';
        }

        $stream = get_post_meta($post_id, '_radio_stream_url', true);
        if (!$stream) {
            $stream = get_post_meta($post_id, 'stream_url', true);
        }
        $backup_stream = get_post_meta($post_id, '_radio_backup_stream_url', true);

        $genre_name = 'General';
        $genres = get_the_terms($post_id, 'radio_genre');
        if (empty($genres) || is_wp_error($genres)) {
            $genres = get_the_terms($post_id, 'genre');
        }
        if (!empty($genres) && !is_wp_error($genres) && isset($genres[0]->name)) {
            $genre_name = $genres[0]->name;
        }

        $data[] = array(
            'id'            => $post_id,
            'title'         => $title,
            'link'          => $link,
            'url'           => $link,
            'img'           => $thumb_url,
            'thumb'         => $thumb_url,
            'stream'        => $stream,
            'backup_stream' => $backup_stream,
            'genre'         => $genre_name
        );
    }

    wp_send_json_success($data);
    wp_die();
}

/* ============================================================
   KIRIK YAYIN BİLDİRİM SİSTEMİ (REPORT BROKEN AJAX & ADMIN PAGE)
   ============================================================ */
add_action('wp_ajax_report_broken_radio', 'handle_broken_radio_report');
add_action('wp_ajax_nopriv_report_broken_radio', 'handle_broken_radio_report');

function handle_broken_radio_report() {
    if (!isset($_POST['radio_id'])) {
        wp_send_json_error(array('message' => 'ID yok'));
    }

    $radio_id = intval($_POST['radio_id']);
    
    // Doğrudan WordPress post meta güncelleme fonksiyonu
    $current_count = (int) get_post_meta($radio_id, '_radio_broken_reports', true);
    $new_count = $current_count + 1;
    
    update_post_meta($radio_id, '_radio_broken_reports', $new_count);
    update_post_meta($radio_id, '_radio_last_broken_date', current_time('mysql'));

    // Kesin sonuç döndür
    wp_send_json_success(array(
        'success' => true,
        'count' => $new_count,
        'id' => $radio_id
    ));
}
// Yönetim Paneline Kırık Yayınlar Alt Menüsü Ekleme
add_action('admin_menu', 'register_broken_radios_admin_page');

function register_broken_radios_admin_page() {
    add_submenu_page(
        'edit.php?post_type=radio',
        'Kırık Yayın Raporları',
        'Kırık Yayınlar',
        'manage_options',
        'broken-radio-reports',
        'render_broken_radios_page'
    );
}

// Yönetim Paneli Sayfa Görünümü
function render_broken_radios_page() {
    $args = array(
        'post_type'      => 'radio',
        'posts_per_page' => -1,
        'meta_query'     => array(
            array(
                'key'     => '_radio_broken_reports',
                'compare' => 'EXISTS',
            ),
        ),
        'orderby'        => 'meta_value_num',
        'meta_key'       => '_radio_broken_reports',
        'order'          => 'DESC'
    );
    $broken_query = new WP_Query($args);
    
    echo '<div class="wrap">';
    echo '<h1>Kırık Yayın Bildirilen Radyolar</h1>';
    echo '<p>Kullanıcılar tarafından çalışmadığı bildirilen radyo istasyonları aşağıda listelenmiştir.</p>';
    
    if ($broken_query->have_posts()) {
        echo '<table class="wp-list-table widefat fixed striped" style="margin-top:15px;">';
        echo '<thead><tr>';
        echo '<th style="width:30%;">Radyo Adı</th>';
        echo '<th style="width:15%;">Bildirim Sayısı</th>';
        echo '<th style="width:25%;">Son Bildirim Tarihi</th>';
        echo '<th style="width:30%;">İşlemler</th>';
        echo '</tr></thead><tbody>';
        
        while ($broken_query->have_posts()) {
            $broken_query->the_post();
            $r_id = get_the_ID();
            $count = get_post_meta($r_id, '_radio_broken_reports', true);
            $last_date = get_post_meta($r_id, '_radio_last_broken_date', true);
            $edit_url = get_edit_post_link($r_id);
            $view_url = get_permalink($r_id);
            
            echo '<tr>';
            echo '<td><strong><a href="' . esc_url($edit_url) . '">' . get_the_title() . '</a></strong></td>';
            echo '<td><span style="background:#ef4444; color:#fff; padding:3px 8px; border-radius:4px; font-weight:bold;">' . intval($count) . ' kez</span></td>';
            echo '<td>' . esc_html($last_date) . '</td>';
            echo '<td>';
            echo '<a href="' . esc_url($edit_url) . '" class="button button-primary" style="margin-right:5px;">Düzenle / Linki Değiştir</a>';
            echo '<a href="' . esc_url($view_url) . '" target="_blank" class="button">Görüntüle</a>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
    } else {
        echo '<div style="background:#fff; padding:20px; border:1px solid #ccd0d4; margin-top:15px;"><p>Harika! Henüz bildirilen kırık bir radyo yayını yok veya bildirimler henüz veritabanına işlenmedi.</p></div>';
    }
    
    echo '</div>';
    wp_reset_postdata();
}
/**
 * Kurumsal Sayfa Route Yöneticisi
 */
add_action('init', function() {
    add_rewrite_rule('^(about|about-us|terms|privacy|faq|advertise|dmca|contact)/?$', 'index.php?pagename=$matches[1]', 'top');
});

add_action('template_redirect', function() {
    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $allowed = array('contact', 'about', 'about-us', 'terms', 'privacy', 'faq', 'advertise', 'dmca');

    if (in_array($uri, $allowed, true)) {
        global $wp_query;
        $wp_query->is_404 = false;
        status_header(200);

        $template = ($uri === 'contact') ? get_stylesheet_directory() . '/page-contact.php' : get_stylesheet_directory() . '/page.php';
        
        if (file_exists($template)) {
            include($template);
            exit;
        }
    }
});

/* ============================================================
   RADYO İSTASYONU TAM KAPSAMLI YÖNETİM PANELİ (AÇIK VE ORTADA)
   ============================================================ */

// Klasik Editör modunu aktif ediyoruz
add_filter('use_block_editor_for_post_type', 'liveradio_disable_block_editor_for_radio', 10, 2);
function liveradio_disable_block_editor_for_radio($use_block_editor, $post_type) {
    if ($post_type === 'radio') {
        return false;
    }
    return $use_block_editor;
}

// Meta kutusunu orta alanda tutma filtresi
add_filter('get_user_option_meta-box-order_radio', function($order) {
    return array(
        'normal'   => 'liveradio_main_panel_v2',
        'side'     => '',
        'advanced' => ''
    );
});

if (!function_exists('liveradio_add_station_meta_box')) {
    function liveradio_add_station_meta_box() {
        add_meta_box(
            'liveradio_main_panel_v2',
            '📻 Radyo İstasyonu Yönetim ve Canlı Yayın Paneli',
            'liveradio_station_meta_box_callback',
            'radio',
            'normal',
            'high'
        );
    }
    add_action('add_meta_boxes', 'liveradio_add_station_meta_box');
}

if (!function_exists('liveradio_station_meta_box_callback')) {
    function liveradio_station_meta_box_callback($post) {
        wp_nonce_field('liveradio_save_station_meta', 'liveradio_station_nonce');

        $stream_url = get_post_meta($post->ID, '_radio_stream_url', true);
        if (empty($stream_url)) {
            $stream_url = get_post_meta($post->ID, 'stream_url', true);
        }

        $backup_stream_url = get_post_meta($post->ID, '_radio_backup_stream_url', true);

        $logo_url = get_post_meta($post->ID, 'logo_url', true);
        if (empty($logo_url)) {
            $logo_url = get_post_meta($post->ID, '_radio_logo', true);
        }

        $homepage = get_post_meta($post->ID, '_radio_homepage', true);
        $bitrate  = get_post_meta($post->ID, '_radio_bitrate', true) ?: '128';
        $codec    = get_post_meta($post->ID, '_radio_codec', true);

        // AÇIKLAMA METNİNİ ÇEKME
        $current_info = '';
        if (!empty($post->post_excerpt)) {
            $current_info = $post->post_excerpt;
        } elseif (!empty($post->post_content)) {
            $current_info = $post->post_content;
        } else {
            $current_info = get_post_meta($post->ID, '_radio_current_playing', true);
            if (empty($current_info)) {
                $current_info = get_post_meta($post->ID, 'description', true);
            }
        }

        if (empty(trim($current_info))) {
            $bitrate_str = !empty($bitrate) ? " (" . $bitrate . " Kbps)" : "";
            $current_info = "Listen to " . get_the_title($post->ID) . " live radio stream online in high quality audio" . $bitrate_str . ". Enjoy non-stop music, live broadcasts, news updates, and sports directly in your web browser on LiveRadioStream.net with zero buffering and no registration required.";
        }

        $current_country_terms = wp_get_post_terms($post->ID, 'radio_country', array('fields' => 'names'));
        $selected_country = !empty($current_country_terms) ? $current_country_terms[0] : '';

        $countries = get_terms(array('taxonomy' => 'radio_country', 'hide_empty' => false));
        $bitrate_options = array('32', '64', '96', '128', '160', '192', '256', '320');
        $codec_options = array('MP3', 'AAC', 'AAC+', 'OGG', 'OPUS', 'WMA');
        ?>
        <div style="background: #ffffff; border: 2px solid #2271b1; border-radius: 6px; padding: 20px; margin: 10px 0; box-shadow: 0 1px 5px rgba(0,0,0,0.1);">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                
                <div>
                    <label style="font-weight: 700; display: block; margin-bottom: 6px; color: #1d2327; font-size: 14px;">🎵 Ana Yayın Akışı URL (Stream URL):</label>
                    <input type="text" name="_radio_stream_url" value="<?php echo esc_attr($stream_url); ?>" style="width: 100%; padding: 10px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px;" placeholder="https://radyo-yayin-adresi.com/stream">
                </div>

                <div>
                    <label style="font-weight: 700; display: block; margin-bottom: 6px; color: #d63638; font-size: 14px;">🔄 Yedek Yayın Akışı URL (Backup Stream URL):</label>
                    <input type="text" name="_radio_backup_stream_url" value="<?php echo esc_attr($backup_stream_url); ?>" style="width: 100%; padding: 10px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px;" placeholder="https://yedek-yayin-adresi.com/stream">
                    <p style="font-size: 11px; color: #646970; margin-top: 4px;">* Ana yayın koptuğunda otomatik yedek akış devreye girer.</p>
                </div>

                <div style="grid-column: span 2;">
                    <label style="font-weight: 700; display: block; margin-bottom: 6px; color: #1d2327; font-size: 14px;">💬 Sitedeki Canlı Açıklama / Tanıtım Metni (HTML Destekli):</label>
                    <textarea name="radio_custom_description" rows="5" style="width: 100%; padding: 10px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px; background: #fff;" placeholder="Sitede görünen açıklama metni..."><?php echo esc_textarea($current_info); ?></textarea>
                    <p style="font-size: 12px; color: #2271b1; margin-top: 5px; font-weight: 600;">* Bu alana yazdığınız HTML linkler ön yüzde doğrudan tıklanabilir olarak görünür.</p>
                </div>

                <div>
                    <label style="font-weight: 700; display: block; margin-bottom: 6px; color: #1d2327; font-size: 14px;">🖼️ Radyo Logosu (Logo URL):</label>
                    <input type="text" name="logo_url" value="<?php echo esc_attr($logo_url); ?>" style="width: 100%; padding: 9px; border: 1px solid #8c8f94; border-radius: 4px;">
                </div>

                <div>
                    <label style="font-weight: 700; display: block; margin-bottom: 6px; color: #1d2327; font-size: 14px;">🌐 Resmi Web Sitesi:</label>
                    <input type="text" name="_radio_homepage" value="<?php echo esc_attr($homepage); ?>" style="width: 100%; padding: 9px; border: 1px solid #8c8f94; border-radius: 4px;">
                </div>

                <div>
                    <label style="font-weight: 700; display: block; margin-bottom: 6px; color: #1d2327; font-size: 14px;">🌍 Ülke:</label>
                    <select name="_radio_country_select" style="width: 100%; padding: 9px; border: 1px solid #8c8f94; border-radius: 4px;">
                        <option value="">-- Ülke Seçin --</option>
                        <?php if (!empty($countries) && !is_wp_error($countries)) : ?>
                            <?php foreach ($countries as $c) : ?>
                                <option value="<?php echo esc_attr($c->name); ?>" <?php selected($selected_country, $c->name); ?>>
                                    <?php echo esc_html($c->name); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label style="font-weight: 700; display: block; margin-bottom: 6px; color: #1d2327; font-size: 14px;">🎧 Ses Özellikleri (Bitrate / Codec):</label>
                    <div style="display: flex; gap: 10px;">
                        <select name="_radio_bitrate" style="width: 50%; padding: 9px; border: 1px solid #8c8f94; border-radius: 4px;">
                            <option value="">Bitrate</option>
                            <?php foreach ($bitrate_options as $b) : ?>
                                <option value="<?php echo $b; ?>" <?php selected($bitrate, $b); ?>><?php echo $b; ?> kbps</option>
                            <?php endforeach; ?>
                        </select>
                        <select name="_radio_codec" style="width: 50%; padding: 9px; border: 1px solid #8c8f94; border-radius: 4px;">
                            <option value="">Codec</option>
                            <?php foreach ($codec_options as $co) : ?>
                                <option value="<?php echo $co; ?>" <?php selected($codec, $co); ?>><?php echo $co; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }
}

if (!function_exists('liveradio_save_station_meta')) {
    function liveradio_save_station_meta($post_id) {
        // 1. Nonce güvenlik kontrolü
        if (!isset($_POST['liveradio_station_nonce']) || !wp_verify_nonce($_POST['liveradio_station_nonce'], 'liveradio_save_station_meta')) {
            return;
        }
        
        // 2. Autosave (Otomatik kayıt) ve Revizyon koruması (Veri silinmesini önler)
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }

        // 3. Kullanıcı yetki kontrolü
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // 4. Ana Yayın URL'si (.m3u8 ve özel karakterler için esc_url_raw kullanıyoruz)
        if (isset($_POST['_radio_stream_url'])) {
            $stream_url = trim($_POST['_radio_stream_url']);
            update_post_meta($post_id, '_radio_stream_url', esc_url_raw($stream_url));
        }

        // 5. Diğer meta alanları
        $fields = array('_radio_backup_stream_url', 'logo_url', '_radio_homepage', '_radio_bitrate', '_radio_codec');
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }

        // 6. Açıklama metni ve içerik eşitleme
        if (isset($_POST['radio_custom_description'])) {
            $desc_text = wp_kses_post($_POST['radio_custom_description']);
            
            update_post_meta($post_id, '_radio_current_playing', $desc_text);
            update_post_meta($post_id, 'description', $desc_text);

            global $wpdb;
            $wpdb->update(
                $wpdb->posts,
                array(
                    'post_excerpt' => $desc_text,
                    'post_content' => $desc_text
                ),
                array('ID' => $post_id)
            );
        }

        // 7. Ülke taksonomisi kaydı
        if (isset($_POST['_radio_country_select'])) {
            $country_name = sanitize_text_field($_POST['_radio_country_select']);
            if (!empty($country_name)) {
                wp_set_post_terms($post_id, array($country_name), 'radio_country', false);
            } else {
                wp_set_post_terms($post_id, array(), 'radio_country', false);
            }
        }
    }
    add_action('save_post_radio', 'liveradio_save_station_meta');
}
// FontAwesome kuyruğunu tamamen devre dışı bırakır
if (!function_exists('liveradio_remove_fa')) {
    function liveradio_remove_fa() {
        wp_dequeue_style('font-awesome');
        wp_deregister_style('font-awesome');
        wp_dequeue_style('fontawesome');
        wp_dequeue_style('wpsr_fa_icons');
        wp_deregister_style('wpsr_fa_icons');
    }
    add_action('wp_enqueue_scripts', 'liveradio_remove_fa', 999);
}