<?php
/**
 * Header Template for LiveRadioStream.net
 * Optimized Speed Build
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- HLS.js Library for .m3u8 Live Streams -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container header-inner">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-title">
            <svg class="site-title-icon" viewBox="0 0 24 24" fill="#f97316"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
            LiveRadio<span>Stream.net</span>
        </a>

        <!-- Advanced Instant Live Search -->
        <div class="header-search-wrap">
            <div class="search-input-inner-box">
                <svg class="search-icon-inside" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" id="live-search-input" class="header-search-input" placeholder="Search radio station name, genre, country..." autocomplete="off">
                <button type="button" id="clear-search-btn" class="clear-search-btn">✕</button>
            </div>
            <div id="live-search-results" class="live-search-results"></div>
        </div>

        <nav class="main-nav">
            <ul>
                <li>
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <svg class="nav-svg-icon" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        Home
                    </a>
                </li>
                <li>
                    <a href="#favorites" id="nav-fav-link">
                        <svg class="nav-svg-icon" fill="#10b981" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        Favorites
                    </a>
                </li>
                <li>
                    <a href="#history" id="nav-history-link">
                        <svg class="nav-svg-icon" fill="#38bdf8" viewBox="0 0 24 24"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
                        History
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Üyelik Butonları -->
        <div class="header-user-area" style="display: flex; align-items: center; gap: 10px;">
            <?php if (is_user_logged_in()) : 
                $current_user = wp_get_current_user();
            ?>
                <span style="color: #94a3b8; font-size: 0.88rem; font-weight: 600; white-space: nowrap;">
                    <strong style="color: #fff;"><?php echo esc_html($current_user->display_name); ?></strong>
                </span>
                <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" style="background: #ef4444; color: #fff; padding: 7px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; text-decoration: none;">
                    Logout
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/sign-in/')); ?>" style="background: transparent; color: #fff; border: 1.5px solid var(--border-light); padding: 7px 16px; border-radius: 8px; font-size: 0.88rem; font-weight: 700; text-decoration: none; white-space: nowrap; transition: border-color 0.2s;">
                    Sign In
                </a>
                <a href="<?php echo esc_url(home_url('/sign-up/')); ?>" style="background: var(--primary); color: #fff; padding: 7px 18px; border-radius: 8px; font-size: 0.88rem; font-weight: 700; text-decoration: none; box-shadow: 0 4px 15px rgba(249, 115, 22, 0.35); white-space: nowrap;">
                    Sign Up
                </a>
            <?php endif; ?>
        </div>

    </div>
</header>