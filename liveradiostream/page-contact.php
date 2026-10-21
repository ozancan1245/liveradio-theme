<?php
/* Template Name: Contact Page */
get_header();

$sent_success = false;
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_nonce'])) {
    if (wp_verify_nonce($_POST['contact_nonce'], 'send_contact_message')) {
        $name    = sanitize_text_field($_POST['c_name']);
        $email   = sanitize_email($_POST['c_email']);
        $message = sanitize_textarea_field($_POST['c_message']);

        if (!empty($name) && !empty($email) && !empty($message) && is_email($email)) {
            $to      = 'support@liveradiostream.net';
            $subject = 'New Contact Message from ' . $name;
            $body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";
            $headers = array('Content-Type: text/plain; charset=UTF-8', 'From: ' . $name . ' <' . $email . '>');

            if (wp_mail($to, $subject, $body, $headers)) {
                $sent_success = true;
            } else {
                $error_msg = 'E-posta gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
            }
        } else {
            $error_msg = 'Lütfen tüm alanları eksiksiz ve geçerli bir e-posta ile doldurun.';
        }
    }
}
?>
<div class="container" style="max-width: 950px; margin: 3rem auto; padding: 2.5rem; background: #0f172a !important; border: 1px solid #1e293b !important; border-radius: 12px !important; box-sizing: border-box !important; box-shadow: 0 10px 25px rgba(0,0,0,0.4) !important; color: #f8fafc !important;">
    
    <h1 style="color: #fff !important; font-size: 2.2rem !important; font-weight: 800 !important; margin-top: 0 !important; margin-bottom: 0.5rem !important; display: flex !important; align-items: center !important; gap: 10px !important;">
        <span style="width: 12px; height: 12px; background: #f97316; border-radius: 50%; display: inline-block;"></span> 
        Contact Us | Live Radio Stream Support
    </h1>
    <p style="color: #94a3b8 !important; font-size: 1.05rem !important; margin-bottom: 2rem !important; line-height: 1.6 !important;">
        Have questions about our global FM, AM, and online streaming directory, want to submit a new radio station, or report a technical issue? Our dedicated support team is available 24/7 to assist you.
    </p>

    <?php if ($sent_success): ?>
        <div style="background: rgba(16, 185, 129, 0.15) !important; border: 1px solid #10b981 !important; color: #10b981 !important; padding: 1rem 1.25rem !important; border-radius: 8px !important; margin-bottom: 1.5rem !important; font-weight: 600 !important;">
            ✓ Your message has been sent successfully! We will get back to you soon.
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div style="background: rgba(239, 68, 68, 0.15) !important; border: 1px solid #ef4444 !important; color: #ef4444 !important; padding: 1rem 1.25rem !important; border-radius: 8px !important; margin-bottom: 1.5rem !important; font-weight: 600 !important;">
            <?php echo esc_html($error_msg); ?>
        </div>
    <?php endif; ?>

    <div style="display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 2rem !important; align-items: start !important;">
        <div style="background: #111827 !important; padding: 1.8rem !important; border: 1px solid #1e293b !important; border-radius: 10px !important;">
            <h3 style="color: #fff !important; font-size: 1.25rem !important; margin-top: 0 !important; margin-bottom: 1rem !important; font-weight: 700 !important;">Send Us a Message</h3>
            <form action="" method="POST" style="display: flex !important; flex-direction: column !important; gap: 1rem !important;">
                <?php wp_nonce_field('send_contact_message', 'contact_nonce'); ?>
                <div>
                    <label style="display: block !important; font-size: 0.85rem !important; color: #94a3b8 !important; margin-bottom: 4px !important;">Your Name</label>
                    <input type="text" name="c_name" placeholder="John Doe" required style="width: 100% !important; padding: 10px !important; background: #0f172a !important; border: 1px solid #1e293b !important; border-radius: 6px !important; color: #fff !important; outline: none !important; box-sizing: border-box !important;">
                </div>
                <div>
                    <label style="display: block !important; font-size: 0.85rem !important; color: #94a3b8 !important; margin-bottom: 4px !important;">Email Address</label>
                    <input type="email" name="c_email" placeholder="john@example.com" required style="width: 100% !important; padding: 10px !important; background: #0f172a !important; border: 1px solid #1e293b !important; border-radius: 6px !important; color: #fff !important; outline: none !important; box-sizing: border-box !important;">
                </div>
                <div>
                    <label style="display: block !important; font-size: 0.85rem !important; color: #94a3b8 !important; margin-bottom: 4px !important;">Message</label>
                    <textarea name="c_message" rows="4" placeholder="How can we help you?" required style="width: 100% !important; padding: 10px !important; background: #0f172a !important; border: 1px solid #1e293b !important; border-radius: 6px !important; color: #fff !important; outline: none !important; resize: vertical !important; box-sizing: border-box !important;"></textarea>
                </div>
                <button type="submit" style="background: #f97316 !important; color: #fff !important; border: none !important; padding: 12px !important; border-radius: 6px !important; font-weight: bold !important; cursor: pointer !important;">Send Message</button>
            </form>
        </div>
        <div style="display: flex !important; flex-direction: column !important; gap: 1.5rem !important;">
            <div style="background: #111827 !important; padding: 1.8rem !important; border: 1px solid #1e293b !important; border-radius: 10px !important;">
                <h3 style="color: #fff !important; font-size: 1.25rem !important; margin-top: 0 !important; margin-bottom: 0.75rem !important; font-weight: 700 !important;">Direct Communication</h3>
                <p style="color: #94a3b8 !important; font-size: 0.95rem !important; margin-bottom: 1.2rem !important; line-height: 1.6 !important;">For partnerships, broadcasting inquiries, or copyright matters, you can email our administrative desk directly.</p>
                <a href="mailto:support@liveradiostream.net" style="display: inline-flex !important; align-items: center !important; gap: 8px !important; background: #1e293b !important; color: #38bdf8 !important; padding: 12px 16px !important; border-radius: 6px !important; text-decoration: none !important; font-size: 0.95rem !important; font-weight: 600 !important;">
                    ✉ support@liveradiostream.net
                </a>
            </div>
        </div>
    </div>

</div>
<?php
get_footer();