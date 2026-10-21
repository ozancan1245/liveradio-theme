<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <h2>Live Radio Stream</h2>
            <p style="font-size: 1.1rem; color: #fff; font-weight: 600; margin-bottom: 0.75rem;">Free live FM, AM & internet radio from around the world</p>
            <p style="line-height: 1.7; font-size: 0.95rem;">
                Listen to live radio stations worldwide on <strong>LiveRadioStream.net</strong> &mdash; Discover top FM, AM, and online radio channels for free. Experience seamless music, news, sports, and talk radio in one place. Browse by genre, country, or language, search for your favorite station, and start playing instantly in your browser &mdash; no registration, no apps, no subscription, 24/7 on any device.
            </p>
        </div>

        <style>
            .footer-grid {
                display: grid !important;
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                gap: 1.5rem !important;
            }
            @media (max-width: 992px) {
                .footer-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
            }
            @media (max-width: 576px) {
                .footer-grid { grid-template-columns: repeat(1, minmax(0, 1fr)) !important; }
            }
        </style>

        <div class="footer-grid">
            <div class="footer-col">
                <h3>Genres</h3>
                <ul>
                    <?php
                    $top_footer_genres = get_terms(array(
                        'taxonomy'   => 'radio_genre',
                        'hide_empty' => false,
                        'orderby'    => 'count',
                        'order'      => 'DESC',
                        'number'     => 7,
                    ));

                    if (!empty($top_footer_genres) && !is_wp_error($top_footer_genres)) {
                        foreach ($top_footer_genres as $f_genre) {
                            echo '<li><a href="' . esc_url(get_term_link($f_genre)) . '">' . esc_html($f_genre->name) . ' <span style="opacity: 0.7; font-size: 0.85rem;">(' . $f_genre->count . ')</span></a></li>';
                        }
                    } else {
                        echo '<li><a href="#">Pop</a></li>';
                        echo '<li><a href="#">Rock</a></li>';
                        echo '<li><a href="#">Jazz</a></li>';
                    }
                    ?>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Countries</h3>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/country/united-states-of-america/')); ?>"><strong class="flag-badge">US</strong> USA</a></li>
                    <li><a href="<?php echo esc_url(home_url('/country/germany/')); ?>"><strong class="flag-badge">DE</strong> Germany</a></li>
                    <li><a href="<?php echo esc_url(home_url('/country/united-kingdom/')); ?>"><strong class="flag-badge">GB</strong> UK</a></li>
                    <li><a href="<?php echo esc_url(home_url('/country/france/')); ?>"><strong class="flag-badge">FR</strong> France</a></li>
                    <?php 
                    $turkey_term = get_term_by('slug', 'turkey', 'radio_country');
                    $turkey_url = $turkey_term ? get_term_link($turkey_term) : home_url('/radio_country/turkey/');
                    ?>
                    <li><a href="<?php echo esc_url($turkey_url); ?>"><strong class="flag-badge">TR</strong> Turkey</a></li>
                    <li><a href="<?php echo esc_url(home_url('/country/russia/')); ?>"><strong class="flag-badge">RU</strong> Russia</a></li>
                    <li><a href="<?php echo esc_url(home_url('/country/canada/')); ?>"><strong class="flag-badge">CA</strong> Canada</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Discover</h3>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/trending/')); ?>">Trending</a></li>
                    <li><a href="<?php echo esc_url(home_url('/most-played/')); ?>">Most Played</a></li>
                    <li><a href="<?php echo esc_url(home_url('/new-stations/')); ?>">New Stations</a></li>
                    <li><a href="<?php echo esc_url(home_url('/random-play/')); ?>">Random Play</a></li>
                    <li><a href="#favorites">Favorites</a></li>
                    <li><a href="#history">History</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Links</h3>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/terms/')); ?>">Terms</a></li>
                    <li><a href="<?php echo esc_url(home_url('/faq/')); ?>">FAQ</a></li>
                    <li><a href="<?php echo esc_url(home_url('/advertise/')); ?>">Advertise</a></li>
                    <li><a href="<?php echo esc_url(home_url('/dmca/')); ?>">DMCA</a></li>
                    <li><a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a></li>
                    <li><a href="<?php echo esc_url(home_url('/about/')); ?>">About Us</a></li>
                    <li><a href="<?php echo esc_url(home_url('/privacy/')); ?>">Privacy</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <strong>LiveRadioStream.net</strong>. All rights reserved.</p>
        </div>
    </div>
</footer>

