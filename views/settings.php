<?php

    // Make sure we don't expose any info if called directly
    if ( ! defined( 'ABSPATH' ) ) {
        echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
        exit;
    }

    function rafflys_is_config ($key, $value) {
        global $config;

        if ( isset($config[$key]) && $config[$key] == $value ) {
            return true;
        }

        return false;
    }


    function rafflys_get_page_title_from_id ($pages=array(), $page_id) {

        foreach ($pages as $page) {
            if ($page->ID == $page_id) {
                return $page->post_title;
            }
        }

        return '';
    }
?>


<div id="rafflys-plugin-container">
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Content -->
    <div class="rafflys-lower">

        <div class="rafflys-boxes">

            <!-- No Promotions -->
            <?php if (!count($user_promotions)) {?>
            <div class="rafflys-card">
                <div class="centered rafflys-box-header rafflys-text-center">
                    <h2 style="margin-top: 0;"><?php echo esc_html_e('Create Spin the Wheel promotion in minutes', 'rafflys') ?></h2>
                </div>
                <div class="rafflys-setup-instructions rafflys-text-center">
                    <p><?php echo esc_html_e('Create your first Spin the Wheel promotion', 'rafflys') ?></p>
                    <a href="<?php echo esc_url( $data['app_url'] . $data['create_url'] ) ?>" class="rafflys-button rafflys-is-primary">
                        <?php echo esc_html_e('Start now, it\'s free', 'rafflys') ?>
                    </a>
                </div>
            </div>
            <?php } ?>

            <!-- Promotions -->
            <?php if (count($user_promotions)) {?>
            <div id="promotions-list">
                <div class="rafflys-card">
                    <div class="rafflys-section-header">
                        <div>
                            <h2 class="rafflys-section-header__label">
                                <span><?php echo esc_html_e('Your Fortune Wheels', 'rafflys') ?></span>
                            </h2>
                            <span class="rafflys-section-header__info">
                                <?php echo esc_html_e('Choose the promotion you want to display on this site', 'rafflys') ?>
                            </span>
                        </div>
                        <a href="<?php echo esc_url($data['app_url'] . $data['create_url']); ?>" class="rafflys-button rafflys-is-primary">
                            <?php echo esc_html_e('Create', 'rafflys'); ?>
                        </a>
                    </div>
                    <div class="inside">
                        <div>
                        <?php
                            foreach ($user_promotions as $promotion):
                                $promotion_config = isset($config[$promotion['hash']]) ? $config[$promotion['hash']] : false;
                                $is_active = $promotion_config;
                        ?>
                            <div class="promo-item">
                                <h3>
                                    <?php echo esc_html($promotion['name']); ?>

                                    <?php if ($is_active) { ?>
                                        <span class="badge badge-success">
                                            <small style="font-weight: 400; margin-left: 10px">
                                                <?php echo esc_html_e('Active', 'rafflys'); ?> (<?php echo esc_html($promotion_config['display']); ?>
                                                <?php if ($promotion_config['display'] === 'page') {
                                                    echo esc_html( rafflys_get_page_title_from_id($pages, $promotion_config['display_page']) );
                                                } ?>
                                            )
                                            </small>
                                        </span>
                                    <?php }?>
                                </h3>

                                <div class="promot-item__actions">
                                    <div>

                                        <a href="<?php echo esc_url('https://app-sorteos.com/promotions/setup/' . $promotion['id'] . '?wp_site=' . urlencode($wp_site_url) . '#design' ); ?>">
                                            <?php echo esc_html_e('Edit', 'rafflys'); ?>
                                        </a>

                                        <span style="padding:0 4px; opacity: .4">|</span>

                                        <a href="<?php echo esc_url('https://app-sorteos.com/promotions/setup/' . $promotion['id'] . '?wp_site=' . urlencode($wp_site_url) . '#results' ); ?>">
                                            <?php echo esc_html_e('View Leads', 'rafflys'); ?>
                                        </a>

                                        <!-- <span style="padding:0 4px; opacity: .4">|</span> -->
                                    </div>
                                    <div>
                                        <?php if ($is_active) { ?>
                                            <a href="javascript:void(0)" class="js-disable-prom" data-id="<?php echo esc_html($promotion['hash']); ?>">
                                                <?php echo esc_html_e('Deactivate', 'rafflys'); ?>
                                            </a>
                                        <?php } else { ?>
                                            <a href="javascript:void(0)" class="js-enable-prom" data-id="<?php echo esc_html($promotion['hash']); ?>"><?php echo esc_html_e('Activate', 'rafflys'); ?></a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div style="text-align: center; margin: 20px">
                    <a class="submitdelete deletion" href="#" id="js-rafflys-logout">
                        Disconnect this account
                    </a>

                    <span style="padding:0 4px; opacity: .4">|</span>

                    <a href="https://app-sorteos.com/" target="_blank">
                        Go to Rafflys Dashboard
                    </a>
                </div>
            </div>

            <?php } ?>

            <!-- Settings -->
            <div class="rafflys-card" id="promotion-config" style="display: none;">
                <div class="rafflys-section-header">
                    <h2 class="rafflys-section-header__label">
                        <span>Settings</span>
                    </h2>
                </div>

                <div class="inside">
                    <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" autocomplete="off" method="POST" id="rafflys-settings-form">

                        <div class="rafflys-settings">

                            <!-- Display Options -->
                            <div class="rafflys-settings__row is-radio">
                                <div class="rafflys-settings__row-text">
                                    <h3 class="rafflys-settings__row-title">Display Options</h3>
                                </div>
                                <div class="rafflys-settings__row-input">
                                    <fieldset>
                                        <legend class="screen-reader-text">
                                            <span>rafflys anti-spam strictness</span>
                                        </legend>
                                        <div>
                                            <label class="rafflys-settings__row-input-label" for="rafflys_display_1">
                                                <input type="radio"
                                                    name="rafflys_display"
                                                    id="rafflys_display_1"
                                                    value="everywhere"
                                                    <?php echo (rafflys_is_config('display', 'everywhere')) ? 'checked="checked"' : ''; ?>>
                                                <span class="rafflys-settings__row-label-text">
                                                    Everywhere
                                                </span>
                                            </label>
                                        </div>
                                        <div>
                                            <label class="rafflys-settings__row-input-label" for="rafflys_display_6">
                                                <input type="radio"
                                                    name="rafflys_display"
                                                    id="rafflys_display_6"
                                                    value="homepage"
                                                    <?php echo rafflys_is_config('display', 'homepage') ? 'checked="checked"' : ''; ?>>
                                                <span class="rafflys-settings__row-label-text">
                                                    Homepage
                                                </span>
                                            </label>
                                        </div>
                                        <div>
                                            <label class="rafflys-settings__row-input-label" for="rafflys_display_0">
                                                <input
                                                    type="radio"
                                                    name="rafflys_display"
                                                    id="rafflys_display_0"
                                                    value="all_posts"
                                                    <?php echo rafflys_is_config('display', 'all_posts') ? 'checked="checked"' : ''; ?>>
                                                <span class="rafflys-settings__row-label-text">
                                                    All blog posts
                                                </span>
                                            </label>
                                        </div>
                                        <div>
                                            <label class="rafflys-settings__row-input-label" for="rafflys_display_2">
                                                <input
                                                    type="radio"
                                                    name="rafflys_display"
                                                    id="rafflys_display_2"
                                                    value="all_pages"
                                                    <?php echo rafflys_is_config('display', 'all_pages') ? 'checked="checked"' : ''; ?>>
                                                <span class="rafflys-settings__row-label-text">
                                                    All pages
                                                </span>
                                            </label>
                                        </div>
                                        <div class="rafflys-settings__row-flex">
                                            <label class="rafflys-settings__row-input-label" for="rafflys_display_3">
                                                <input
                                                    type="radio"
                                                    name="rafflys_display"
                                                    id="rafflys_display_3"
                                                    value="page"
                                                    <?php echo rafflys_is_config('display', 'page') ? 'checked="checked"' : ''; ?>>
                                                <span class="rafflys-settings__row-label-text">
                                                    Specific Page
                                                </span>
                                            </label>
                                            <div id="rafflys_display_page" style="display:<?php echo rafflys_is_config('display', 'page') ? 'block' : 'none'; ?>;">
                                                <select name="rafflys_display_page">
                                                    <?php foreach ($pages as $page): ?>
                                                        <option value="<?php echo esc_html($page->ID) ?>" <?php echo rafflys_is_config('display_page', esc_html($page->ID)) ? 'selected="selected"' : ''; ?>><?php echo esc_html($page->post_title) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="rafflys-settings__row-flex">
                                            <label class="rafflys-settings__row-input-label" for="rafflys_display_4">
                                                <input
                                                    type="radio"
                                                    name="rafflys_display"
                                                    id="rafflys_display_4"
                                                    value="url"
                                                    <?php echo rafflys_is_config('display', 'url') ? 'checked="checked"' : ''; ?>>
                                                <span class="rafflys-settings__row-label-text">
                                                    Specific URL
                                                </span>
                                            </label>
                                            <div id="rafflys_display_url" style="display:<?php echo rafflys_is_config('display', 'url') ? 'block' : 'none'; ?>;">
                                                <input type="text" name="rafflys_display_url" placeholder="Enter an URL" value="<?php echo isset($config['display_url']) ? esc_html($config['display_url']) : '' ?>" style="min-width: 400px;">
                                            </div>
                                        </div>
                                        <div id="rafflys_display_url_help" style="display:<?php echo rafflys_is_config('display', 'url') ? 'block' : 'none'; ?>;">
                                            <p class="rafflys-settings__row-note">+ On a specific page: <span class="rafflys-js-domain"></span>/page (just copy from the browser bar)</p>
                                            <p class="rafflys-settings__row-note">+ On a group of pages: <span class="rafflys-js-domain"></span>/category/*</p>
                                            <p class="rafflys-settings__row-note">+ On a product/group page: <span class="rafflys-js-domain"></span>/product/*iphone*</p>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>

                        <div class="rafflys-card-actions">
                            <div id="delete-action">
                            </div>
                            <div class="rafflys-card-buttons">
                                <a style="margin-right: 14px;" href="javascript:void(0)" onclick="Rafflys_Admin.cancel_edit_promotion()">
                                    <?php echo esc_html_e('Cancel', 'rafflys'); ?>
                                </a>

                                <button type="submit" class="rafflys-button rafflys-is-primary">
                                    Save changes
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="rafflys_promotion_id" value="" id="promotion-config-id">
                        <input type="hidden" name="nonce" value="<?php echo esc_html($data['nonce']); ?>">
                        <input type="hidden" name="action" value="rafflys_update_settings">
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .promo-item {
        display: flex;
        justify-content: center;
        border-top: 1px solid rgba(0,0,0,0.1);
        flex-direction: column;
        padding: 16px 16px;
    }

    .promo-item h3, .promo-item h4 {
        margin: 0;
        padding: 0;
        margin-bottom: 10px;
        font-size: 17px !important;
        font-weight: 500;
    }

    .badge-success {
        color: green;
    }
    .badge-inactive {
        color: grey;
    }
    .promot-item__actions {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }
</style>

<script>
    window.rafflys_ajax_nonce = '<?php echo esc_html($data['nonce']); ?>';
</script>