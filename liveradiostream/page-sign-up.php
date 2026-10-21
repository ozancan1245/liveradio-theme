<?php
/* Template Name: Custom Sign Up */
get_header();
$error   = get_transient('custom_auth_error_' . $_SERVER['REMOTE_ADDR']);
$success = get_transient('custom_auth_success_' . $_SERVER['REMOTE_ADDR']);
delete_transient('custom_auth_error_' . $_SERVER['REMOTE_ADDR']);
delete_transient('custom_auth_success_' . $_SERVER['REMOTE_ADDR']);
?>

<div class="container" style="display: flex; justify-content: center; align-items: center; min-height: 70vh; padding: 40px 20px;">
    <div style="background: var(--bg-card, #1e293b); border: 1px solid var(--border-light, #334155); padding: 30px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
        <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.5rem; text-align: center;">Create an Account</h2>

        <?php if ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #fca5a5; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span><?php echo esc_html($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #34d399; padding: 20px; border-radius: 8px; margin-bottom: 15px; text-align: center;">
                <svg style="width: 32px; height: 32px; margin: 0 auto 10px auto; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p style="margin: 0 0 15px 0; font-weight: 600; font-size: 0.95rem; color: #fff;"><?php echo esc_html($success); ?></p>
                <a href="<?php echo home_url('/sign-in/'); ?>" style="display: inline-block; background: var(--primary, #f97316); color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 0.9rem; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);">Sign In Now</a>
            </div>
        <?php else: ?>

            <form action="" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                <?php wp_nonce_field('custom_register_action', 'custom_register_nonce'); ?>
                
                <div>
                    <label style="color: #94a3b8; font-size: 0.85rem; display: block; margin-bottom: 5px;">Username</label>
                    <input type="text" name="reg_username" required style="width: 100%; padding: 10px; background: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #fff; font-size: 0.9rem;">
                </div>

                <div>
                    <label style="color: #94a3b8; font-size: 0.85rem; display: block; margin-bottom: 5px;">Email Address</label>
                    <input type="email" name="reg_email" required style="width: 100%; padding: 10px; background: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #fff; font-size: 0.9rem;">
                </div>

                <div>
                    <label style="color: #94a3b8; font-size: 0.85rem; display: block; margin-bottom: 5px;">Password</label>
                    <input type="password" name="reg_password" required style="width: 100%; padding: 10px; background: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #fff; font-size: 0.9rem;">
                </div>

                <div>
                    <label style="color: #94a3b8; font-size: 0.85rem; display: block; margin-bottom: 5px;">Confirm Password</label>
                    <input type="password" name="reg_password_confirm" required style="width: 100%; padding: 10px; background: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #fff; font-size: 0.9rem;">
                </div>

                <button type="submit" style="background: var(--primary, #f97316); color: #fff; border: none; padding: 12px; border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 0.95rem; margin-top: 10px; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);">
                    Sign Up
                </button>
            </form>

            <p style="text-align: center; margin-top: 20px; color: #94a3b8; font-size: 0.85rem;">
                Already have an account? <a href="<?php echo home_url('/sign-in/'); ?>" style="color: var(--primary, #f97316); text-decoration: none; font-weight: 600;">Sign In</a>
            </p>

        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>