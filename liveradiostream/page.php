<?php
get_header();

// Mevcut sayfanın slug (url) bilgisini alalım
$slug = get_post_field('post_name', get_post());

// Eğer panelden içerik girilmediyse sayfaya özel varsayılan SEO metinleri
$default_contents = array(
    'terms' => 'By accessing, browsing, or utilizing <strong>LiveRadioStream.net</strong>, you explicitly agree to comply with these terms and conditions. We provide a global directory for online FM, AM, and web radio streams for personal entertainment.',
    'privacy' => 'At <strong>LiveRadioStream.net</strong>, safeguarding your personal privacy is our top priority. This privacy policy outlines what data we collect, how we use it, and how we protect your browsing experience.',
    'faq' => 'Find answers to common questions about free global streaming, how to add your radio station, how to use our audio player, and technical troubleshooting tips.',
    'about' => 'Welcome to <strong>LiveRadioStream.net</strong>, your premier global destination for high-quality, free live FM, AM, and internet radio broadcasting. We connect listeners worldwide to top-tier broadcasters seamlessly.',
    'advertise' => 'Reach a massive global audience of music and talk-radio enthusiasts by advertising on <strong>LiveRadioStream.net</strong>. Contact our marketing desk for custom banner opportunities and sponsored streams.',
    'dmca' => 'We comply strictly with the Digital Millennium Copyright Act (DMCA). If you believe your copyrighted material is listed without authorization, please review our removal guidelines.'
);

$default_excerpts = array(
    'terms' => 'Terms of Service and guidelines for using LiveRadioStream.net safely and legally.',
    'privacy' => 'How we protect your data and respect your digital privacy while streaming online radio.',
    'faq' => 'Everything you need to know about free global radio streaming and platform features.',
    'about' => 'Discover the ultimate global radio experience connecting millions of listeners worldwide.',
    'advertise' => 'Promote your brand, audio services, or station to a worldwide audience.',
    'dmca' => 'Copyright infringement notification and removal request guidelines.'
);

// WordPress panelinden girilen gerçek içerik
$content = get_the_content();
$excerpt = get_the_excerpt();

// Eğer içerik boşsa varsayılan metni kullan
if (empty(trim(strip_tags($content))) && isset($default_contents[$slug])) {
    $content = '<p>' . $default_contents[$slug] . '</p>';
}

if (empty(trim($excerpt)) && isset($default_excerpts[$slug])) {
    $excerpt = $default_excerpts[$slug];
}
?>
<div class="container" style="max-width: 950px; margin: 3rem auto; padding: 2.5rem; background: #0f172a !important; border: 1px solid #1e293b !important; border-radius: 12px !important; box-sizing: border-box !important; box-shadow: 0 10px 25px rgba(0,0,0,0.4) !important; color: #f8fafc !important;">
    
    <h1 style="color: #fff !important; font-size: 2.2rem !important; font-weight: 800 !important; margin-top: 0 !important; margin-bottom: 0.5rem !important; display: flex !important; align-items: center !important; gap: 10px !important;">
        <span style="width: 12px; height: 12px; background: #38bdf8; border-radius: 50%; display: inline-block;"></span> 
        <?php the_title(); ?>
    </h1>

    <?php if (!empty($excerpt)) : ?>
        <p style="color: #94a3b8 !important; font-size: 1.05rem !important; margin-bottom: 2rem !important; line-height: 1.6 !important;">
            <?php echo esc_html($excerpt); ?>
        </p>
    <?php endif; ?>

    <div style="background: #111827 !important; padding: 2.2rem !important; border: 1px solid #1e293b !important; border-radius: 10px !important; line-height: 1.9 !important; color: #cbd5e1 !important; font-size: 1rem !important;">
        <?php echo apply_filters('the_content', $content); ?>
    </div>

    <div style="margin-top: 2rem !important; display: flex !important; gap: 1rem !important; flex-wrap: wrap !important;">
        <a href="<?php echo esc_url(home_url('/')); ?>" style="display: inline-flex !important; align-items: center !important; gap: 8px !important; background: #1e293b !important; color: #f97316 !important; padding: 12px 18px !important; border-radius: 6px !important; text-decoration: none !important; font-size: 0.95rem !important; font-weight: 600 !important;">
            « Return to Homepage
        </a>
        <a href="<?php echo esc_url(home_url('/contact/')); ?>" style="display: inline-flex !important; align-items: center !important; gap: 8px !important; background: #f97316 !important; color: #fff !important; padding: 12px 18px !important; border-radius: 6px !important; text-decoration: none !important; font-size: 0.95rem !important; font-weight: 600 !important;">
            ✉ Contact Support
        </a>
    </div>

</div>
<?php
get_footer();