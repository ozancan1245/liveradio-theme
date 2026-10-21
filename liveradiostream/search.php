<?php
get_header();
?>

<main class="main-content search-results-page" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    <header class="page-header" style="margin-bottom: 30px;">
        <h1 class="page-title" style="font-size: 24px; font-weight: bold;">
            <?php printf(esc_html__('Arama Sonuçlarý: "%s"', 'liveradiostream'), '<span>' . get_search_query() . '</span>'); ?>
        </h1>
    </header>

    <?php if (have_posts()) : ?>
        <div class="radio-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
            <?php
            while (have_posts()) :
                the_post();
                $thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: 'https://via.placeholder.com/200/151e32/ffffff?text=Radio';
                $stream_url = get_post_meta(get_the_ID(), '_radio_stream_url', true) ?: get_post_meta(get_the_ID(), 'stream_url', true);
            ?>
                <article id="post-<?php the_ID(); ?>" class="radio-card" style="background: #151e32; border-radius: 12px; padding: 15px; text-align: center; color: #fff;">
                    <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                        <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title_attribute(); ?>" style="width: 100%; height: 160px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;">
                        <h2 style="font-size: 16px; margin: 0 0 10px 0; font-weight: 600;"><?php the_title(); ?></h2>
                    </a>
                    <?php if ($stream_url) : ?>
                        <button class="play-btn" data-stream="<?php echo esc_url($stream_url); ?>" style="background: #10b981; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%;">
                            Dinle / Oynat
                        </button>
                    <?php endif; ?>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="pagination" style="margin-top: 40px; text-align: center;">
            <?php
            the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => __('&laquo; Önceki', 'liveradiostream'),
                'next_text' => __('Sonraki &raquo;', 'liveradiostream'),
            ));
            ?>
        </div>

    <?php else : ?>
        <div class="no-search-results" style="background: #1f2937; padding: 40px; border-radius: 12px; text-align: center; color: #9ca3af;">
            <p style="font-size: 18px; margin: 0;"><?php esc_html_e('No radio station found matching your query.', 'liveradiostream'); ?></p>
        </div>
    <?php endif; ?>
</main>

<?php
get_footer();