<div id="radio-player-bar" class="sticky-player">
    <div class="player-inner">
        <div class="player-info">
            <img id="player-img" src="https://via.placeholder.com/100/151e32/ffffff?text=Radio" class="player-thumb" alt="LiveRadioStream.net">
            <div class="player-details">
                <h4 id="player-title">No Station Selected</h4>
                <p id="player-status">Click play on any radio station</p>
            </div>
        </div>
        
        <div class="player-controls">
            <button id="player-play-btn" class="btn-play-sticky" aria-label="Play/Pause">&#9654;</button>
        </div>

        <div class="player-volume">
            <svg width="20" height="20" fill="var(--text-muted)" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
            <input type="range" id="player-volume-slider" aria-label="Volume Control" min="0" max="1" step="0.05" value="0.8">
        </div>
    </div>
</div>

<audio id="main-audio-element" preload="none"></audio>

<style>
.live-search-popover {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    margin-top: 8px;
    background: #0f172a !important;
    border: 1px solid #1e293b !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
    z-index: 999999 !important;
    max-height: 380px;
    overflow-y: auto;
    display: none;
    padding: 8px 0;
}
.live-search-popover.active { display: block !important; }
.live-search-item {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    padding: 8px 12px !important;
    text-decoration: none !important;
    color: #f8fafc !important;
    transition: background 0.15s ease !important;
    border-bottom: 1px solid rgba(30, 41, 59, 0.5);
}
.live-search-item:last-child { border-bottom: none; }
.live-search-item:hover, .live-search-item.selected { background: #1e293b !important; }
.live-search-thumb {
    width: 40px !important; height: 40px !important;
    border-radius: 6px !important; object-fit: cover !important;
    background: #1e293b; flex-shrink: 0;
}
.live-search-info { display: flex; flex-direction: column; min-width: 0; flex: 1; }
.live-search-title {
    font-size: 0.9rem !important; font-weight: 600 !important;
    color: #ffffff !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.live-search-genre { font-size: 0.75rem !important; color: #f97316 !important; font-weight: 500; }
.live-search-status { padding: 16px 12px; text-align: center; color: #94a3b8; font-size: 0.88rem; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Arama Kutusu Canlı Öneri Sistemi
    var searchInputs = document.querySelectorAll('input[type="search"], input[name="s"], .search-input');
    searchInputs.forEach(function(input) {
        var wrapper = input.parentElement;
        if (getComputedStyle(wrapper).position === 'static') { wrapper.style.position = 'relative'; }
        var popover = document.createElement('div');
        popover.className = 'live-search-popover';
        wrapper.appendChild(popover);
        var debounceTimer;
        input.addEventListener('input', function() {
            var query = this.value.trim();
            clearTimeout(debounceTimer);
            if (query.length < 2) { popover.classList.remove('active'); popover.innerHTML = ''; return; }
            popover.classList.add('active');
            popover.innerHTML = '<div class="live-search-status">Searching stations...</div>';
            debounceTimer = setTimeout(function() {
                var ajaxUrl = (typeof live_search_params !== 'undefined') ? live_search_params.ajax_url : '/wp-admin/admin-ajax.php';
                fetch(ajaxUrl + '?action=live_radio_search&keyword=' + encodeURIComponent(query))
                    .then(function(res) { return res.json(); })
                    .then(function(response) {
                        if (response.success && response.data.length > 0) {
                            var html = '';
                            response.data.forEach(function(item) {
                                html += '<a href="' + item.permalink + '" class="live-search-item">' +
                                        '<img src="' + item.thumb + '" class="live-search-thumb" alt="' + item.title + '">' +
                                        '<div class="live-search-info">' +
                                            '<span class="live-search-title">' + item.title + '</span>' +
                                            '<span class="live-search-genre">' + item.genre + '</span>' +
                                        '</div>' +
                                    '</a>';
                            });
                            popover.innerHTML = html;
                        } else {
                            popover.innerHTML = '<div class="live-search-status">No radio station found matching "' + query + '"</div>';
                        }
                    })
                    .catch(function() { popover.innerHTML = '<div class="live-search-status">An error occurred while searching.</div>'; });
            }, 250);
        });
        document.addEventListener('click', function(e) { if (!wrapper.contains(e.target)) { popover.classList.remove('active'); } });
        input.addEventListener('keydown', function(e) { if (e.key === 'Escape') { popover.classList.remove('active'); } });
    });

    // Sidebar ve Footer'daki olası Türkiye yazılarını ve linklerini Turkey ile sabitleme güvenlik scripti
    document.querySelectorAll('a').forEach(function(link) {
        let text = link.textContent.trim();
        let href = link.getAttribute('href') || '';
        if (text.toLowerCase().includes('türkiye') || href.includes('türkiye') || href.includes('turkiye')) {
            if (text.toLowerCase().includes('türkiye')) {
                link.innerHTML = link.innerHTML.replace(/türkiye/gi, 'Turkey');
            }
            if (href.includes('/country/türkiye') || href.includes('/country/turkiye')) {
                link.setAttribute('href', '<?php echo esc_url($turkey_url); ?>');
            }
        }
    });
});
</script>

<?php wp_footer(); ?>