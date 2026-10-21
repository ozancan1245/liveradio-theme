<?php
/**
 * Radio Card Template Part - Forced Clean Square Letter Avatar
 */

$post_id     = get_the_ID();
$radio_title = get_the_title();
$stream_url  = get_post_meta($post_id, '_radio_stream_url', true) ?: get_post_meta($post_id, 'stream_url', true);
$post_link   = get_permalink();

// Yatay ve bozuk veritabanı logolarını tamamen bypass ediyoruz. 
// Doğrudan başlığın ilk harfini alarak kusursuz, kare renkli harfli logo üretiyoruz.
$colors       = ['f97316', '3b82f6', '10b981', '8b5cf6', 'ec4899', '06b6d4', 'eab308', '6366f1'];
$color_index  = abs(crc32($radio_title)) % count($colors);
$bg_color     = $colors[$color_index];
$first_letter = mb_substr(trim($radio_title), 0, 1);

$thumb_url    = "https://ui-avatars.com/api/?name=" . urlencode($first_letter) . "&background=" . $bg_color . "&color=fff&size=300&bold=true&rounded=false";

$genre_name = 'General';
$genres = get_the_terms($post_id, 'radio_genre') ?: get_the_terms($post_id, 'genre');
if (!empty($genres) && !is_wp_error($genres) && isset($genres[0]->name)) {
    $genre_name = $genres[0]->name;
}
?>

<div class="radio-card">
    <div class="radio-thumb-wrap">
        <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
        
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
        <a href="<?php echo esc_url($post_link); ?>"><?php the_title(); ?></a>
    </h3>
    <span class="radio-genre-sub"><?php echo esc_html($genre_name); ?></span>
</div>