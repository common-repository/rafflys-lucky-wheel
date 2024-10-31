<?php

class Rafflys_Admin {

    private static $initiated = false;

    public static function init() {
        if (!self::$initiated) {
            self::init_plugin_options();
        }
    }

    public static function init_plugin_options() {
        // if (current_user_can('activate_plugins')) {
            add_action('admin_init', array('Rafflys_Admin', 'handle_form'));
            add_action('admin_menu', array('Rafflys_Admin', 'rafflys_admin_add_sidebar_menu'));
            add_filter('plugin_action_links_' . RAFFLYS__PLUGIN_BASENAME, array('Rafflys_Admin', 'rafflys_add_actions_links'));
            add_action('admin_footer', array('Rafflys_Admin', 'rafflys_admin_add_custom_class'));
            add_action('admin_enqueue_scripts', array('Rafflys_Admin', 'rafflys_admin_enqueue_scripts'));
            add_filter('allowed_redirect_hosts', array('Rafflys_Admin', 'allowed_redirect_hosts'));

            add_action('wp_ajax_rafflys_logout', array('Rafflys_Admin', 'rafflys_exec_logout'));
            add_action('wp_ajax_rafflys_promotion_status', array('Rafflys_Admin', 'rafflys_promotion_status'));
            add_action('admin_post_add_api_key', array('Rafflys_Admin', 'add_api_key'));
            add_action('admin_post_rafflys_update_settings', array('Rafflys_Admin', 'rafflys_update_settings'));
        // }
    }

    public static function allowed_redirect_hosts($hosts) {
        $hosts[] = RAFFLYS_APP_HOST;
        return $hosts;
    }

    public static function admin_plugin_settings_link($links) {
        $settings_link = '<a href="' . esc_url(self::get_page_url()) . '">' . __('Settings', 'personizely') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }


    public static function plugin_settings() {
        add_menu_page('Personizely Settings', 'Personizely', 'manage_options', 'personizely', array('Personizely_Admin', 'plugin_settings_view'), PERSONIZELY__PLUGIN_URL . '/assets/img/icon.png');
    }

    public static function validate_api_key($api_key) {
        return strlen($api_key) === 10 && preg_match('/^[a-z0-9]+$/', $api_key);
    }

    public static function sanitize_api_key($api_key) {
        return sanitize_key($api_key);
    }

    public static function plugin_settings_view() {
        global $wp;

        $api_key = Rafflys::get_api_key();

        // var_dump($api_key); die;

        if ($api_key) {

            $user_data = Rafflys_API::get_user($api_key);
            $user_promotions = Rafflys_API::get_user_promotions($user_data['id']);
            $pages = get_pages();
            $config = unserialize(get_option('rafflys_config'));
            $wp_site_url = get_admin_url() . 'admin.php?page=rafflys';
            $nonce = wp_create_nonce('rafflys_settings_save');

            $data = array(
                'app_url' => RAFFLYS_APP_URL,
                'wp_site' => $wp_site_url,
                'nonce' => $nonce,
                'create_url' => '/promotions/start/fortune-wheel?wp_site=' . urlencode($wp_site_url),
            );

            include RAFFLYS__PLUGIN_DIR . '/views/settings.php';
            return true;
        }


        $nonce = wp_create_nonce('rafflys_api_key_save');

        $params = [
            'site' => home_url(add_query_arg(array(), $wp->request)),
            'initial' => 1,
            'nonce' => $nonce
        ];

        $info = [
            'email' => get_option('admin_email'),
            'name' => get_option('blogname'),
            'domain' => parse_url(get_option('siteurl'))['host']
        ];

        $connectUrl = '/connect/wordpress?'. http_build_query($params);

        $data = array(
            'app_url' => RAFFLYS_APP_URL,
            'connect_url' => $connectUrl,
            'nonce' => $nonce,
            'register_url' => '/' . RAFFLYS_USER_LANG . '/signup?next='. urlencode($connectUrl) . '&' . http_build_query($info),
        );

        include RAFFLYS__PLUGIN_DIR . '/views/setup.php';
    }

    public static function handle_form() {

        $api_key = null;
        $wp_nonce = null;

        if (isset($_GET['nonce'])) {
            $wp_nonce = sanitize_text_field( wp_unslash($_GET['nonce']) );
        }

        if (isset($_GET['api_key'])) {
            $api_key = sanitize_key($_GET['api_key']);
        }

        if ($api_key && $wp_nonce) {
            if (wp_verify_nonce($wp_nonce, 'rafflys_api_key_save')) {
                self::__update_api_key($api_key);
                wp_redirect(admin_url('admin.php?page=rafflys&api_key_added=1'));
            } else {
                wp_redirect(admin_url('admin.php?page=rafflys&api_key_added_error=1'));
            }
        }
    }

    public static function get_page_url() {

        $args = array('page' => 'personizely');

        $url = add_query_arg($args, admin_url('admin.php'));

        return $url;
    }

