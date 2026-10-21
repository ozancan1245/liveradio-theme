<?php
/*
Template Name: Most Played Radios
*/
get_header(); ?>

<main class="container" style="margin-top: 2.5rem; margin-bottom: 4rem;">
    <!-- Sayfa Başlık Alanı -->
    <div style="margin-bottom: 2rem; border-bottom: 1px solid var(--border-light, #1e293b); padding-bottom: 1rem;">
        <span class="badge" style="background: var(--primary, #f97316); color:#fff; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: inline-block; margin-bottom: 0.5rem;">Discover</span>
        <h1 style="color: #fff; font-size: 1.8rem; font-weight: 800; margin: 0 0 0.4rem 0;">Most Played Radio Stations</h1>
        <p style="color: var(--text-muted, #94a3b8); font-size: 0.95rem; margin: 0;">Discover the stations with the highest listener counts and total plays across the globe.</p>
    </div>

    <!-- Radyo İstasyonları Grid Listesi -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
        <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $most_played_query = new WP_Query(array(
            'post_type'      => 'radio',
            'posts_per_page' => 15,
            'paged'          => $paged,
            'meta_key'       => '_radio_play_count',
            'orderby'        => array(
                'meta_value_num' => 'DESC',
                'date'           => 'DESC'
            ),
            'no_found_rows'  => true,
        ));

        if ($most_played_query->have_posts()) :
            while ($most_played_query->have_posts()) : $most_played_query->the_post();
                $stream_url = get_post_meta(get_the_ID(), '_radio_stream_url', true);
                $location   = get_post_meta(get_the_ID(), '_radio_location', true);
                $bitrate    = get_post_meta(get_the_ID(), '_radio_bitrate', true) ?: '128 Kbps';
                $play_count = intval(get_post_meta(get_the_ID(), '_radio_play_count', true));
                
                $genres     = get_the_terms(get_the_ID(), 'radio_genre');
                $genre_name = ($genres && !is_wp_error($genres)) ? $genres[0]->name : 'General';
                
                $thumb_url  = get_the_post_thumbnail_url(get_the_ID(), 'radio-thumb') ?: 'https://via.placeholder.com/300x300/151e32/ffffff?text=' . urlencode(get_the_title());
                $permalink  = get_permalink();
                
                $countries  = get_the_terms(get_the_ID(), 'radio_country');
                $country_name = ($countries && !is_wp_error($countries)) ? $countries[0]->name : 'Worldwide';
                $country_slug = ($countries && !is_wp_error($countries)) ? $countries[0]->slug : '';
                $flag_icon    = function_exists('liveradiostream_get_country_flag') ? liveradiostream_get_country_flag($country_slug) : '';
        ?>
            <!-- Modern Kart Yapısı -->
            <div style="background: #111827; border: 1px solid #1e293b; border-radius: 14px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <div>
                    <!-- Üst Kısım: Logo ve Başlık -->
                    <div style="display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 1rem;">
                        <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" style="width: 75px; height: 75px; border-radius: 10px; object-fit: cover; flex-shrink: 0; background: #0b0f19; border: 1px solid #1e293b;">
                        <div style="min-width: 0; flex: 1;">
                            <span style="font-size: 0.7rem; color: #f97316; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 2px;"><?php echo esc_html($genre_name); ?></span>
                            <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 4px 0; color: #fff; line-height: 1.3;">
                                <a href="<?php echo esc_url($permalink); ?>" style="color: inherit; text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php the_title(); ?></a>
                            </h3>
                            <span style="font-size: 0.78rem; color: #94a3b8; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo $flag_icon . ' ' . esc_html($location ? $location : $country_name); ?></span>
                        </div>
                    </div>

                    <!-- Dinlenme ve Bitrate Bilgisi -->
                    <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center; background: #0b0f19; padding: 0.5rem 0.75rem; border-radius: 8px; border: 1px solid #1e293b;">
                        <span>🎧 <strong style="color: #fff;"><?php echo number_format_i18n($play_count); ?></strong> plays</span>
                        <span style="color: #38bdf8; font-weight: 600;"><?php echo esc_html($bitrate); ?></span>
                    </div>
                </div>

                <!-- Alt Butonlar -->
                <div style="display: flex; gap: 0.5rem;">
                    <button class="js-play-trigger" style="flex: 1; padding: 0.65rem; background: #f97316; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 6px; transition: opacity 0.2s;"
                            data-id="<?php echo get_the_ID(); ?>"
                            data-stream="<?php echo esc_url($stream_url); ?>" 
                            data-title="<?php echo esc_attr(get_the_title()); ?>" 
                            data-img="<?php echo esc_url($thumb_url); ?>"
                            data-link="<?php echo esc_url($permalink); ?>">
                        &#9658; Play Live
                    </button>
                    <a href="<?php echo esc_url($permalink); ?>" style="padding: 0.65rem 1rem; background: #1e293b; color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">Details</a>
                </div>
            </div>
        <?php
            endwhile;
            wp_reset_postdata();
        else :
            echo '<p style="color: var(--text-muted, #94a3b8); grid-column: 1 / -1; text-align: center; padding: 3rem;">No played radio stations found yet. Start listening to populate this list!</p>';
        endif;
        ?>
    </div>

    <!-- Sayfalama (Pagination) -->
    <div style="margin-top: 3rem; text-align: center;" class="mostplayed-pagination">
        <?php
        $big = 999999999;
        echo paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $most_played_query->max_num_pages,
            'prev_text' => '&laquo; Previous',
            'next_text' => 'Next &raquo;',
        ));
        ?>
    </div>
</main>

<style>
    .mostplayed-pagination .page-numbers {
        background: #111827;
        color: #fff;
        border: 1px solid #1e293b;
        padding: 0.5rem 1rem;
        margin: 0 4px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
    }
    .mostplayed-pagination .page-numbers.current {
        background: #f97316;
        border-color: #f97316;
    }
    .mostplayed-pagination .page-numbers:hover:not(.current) {
        background: #1e293b;
    }
</style>

<?php get_footer(); ?>