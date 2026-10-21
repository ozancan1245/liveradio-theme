<?php get_header(); ?>

<main class="container" style="margin-top: 2rem;">
    <?php
    while (have_posts()) : the_post();
        $post_id    = get_the_ID();
        $stream_url = get_post_meta($post_id, '_radio_stream_url', true);
        if (empty($stream_url)) {
            $stream_url = get_post_meta($post_id, 'stream_url', true);
        }
        $backup_url = get_post_meta($post_id, '_radio_backup_stream_url', true);

        $location   = get_post_meta($post_id, '_radio_location', true);
        $bitrate    = get_post_meta($post_id, '_radio_bitrate', true) ?: '128 Kbps';
        $frequency  = get_post_meta($post_id, '_radio_frequency', true) ?: 'FM / Online Stream';
        $format     = get_post_meta($post_id, '_radio_format', true) ?: 'MP3 / AAC';
        $rating_avg = get_post_meta($post_id, '_radio_rating_avg', true) ?: '5.0';
        $votes_cnt  = get_post_meta($post_id, '_radio_votes_count', true) ?: '1';
        $radio_title = get_the_title();
        
        $genres     = get_the_terms($post_id, 'radio_genre');
        $genre_name = ($genres && !is_wp_error($genres)) ? $genres[0]->name : 'General';
        
        $countries  = get_the_terms($post_id, 'radio_country');
        $country_name = ($countries && !is_wp_error($countries)) ? $countries[0]->name : 'Worldwide';
        $country_slug = ($countries && !is_wp_error($countries)) ? $countries[0]->slug : '';
        $flag_icon    = liveradiostream_get_country_flag($country_slug);

        $languages  = get_the_terms($post_id, 'radio_language');
        $lang_name  = ($languages && !is_wp_error($languages)) ? $languages[0]->name : 'English';

        // Önce öne çýkan görseli veya logo meta alanýný alalým
        $thumb_url  = get_the_post_thumbnail_url($post_id, 'radio-thumb') ?: get_post_meta($post_id, 'logo_url', true);

        // Eðer logo yoksa veya eski placeholder kalýntýsý içeriyorsa dinamik renkli harfli logo üretelim
        if (empty($thumb_url) || strpos($thumb_url, 'placeholder') !== false) {
            $colors       = ['f97316', '3b82f6', '10b981', '8b5cf6', 'ec4899', '06b6d4', 'eab308', '6366f1'];
            $color_index  = abs(crc32($radio_title)) % count($colors);
            $bg_color     = $colors[$color_index];
            $first_letter = mb_substr(trim($radio_title), 0, 1);
            
            $thumb_url    = "https://placehold.co/300/{$bg_color}/ffffff/png?text=" . urlencode($first_letter) . "&font=montserrat";
        }

        $permalink  = get_permalink();
        $should_autoplay = isset($_GET['play']) && $_GET['play'] === 'true';
    ?>

    <!-- Schema.org JSON-LD for SEO -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "RadioStation",
      "name": "<?php echo esc_js($radio_title); ?>",
      "image": "<?php echo esc_js($thumb_url); ?>",
      "description": "Listen to <?php echo esc_js($radio_title); ?> live stream online on LiveRadioStream.net.",
      "genre": "<?php echo esc_js($genre_name); ?>",
      "url": "<?php echo esc_js($permalink); ?>",
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "<?php echo esc_js($rating_avg); ?>",
        "ratingCount": "<?php echo esc_js($votes_cnt); ?>"
      }
    }
    </script>

    <!-- Header Block Left-Aligned & Styled -->
    <div class="single-radio-header" id="single-radio-hero"
         data-autoplay="<?php echo $should_autoplay ? 'true' : 'false'; ?>"
         data-id="<?php echo esc_attr($post_id); ?>"
         data-stream="<?php echo esc_url($stream_url); ?>" 
         data-backup-stream="<?php echo esc_url($backup_url); ?>"
         data-title="<?php echo esc_attr($radio_title); ?>" 
         data-img="<?php echo esc_url($thumb_url); ?>"
         data-link="<?php echo esc_url($permalink); ?>">
         
        <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($radio_title); ?> Logo" onerror="this.style.display='none';">
        
        <div class="single-radio-info" style="text-align: left;">
            <span class="badge" style="background: var(--primary); color:#fff; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; display: inline-block; margin-bottom: 0.5rem;"><?php echo esc_html($genre_name); ?></span>
            
            <h1>Listen to <?php the_title(); ?> Live</h1>
            
            <!-- Tech Badges Bar -->
            <div class="tech-specs-pills">
                <span class="tech-pill">Location: <strong><?php echo $flag_icon . ' ' . esc_html($location ? $location : $country_name); ?></strong></span>
                <span class="tech-pill">Bitrate: <strong><?php echo esc_html($bitrate); ?></strong></span>
                <span class="tech-pill">Frequency: <strong><?php echo esc_html($frequency); ?></strong></span>
                <span class="tech-pill">Format: <strong><?php echo esc_html($format); ?></strong></span>
                <span class="tech-pill">Language: <strong><?php echo esc_html($lang_name); ?></strong></span>
            </div>

            <!-- Rating Box -->
            <div class="rating-box">
                <span class="stars" id="star-rating-btn" data-id="<?php echo esc_attr($post_id); ?>">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                <span style="font-weight: 700; color: #fff;" id="rating-val-display"><?php echo esc_html($rating_avg); ?></span> / 5.0 
                (<span id="votes-val-display"><?php echo esc_html($votes_cnt); ?></span> votes)
            </div>

            <!-- Action Buttons Row with Distinct Colored Buttons -->
            <div class="action-buttons-row">
                <button class="btn-play-hero js-play-trigger" 
                        data-id="<?php echo esc_attr($post_id); ?>"
                        data-stream="<?php echo esc_url($stream_url); ?>" 
                        data-backup-stream="<?php echo esc_url($backup_url); ?>"
                        data-title="<?php echo esc_attr($radio_title); ?>" 
                        data-img="<?php echo esc_url($thumb_url); ?>"
                        data-link="<?php echo esc_url($permalink); ?>">
                    &#9658; Play Live Stream
                </button>

                <!-- GREEN FAVORITE BUTTON -->
                <button class="btn-fav-green btn-favorite js-fav-toggle" 
                        data-id="<?php echo esc_attr($post_id); ?>" 
                        data-title="<?php echo esc_attr($radio_title); ?>" 
                        data-link="<?php echo esc_url($permalink); ?>">
                    Add Favorite
                </button>

                <!-- YELLOW EMBED PLAYER BUTTON -->
                <button class="btn-embed-yellow" id="embed-player-btn">
                    Embed Player
                </button>

                <!-- RED REPORT BROKEN BUTTON -->
                <button class="btn-report-red" id="report-broken-btn" data-id="<?php echo esc_attr($post_id); ?>">
                    Report Broken
                </button>
            </div>

            <!-- Yellow Bordered Embed Box -->
            <div class="embed-code-box" id="embed-code-box" style="display:none; margin-top: 1rem; padding: 1rem; background: #111827; border: 1px solid #f59e0b; border-radius: 8px;">
                <p style="font-size:0.88rem; color: #f59e0b; font-weight: 700; margin-bottom:0.4rem;">Copy this iFrame code to embed on your website or blog:</p>
                <textarea readonly style="width:100%; background:#0f172a; color:#fff; border:1px solid #1e293b; padding:8px; border-radius:4px;"><iframe src="<?php echo esc_url($permalink); ?>?embed=true" width="320" height="130" frameborder="0" allow="autoplay"></iframe></textarea>
            </div>
        </div>
    </div>

    <div class="layout-grid">
        <!-- Left Main Content (Left-Aligned) -->
        <div class="main-content" style="text-align: left;">
            <div style="background: var(--bg-card); padding: 2rem; border-radius: 14px; border: 1px solid var(--border-light);">
                <h2 style="font-size: 1.4rem; margin-bottom: 1rem; color: #fff;">About <?php the_title(); ?></h2>
                <div class="entry-content" style="color: var(--text-muted); line-height: 1.8;">
                    <?php 
                    $custom_desc = get_post_meta($post_id, '_radio_current_playing', true);
                    if (empty($custom_desc)) {
                        $custom_desc = get_post_meta($post_id, 'description', true);
                    }

                    if (!empty($custom_desc)) {
                        // html_entity_decode ve wpautop kullanýlarak HTML linkler tam týklanabilir hale getirildi
                        echo wpautop(wp_kses_post(html_entity_decode($custom_desc)));
                    } elseif (get_the_content()) {
                        the_content();
                    } else {
                        echo '<p>Listen to <strong>' . esc_html($radio_title) . '</strong> live radio stream online in high quality audio (' . esc_html($bitrate) . '). Enjoy non-stop music, live broadcasts, news updates, and sports directly in your web browser on LiveRadioStream.net with zero buffering and no registration required.</p>';
                    }
                    ?>
                </div>

                <!-- Comprehensive Social Sharing Section -->
                <div style="margin-top: 2rem; border-top: 1px solid var(--border-light); padding-top: 1.25rem;">
                    <h4 style="font-size:0.95rem; font-weight:700; color:#fff; margin-bottom:0.75rem;">Share This Station:</h4>
                    <div class="social-share-grid">
                        <a class="btn-share" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($permalink); ?>" target="_blank" rel="noopener">Facebook</a>
                        <a class="btn-share" href="https://twitter.com/intent/tweet?url=<?php echo urlencode($permalink); ?>&text=Listen+to+<?php echo urlencode($radio_title); ?>+live" target="_blank" rel="noopener">Twitter / X</a>
                        <a class="btn-share" href="https://api.whatsapp.com/send?text=<?php echo urlencode($permalink); ?>" target="_blank" rel="noopener">WhatsApp</a>
                        <a class="btn-share" href="https://t.me/share/url?url=<?php echo urlencode($permalink); ?>&text=Listen+to+<?php echo urlencode($radio_title); ?>+live" target="_blank" rel="noopener">Telegram</a>
                        <a class="btn-share" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($permalink); ?>" target="_blank" rel="noopener">LinkedIn</a>
                        <a class="btn-share" href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode($permalink); ?>&media=<?php echo urlencode($thumb_url); ?>" target="_blank" rel="noopener">Pinterest</a>
                        <a class="btn-share" href="mailto:?subject=Listen%20to%20<?php echo urlencode($radio_title); ?>&body=<?php echo urlencode($permalink); ?>">Email</a>
                    </div>
                </div>
            </div>

            <!-- Ultra Modern Custom Styled Comments Section (Kartýn Dýþýnda, Alt Kýsýmda) -->
            <div style="background: var(--bg-card); padding: 2rem; border-radius: 14px; border: 1px solid var(--border-light); margin-top: 2rem; color: #f8fafc;">
                <h3 style="color:#fff; margin-bottom:1.5rem; font-size: 1.3rem; display: flex; align-items: center; gap: 8px;">
                    Listener Comments & Discussion
                </h3>
                
                <?php
                $current_user = wp_get_current_user();
                $logged_in_as = '';
                if (is_user_logged_in()) {
                    $logout_url = esc_url(wp_logout_url(get_permalink()));
                    $profile_url = esc_url(admin_url('profile.php'));
                    $logged_in_as = sprintf(
                        '<p class="logged-in-as" style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 1.25rem;">Logged in as <a href="%s" style="color: #38bdf8; text-decoration: none;">%s</a>. <a href="%s" style="color: #ef4444; text-decoration: none; margin-left: 10px;">Log out?</a></p>',
                        $profile_url,
                        $current_user->display_name,
                        $logout_url
                    );
                }

                $commenter = wp_get_current_commenter();

                $comments_args = array(
                    'title_reply'          => 'Leave a Reply / Review',
                    'title_reply_to'       => 'Leave a Reply to %s',
                    'class_form'           => 'custom-modern-comment-form',
                    'logged_in_as'         => $logged_in_as,
                    'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s" style="background: #f97316; color: #fff; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: opacity 0.2s;">Post Comment</button>',
                    'comment_field'        => '<div class="comment-form-comment" style="margin-bottom: 1.25rem;"><label for="comment" style="display: block; color: #94a3b8; font-size: 0.9rem; margin-bottom: 6px; font-weight: 600;">Your Review / Message *</label><textarea id="comment" name="comment" cols="45" rows="5" required placeholder="Write your thoughts about this radio station..." style="width: 100%; background: #0b0f19; border: 1px solid #1e293b; color: #fff; padding: 12px; border-radius: 8px; outline: none; box-sizing: border-box; resize: vertical; font-family: inherit;"></textarea></div>',
                    'fields'               => array(
                        'author' => '<div class="comment-form-author" style="margin-bottom: 1.25rem;"><label for="author" style="display: block; color: #94a3b8; font-size: 0.9rem; margin-bottom: 6px; font-weight: 600;">Name *</label><input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author'] ?? '') . '" size="30" required style="width: 100%; background: #0b0f19; border: 1px solid #1e293b; color: #fff; padding: 10px 12px; border-radius: 8px; outline: none; box-sizing: border-box;" /></div>',
                        'email'  => '<div class="comment-form-email" style="margin-bottom: 1.25rem;"><label for="email" style="display: block; color: #94a3b8; font-size: 0.9rem; margin-bottom: 6px; font-weight: 600;">Email *</label><input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email'] ?? '') . '" size="30" required style="width: 100%; background: #0b0f19; border: 1px solid #1e293b; color: #fff; padding: 10px 12px; border-radius: 8px; outline: none; box-sizing: border-box;" /></div>',
                        'cookies' => '<div class="comment-form-cookies-consent" style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 8px;"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" style="accent-color: #f97316;" /><label for="wp-comment-cookies-consent" style="color: #94a3b8; font-size: 0.85rem; cursor: pointer;">Save my name, email, and website in this browser for the next time I comment.</label></div>',
                    ),
                    'comment_notes_before' => '<p class="comment-notes" style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 1.5rem;">Your email address will not be published. Required fields are marked *</p>',
                );
                
                comment_form($comments_args);
                
                // Doðrudan veritabanýndan bu radyoya ait onaylý yorumlarý çekiyoruz
                $station_comments = get_comments(array(
                    'post_id' => $post_id,
                    'status'  => 'approve',
                ));

                if ( !empty($station_comments) ) :
                ?>
                    <div class="comment-list-wrapper" style="margin-top: 2.5rem; border-top: 1px solid #1e293b; padding-top: 1.5rem;">
                        <h4 style="color: #fff; font-size: 1.1rem; margin-bottom: 1.5rem;">Comments (<?php echo count($station_comments); ?>)</h4>
                        <ul class="comment-list" style="list-style: none; padding: 0; margin: 0;">
                            <?php
                            foreach($station_comments as $com) {
                                ?>
                                <li style="background: #0b0f19; border: 1px solid #1e293b; border-radius: 10px; padding: 1.25rem; margin-bottom: 1rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <?php echo get_avatar($com, 40, '', '', array('style' => 'border-radius: 50%; border: 1px solid #334155;')); ?>
                                            <div>
                                                <h5 style="color: #fff; margin: 0; font-size: 0.95rem; font-weight: 700;"><?php echo esc_html($com->comment_author); ?></h5>
                                                <span style="color: #64748b; font-size: 0.75rem;"><?php echo esc_html(get_comment_date('', $com)); ?> at <?php echo esc_html(get_comment_time('', false, $com)); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="color: #cbd5e1; font-size: 0.9rem; line-height: 1.6;">
                                        <?php echo wpautop(esc_html($com->comment_content)); ?>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Sidebar Area -->
        <aside class="sidebar">
            
            <!-- 1. Distinct Green Favorites Accent Box -->
            <div class="sidebar-box green-accent-box">
                <h3>
                    <svg width="20" height="20" fill="var(--fav-green)" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    <span>Favorite Stations (<span id="fav-count">0</span>/50)</span>
                </h3>
                <ul class="sidebar-list" id="favorites-list">
                    <li style="color: var(--text-muted);">No favorite stations added yet.</li>
                </ul>
            </div>

            <!-- 2. Distinct Blue History Accent Box -->
            <div class="sidebar-box blue-accent-box">
                <h3>
                    <svg width="20" height="20" fill="var(--accent-blue)" viewBox="0 0 24 24"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
                    <span>Recently Listened</span>
                </h3>
                <ul class="sidebar-list" id="history-list">
                    <li style="color: var(--text-muted);">Your recently played stations will appear here.</li>
                </ul>
            </div>

            <!-- 3. Distinct Purple Similar Stations Accent Box -->
            <div class="sidebar-box purple-accent-box">
                <h3>
                    <svg width="20" height="20" fill="var(--accent-purple)" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
                    <span>Similar Radio Stations</span>
                </h3>
                <div class="similar-radios-list">
                    <?php
                    $similar_query = new WP_Query(array(
                        'post_type'      => 'radio',
                        'posts_per_page' => 6,
                        'post__not_in'   => array(get_the_ID()),
                        'orderby'        => 'rand'
                    ));
                    if ($similar_query->have_posts()) {
                        while ($similar_query->have_posts()) {
                            $similar_query->the_post();
                            $s_id = get_the_ID();
                            $s_title = get_the_title();
                            $s_stream = get_post_meta($s_id, '_radio_stream_url', true);
                            if (empty($s_stream)) {
                                $s_stream = get_post_meta($s_id, 'stream_url', true);
                            }
                            $s_backup = get_post_meta($s_id, '_radio_backup_stream_url', true);
                            
                            $s_thumb = get_the_post_thumbnail_url($s_id, 'radio-thumb') ?: get_post_meta($s_id, 'logo_url', true);
                            if (empty($s_thumb) || strpos($s_thumb, 'placeholder') !== false) {
                                $s_colors = ['f97316', '3b82f6', '10b981', '8b5cf6', 'ec4899', '06b6d4', 'eab308', '6366f1'];
                                $s_color_idx = abs(crc32($s_title)) % count($s_colors);
                                $s_thumb = "https://placehold.co/100/{$s_colors[$s_color_idx]}/ffffff/png?text=" . urlencode(mb_substr(trim($s_title), 0, 1)) . "&font=montserrat";
                            }

                            $s_link   = get_permalink();
                            $s_genres = get_the_terms($s_id, 'radio_genre');
                            $s_genre  = ($s_genres && !is_wp_error($s_genres)) ? $s_genres[0]->name : 'General';
                            
                            $s_colors = ['f97316', '3b82f6', '10b981', '8b5cf6', 'ec4899', '06b6d4', 'eab308', '6366f1'];
                            $s_color_idx = abs(crc32($s_title)) % count($s_colors);
                            $box_bg = '#' . $s_colors[$s_color_idx];
                            ?>
                            <div class="similar-radio-item">
                                <div class="station-logo-wrapper" style="position: relative; display: inline-block; width: 50px; height: 50px; flex-shrink: 0;">
                                    <img src="<?php echo esc_url($s_thumb); ?>" alt="<?php echo esc_attr($s_title); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    
                                    <div class="fallback-letter-avatar" style="display: none; width: 50px; height: 50px; background: <?php echo $box_bg; ?>; color: #fff; font-weight: bold; font-size: 1.2rem; align-items: center; justify-content: center; border-radius: 8px; position: absolute; top: 0; left: 0;">
                                        <?php echo mb_strtoupper(mb_substr(trim($s_title), 0, 1)); ?>
                                    </div>
                                </div>

                                <div class="similar-radio-details">
                                    <h4><a href="<?php echo esc_url($s_link); ?>"><?php echo esc_html($s_title); ?></a></h4>
                                    <p><?php echo esc_html($s_genre); ?></p>
                                </div>
                                <button class="btn-mini-play js-play-trigger" 
                                        title="Play Station"
                                        data-id="<?php echo esc_attr($s_id); ?>"
                                        data-stream="<?php echo esc_url($s_stream); ?>" 
                                        data-backup-stream="<?php echo esc_url($s_backup); ?>"
                                        data-title="<?php echo esc_attr($s_title); ?>" 
                                        data-img="<?php echo esc_url($s_thumb); ?>"
                                        data-link="<?php echo esc_url($s_link); ?>">
                                    &#9658;
                                </button>
                            </div>
                            <?php
                        }
                        wp_reset_postdata();
                    } else {
                        echo '<p style="color: var(--text-muted);">No similar stations found.</p>';
                    }
                    ?>
                </div>
            </div>

        </aside>
    </div>

    <?php endwhile; ?>
</main>

<!-- Kýrýk Yayýn Bildirim (Report Broken) AJAX Script'i -->
<script>
document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'report-broken-btn') {
        const radioId = e.target.getAttribute('data-id');
        console.log("Radyo ID yakalandý:", radioId);
        
        if (!radioId) return;

        e.target.disabled = true;
        e.target.innerText = 'Reporting...';

        const ajaxUrl = (typeof RadioApp !== 'undefined' && RadioApp.ajaxurl) ? RadioApp.ajaxurl : '<?php echo admin_url('admin-ajax.php'); ?>';

        fetch(ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=report_broken_radio&radio_id=' + radioId
        })
        .then(response => response.json())
        .then(data => {
            console.log("Sunucu yanýtý:", data);
            if (data.success) {
                e.target.innerText = 'Reported Successfully!';
                e.target.style.background = '#10b981';
            } else {
                e.target.innerText = 'Error!';
                e.target.disabled = false;
            }
        })
        .catch(error => {
            console.error('AJAX Hatasý:', error);
            e.target.innerText = 'Failed';
            e.target.disabled = false;
        });
    }
});
</script>
<?php get_footer(); ?>