    public static function log($personizely_debug) {
        if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG)
            error_log(print_r(compact('personizely_debug'), 1)); // send message to debug.log when in debug mode
    }

    public static function rafflys_admin_add_sidebar_menu() {
        // add_menu_page('Personizely Settings', 'Personizely',
        // 'manage_options', 'personizely', array('Personizely_Admin', 'plugin_settings_view'),
        // PERSONIZELY__PLUGIN_URL . '/assets/img/icon.png');

        add_menu_page(
            'Rafflys by AppSorteos',    // Título de la página
            'Rafflys',                  // Título del menú
            'manage_options',           // Capacidad requerida para acceder
            'rafflys',       // Slug de la página
            array('Rafflys_Admin', 'plugin_settings_view'),    // Función que mostrará la página
            RAFFLYS__PLUGIN_URL . '/assets/icon.png',
            99                          // Posición del menú
        );
    }

    public static function rafflys_add_actions_links($links) {
        $plugin_basename = plugin_basename(__FILE__);
        $plugin_links = array(
            '<a href="' . esc_url(admin_url('admin.php?page=rafflys')) . '">' . esc_html__('Settings', 'rafflys') . '</a>',
        );
        return array_merge($plugin_links, $links);
    }

    public static function rafflys_admin_add_custom_class() {
        echo '<script>';
        echo 'document.addEventListener("DOMContentLoaded", function() {';
        echo '    document.body.classList.add("settings_page_rafflys-key-config");';
        echo '});';
        echo '</script>';
    }

    public static function rafflys_admin_enqueue_scripts() {
        $plugin_data = get_plugin_data(__FILE__);
        $version = $plugin_data['Version'];
        $version = time();
        wp_enqueue_style('mi-plugin-styles-1', plugins_url('css/rafflys.css', __FILE__), array(), $version);
        wp_enqueue_style('mi-plugin-styles-2', plugins_url('css/rafflys-admin.css', __FILE__), array(), $version);
        wp_enqueue_script('mi-plugin-script', plugins_url('js/rafflys-admin.js', __FILE__), array('jquery'), $version);
        wp_enqueue_script('jquery', array(), $version);
    }

    public static function rafflys_exec_logout() {

        update_option('rafflys_api_key', NULL);
        update_option('rafflys_config', NULL);

        $response = array(
            'status' => 'success',
            'message' => 'Logout successfully',
            'data' => array(
                'foo' => 'bar',
            )
        );

        // Enviar la respuesta JSON
        wp_send_json_success($response);
    }

    public static function rafflys_promotion_status() {
        if ($_POST) {

            if (isset($_POST['id']) && isset($_POST['is_active'])) {
                if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'rafflys_settings_save')) {
                    $config = unserialize(get_option('rafflys_config', NULL));
                    $id = sanitize_text_field($_POST['id']);
                    $is_active = (int) sanitize_text_field($_POST['is_active']);

                    if (!$is_active && isset($config[$id])) {
                        unset($config[$id]);
                        update_option('rafflys_config', serialize($config));
                    }

                    $response = array(
                        'status' => 'success',
                        'message' => 'Disable successfully',
                        'data' => array(
                            'id' => $id,
                        )
                    );

                    wp_send_json_success($response);
                }
            }
        }

        wp_send_json_success(array(
            'status' => 'error',
            'error' => 'Something went wrong, please try again.',
        ));
    }

    public static function add_api_key() {

        if (isset($_POST['api_key']) && isset($_POST['nonce'])) {

            $wp_nonce = sanitize_text_field( wp_unslash($_POST['nonce']) );

            if (wp_verify_nonce($wp_nonce, 'rafflys_api_key_save')) {
                $api_key = sanitize_text_field($_POST['api_key']);
                self::__update_api_key($api_key);
            }
        }

        wp_redirect(admin_url('admin.php?page=rafflys&api_key_added=1'));
    }

    private static function __update_api_key ($api_key) {
        $user_data = Rafflys_API::get_user($api_key);

        if ($user_data) {
            update_option('rafflys_api_key', $api_key);
            return true;
        }

        return false;
    }

    public static function rafflys_update_settings() {

        if (isset($_POST['rafflys_promotion_id'])
            && isset($_POST['nonce'])
            && wp_verify_nonce($_POST['nonce'], 'rafflys_settings_save')) {

            $current_config = unserialize(get_option('rafflys_config'));

            if (!is_array($current_config)) {
                $current_config = array();
            }

            $display = sanitize_text_field($_POST['rafflys_display']);
            $display_url = sanitize_text_field($_POST['rafflys_display_url']);
            $display_page = sanitize_text_field($_POST['rafflys_display_page']);
            $promotion_id = sanitize_text_field($_POST['rafflys_promotion_id']);

            if ($display !== 'page') {
                $display_page = '';
            }

            $config = array(
                'promotion_id' => $promotion_id,
                'display' => $display,
                'display_url' => $display_url,
                'display_page' => $display_page,
                'is_active' => true,
            );

            $current_config[$promotion_id] = $config;

            update_option('rafflys_config', serialize($current_config));

        }

        wp_redirect(admin_url('admin.php?page=rafflys&settings_updated=1'));
    }
}
