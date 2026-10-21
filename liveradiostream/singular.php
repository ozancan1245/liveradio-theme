<?php
/**
 * Template Name: Standard Page Template
 * Description: LiveRadioStream.net için SEO uyumlu sayfa þablonu
 */

get_header();

$current_slug = get_post_field('post_name', get_post());
$page_title   = get_the_title();
?>

<style>
.seo-page-container {
    max-width: 1200px !important;
    margin: 2rem auto !important;
    padding: 0 1rem !important;
}
.seo-content-box {
    background: #0f172a !important;
    border: 1px solid #1e293b !important;
    border-radius: 12px !important;
    padding: 2.5rem !important;
    color: #f8fafc !important;
    box-sizing: border-box !important;
}
.seo-page-title {
    color: #ffffff !important;
    font-size: 1.8rem !important;
    font-weight: 700 !important;
    margin-top: 0 !important;
    margin-bottom: 1.5rem !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    border-bottom: 1px solid #1e293b !important;
    padding-bottom: 1rem !important;
}
.seo-page-title .title-dot {
    display: inline-block !important;
    width: 10px !important;
    height: 10px !important;
    border-radius: 50% !important;
    background: #0ea5e9 !important;
    box-shadow: 0 0 10px #0ea5e9 !important;
}
.seo-content-body {
    font-size: 1rem !important;
    line-height: 1.8 !important;
    color: #94a3b8 !important;
}
.seo-content-body h2 {
    color: #ffffff !important;
    font-size: 1.3rem !important;
    margin-top: 1.8rem !important;
    margin-bottom: 0.75rem !important;
}
.seo-content-body p {
    margin-bottom: 1rem !important;
}
.seo-content-body ul {
    margin-bottom: 1rem !important;
    padding-left: 1.5rem !important;
}
.seo-content-body li {
    margin-bottom: 0.5rem !important;
}
.seo-content-body a {
    color: #38bdf8 !important;
    text-decoration: underline !important;
}
.back-home-btn {
    display: inline-block !important;
    margin-top: 2.5rem !important;
    background: #f97316 !important;
    color: #ffffff !important;
    padding: 10px 20px !important;
    border-radius: 8px !important;
    text-decoration: none !important;
    font-weight: 600 !important;
    transition: background 0.2s ease !important;
}
.back-home-btn:hover {
    background: #ea580c !important;
}
</style>

<div class="seo-page-container">
    <div class="seo-content-box">
        <?php while (have_posts()) : the_post(); ?>
            <h1 class="seo-page-title">
                <span class="title-dot"></span>
                <?php echo esc_html($page_title); ?>
            </h1>
            
            <div class="seo-content-body">
                <?php 
                $content = get_the_content();
                if (empty(trim($content))) {
                    if ($current_slug == 'terms' || $current_slug == 'terms-of-service') {
                        echo '<h2>Terms & Conditions of Use</h2>';
                        echo '<p>By accessing, browsing, or utilizing <strong>LiveRadioStream.net</strong>, you explicitly agree to comply with and be bound by the following terms and conditions.</p>';
                    } elseif ($current_slug == 'privacy' || $current_slug == 'privacy-policy') {
                        echo '<h2>Privacy Commitment</h2>';
                        echo '<p>At <strong>LiveRadioStream.net</strong>, protecting your personal privacy is a top priority.</p>';
                    } elseif ($current_slug == 'faq') {
                        echo '<h2>Frequently Asked Questions</h2>';
                        echo '<p><strong>Q: Is LiveRadioStream.net free?</strong><br>A: Yes! All radio stations and genres are 100% free.</p>';
                    } elseif ($current_slug == 'about' || $current_slug == 'about-us') {
                        echo '<h2>About LiveRadioStream.net</h2>';
                        echo '<p>Welcome to <strong>LiveRadioStream.net</strong> — your global gateway to live FM, AM, and internet radio broadcasting.</p>';
                    } elseif ($current_slug == 'dmca') {
                        echo '<h2>DMCA Copyright Compliance</h2>';
                        echo '<p><strong>LiveRadioStream.net</strong> respects intellectual property rights and operates strictly as an indexed directory.</p>';
                    } elseif ($current_slug == 'advertise') {
                        echo '<h2>Advertise With Us</h2>';
                        echo '<p>Expand your audio brand\'s reach by partnering with <strong>LiveRadioStream.net</strong>.</p>';
                    } else {
                        the_content();
                    }
                } else {
                    the_content();
                }
                ?>
            </div>
        <?php endwhile; ?>

        <div style="border-top: 1px solid #1e293b; margin-top: 2rem; padding-top: 1rem;">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="back-home-btn">&laquo; Return to Homepage</a>
        </div>
    </div>
</div>

<?php
get_footer();
?>