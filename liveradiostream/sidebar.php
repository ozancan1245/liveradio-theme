<?php
/**
 * Global Sidebar Template for LiveRadioStream.net
 */
?>
<aside class="sidebar-area">
    <!-- Live Search Box (player.js Uyumlu) -->
    <div class="sidebar-box search-box-container" style="margin-bottom: 1.25rem; position: relative;">
        <h3 class="sidebar-title">&#128269; Search Stations</h3>
        <div style="position: relative; display: flex; align-items: center;">
            <input type="text" id="live-search-input" placeholder="Search radio stations..." autocomplete="off" style="width: 100%; padding: 10px 35px 10px 12px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #fff; font-size: 0.9rem;">
            <button type="button" id="clear-search-btn" style="display: none; position: absolute; right: 10px; background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1.1rem;" title="Clear">&times;</button>
        </div>
        <!-- Arama Sonuçlarının Döküleceği Açılır Pencere -->
        <div id="live-search-results" class="search-results-dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 9999; background: #1e293b; border: 1px solid #334155; border-radius: 8px; margin-top: 5px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); max-height: 350px; overflow-y: auto;"></div>
    </div>

    <!-- 1. Favorite Stations Box -->
    <div class="sidebar-box green-accent-box" id="favorites" style="margin-bottom: 1.25rem;">
        <h3 class="sidebar-title">&#10084; Favorite Stations (<span id="fav-count">0</span>/50)</h3>
        <ul id="favorites-list" class="sidebar-fav-list">
            <li style="color: #94a3b8; font-size: 0.85rem;">No favorite stations added yet.</li>
        </ul>
    </div>

    <!-- 2. Recently Listened Box -->
    <div class="sidebar-box blue-accent-box" id="history" style="margin-bottom: 1.25rem;">
        <h3 class="sidebar-title">&#128338; Recently Listened</h3>
        <ul id="history-list" class="sidebar-hist-list">
            <li style="color: #94a3b8; font-size: 0.85rem;">Your listening history will appear here.</li>
        </ul>
    </div>

    <!-- 3. Top Countries Box -->
    <div class="sidebar-box countries-box">
        <h3 class="sidebar-title">&#127757; Top Countries</h3>
        <ul class="sidebar-country-list">
            <?php
            $top_countries = get_terms(array(
                'taxonomy'   => array('radio_country', 'country'),
                'number'     => 10,
                'orderby'    => 'count',
                'order'      => 'DESC',
                'hide_empty' => false
            ));

            if (!empty($top_countries) && !is_wp_error($top_countries) && is_array($top_countries)) :
                foreach ($top_countries as $c_term) :
                    if (!is_object($c_term) || !isset($c_term->slug)) continue;
                    $term_link = get_term_link($c_term);
                    if (is_wp_error($term_link)) continue;
                    
                    $c_name = trim(preg_replace('/^[\?\s\x00-\x1F\x7F-\x9F]+/u', '', $c_term->name));
                    $c_name = trim(preg_replace('/^[^\p{L}\p{N}]+/u', '', $c_name));
                    
                    // Doğrudan dahili akıllı ISO ülke kodları eşlemesi
                    $iso_map = array(
                        'türkiye' => 'TR', 'turkey' => 'TR',
                        'united states' => 'US', 'usa' => 'US',
                        'united kingdom' => 'GB', 'uk' => 'GB',
                        'germany' => 'DE', 'almanya' => 'DE',
                        'france' => 'FR', 'fransa' => 'FR',
                        'argentina' => 'AR', 'arjantin' => 'AR',
                        'oman' => 'OM',
                        'brazil' => 'BR', 'brezilya' => 'BR',
                        'colombia' => 'CO', 'kolombiya' => 'CO',
                        'chile' => 'CL', 'şili' => 'CL',
                        'ireland' => 'IE', 'irlanda' => 'IE',
                        'italy' => 'IT', 'italya' => 'IT',
                        'spain' => 'ES', 'ispanya' => 'ES',
                        'russia' => 'RU', 'rusya' => 'RU',
                        'canada' => 'CA', 'kanada' => 'CA',
                        'global' => 'GL'
                    );

                    $slug_key = strtolower(trim($c_term->slug));
                    $name_key = strtolower(trim($c_name));

                    if (isset($iso_map[$name_key])) {
                        $flag = $iso_map[$name_key];
                    } elseif (isset($iso_map[$slug_key])) {
                        $flag = $iso_map[$slug_key];
                    } else {
                        $flag = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $c_term->slug), 0, 2));
                        if (strlen($flag) < 2) {
                            $flag = 'US';
                        }
                    }
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