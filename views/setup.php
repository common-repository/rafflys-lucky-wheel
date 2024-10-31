<?php
// Make sure we don't expose any info if called directly
if ( ! defined( 'ABSPATH' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
?>

<div id="rafflys-plugin-container">
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Content -->
    <div class="rafflys-lower">

        <div class="rafflys-boxes">

            <!-- Setup -->
            <div class="rafflys-card">
                <div class="centered rafflys-box-header">
                    <h2><?php echo esc_html_e('Create Spin the Wheel promotion in minutes', 'rafflys'); ?></h2>
                </div>
                <div class="rafflys-setup-instructions">
                    <p><?php echo esc_html_e('Set up your Rafflys account to active your first promotion', 'rafflys'); ?></p>
                    <a href="<?php echo esc_url($data['app_url'] . $data['register_url']); ?>" class="rafflys-button rafflys-is-primary">
                        <?php echo esc_html_e('Set up your Rafflys account', 'rafflys'); ?>
                    </a>
                </div>
            </div>

            <!-- Enter API Key -->
            <div class="rafflys-card">
                <div class="rafflys-enter-api-key-box centered">
                    <a href="javascript:void(0);" class="rafflys-js-connect-with-api-key">
                        <?php echo esc_html_e('Already have an account? Enter your API key', 'rafflys'); ?>
                    </a>
                    <div class="enter-api-key" style="display: none;">
                        <h3><?php echo esc_html_e('Enter your API Key', 'rafflys'); ?></h3>
                        <p class="text-muted">
                            <?php echo esc_html_e('You can find your API Key on your account settings or on your promotion dashboard, in the "Share" tab.', 'rafflys'); ?>
                        </p>
                        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="display: flex; justify-content: center; margin-top: 20px;">
                            <input type="text" name="api_key" id="api_key" value="" placeholder="API key">
                            <input type="hidden" name="action" value="add_api_key">
                            <input type="hidden" name="nonce" value="<?php echo esc_html($data['nonce']); ?>">
                            <button type="submit" class="rafflys-button rafflys-is-primary" style="margin-left: 10px;">
                                <?php echo esc_html_e('Connect', 'rafflys'); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
