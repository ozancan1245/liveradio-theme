<?php
/* Template Name: Custom Sign In */
get_header();
$error   = get_transient('custom_auth_error_' . $_SERVER['REMOTE_ADDR']);
$success = get_transient('custom_auth_success_' . $_SERVER['REMOTE_ADDR']);
delete_transient('custom_auth_error_' . $_SERVER['REMOTE_ADDR']);
delete_transient('custom_auth_success_' . $_SERVER['REMOTE_ADDR']);
?>

<div class="container" style="display: flex; justify-content: center; align-items: center; min-height: 70vh; padding: 40px 20px;">
    <div style="background: var(--bg-card, #1e293b); border: 1px solid var(--border-light, #334155); padding: 30px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
        <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.5rem; text-align: center;">Sign In to LiveRadio</h2>

        <?php if ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #ef4444; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 0.85rem; text-align: center;">
                <?php echo esc_html($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 0.85rem; text-align: center;">
                <?php echo esc_html($success); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
            <?php wp_nonce_field('custom_login_action', 'custom_login_nonce'); ?>
            
            <div>
                <label style="color: #94a3b8; font-size: 0.85rem; display: block; margin-bottom: 5px;">Username or Email</label>
                <input type="text" name="log_username" required style="width: 100%; padding: 10px; background: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #fff; font-size: 0.9rem;">
            </div>

            <div>
                <label style="color: #94a3b8; font-size: 0.85rem; display: block; margin-bottom: 5px;">Password</label>
                <input type="password" name="log_password" required style="width: 100%; padding: 10px; background: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #fff; font-size: 0.9rem;">
            </div>

            <button type="submit" style="background: var(--primary, #f97316); color: #fff; border: none; padding: 12px; border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 0.95rem; margin-top: 10px; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);">
                Sign In
            </button>
        </form>

        <p style="text-align: center; margin-top: 20px; color: #94a3b8; font-size: 0.85rem;">
            Don't have an account? <a href="<?php echo home_url('/sign-up/'); ?>" style="color: var(--primary, #f97316); text-decoration: none; font-weight: 600;">Sign Up</a>
        </p>
    </div>
</div>

<?php get_footer(); ?>