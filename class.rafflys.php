<?php

class Rafflys {

    private static $initiated = false;

    private static $api_key = null;

    public static function init() {
        if (!self::$initiated) {
            self::init_plugin();
        }
    }

    public static function locale_filter () {
        return RAFFLYS_USER_LANG;
    }

    public static function load_translations() {
        load_plugin_textdomain('rafflys', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    public static function init_plugin() {

        if (self::get_api_key() && !is_admin()) {
            add_action('wp_footer', array('Rafflys', 'insert_embed_script'));

            // SiteGround Optimizer
            add_filter('sgo_javascript_combine_excluded_external_paths', array('Rafflys', 'optimization_exclude'));

            // WP Rocket
            add_filter('rocket_minify_excluded_external_js', array('Rafflys', 'optimization_exclude'));

            // Jetpack Boost
            add_filter('jetpack_boost_render_blocking_js_exclude_handles', array('Rafflys', 'optimization_exclude'));

            // WP Meteor
            add_filter('wpmeteor_exclude', function ($exclude, $content) {
                if (str_contains($content, RAFFLYS_APP_HOST)) {
                    return true;
                }

                return $exclude;
            });
        }
    }

    public static function optimization_exclude($exclude_list) {
        $exclude_list[] = RAFFLYS_APP_HOST;

        return $exclude_list;
    }

    // public static function shortcode_widget($arguments) {
    //     $widgetId = $arguments[0];
    //     echo "<div data-ply-embedded-widget='$widgetId'></div>";
    // }

    // public static function shortcode_placeholder($arguments) {
    //     $placeholderId = $arguments[0];
    //     echo "<div data-ply-placeholder='$placeholderId'></div>";
    // }

    // public static function personizely_script() {
    //     $url = (PERSONIZELY_STATIC_URL . '/' . self::get_api_key() . '.js');
    //     echo '<script src="' . $url . '"' . (self::get_async() ? ' async' : '') . ' type="text/javascript"></script>' . PHP_EOL;
    // }

    public static function get_api_key() {
        if (self::$api_key !== null) {
            return self::$api_key;
        }

        self::$api_key = get_option('rafflys_api_key');

        return self::$api_key;
    }

    public static function insert_embed_script () {
        $config = unserialize(get_option('rafflys_config'));

        if(!$config) {
            return false;
        }

        $config = array_values($config)[0];

        $display_method = $config['display'];
        $promotion_id = $config['promotion_id'];
        $display_method_url = $config['display_url'];
        $display_method_page = $config['display_page'];
        $should_include_script = false;

        switch ($display_method) {
            case 'everywhere':
                $should_include_script = true;
                break;
            case 'homepage':
                $current_url = get_permalink();
                $home_url = home_url('/');

                if ($current_url === $home_url || is_front_page()) {
                    $should_include_script = true;
                }
                break;
            case 'all_posts':
                if (is_single()) {
                    $should_include_script = true;
                }
                break;
            case 'all_pages':
                if (is_page()) {
                    $should_include_script = true;
                }
                break;
            case 'page':
                if (is_page($display_method_page)) {
                    $should_include_script = true;
                }
                break;
            case 'url':
                function rafflys_match_url_pattern($current_url, $url_pattern) {
                    $escaped_pattern = preg_quote($url_pattern, '/');
                    $regex_pattern = str_replace('\*', '.*', $escaped_pattern);

                    if (preg_match('/' . $regex_pattern . '/', $current_url)) {
                        return true;
                    } else {
                        return false;
                    }
                }
                $current_url = get_permalink();
                if(rafflys_match_url_pattern($current_url, $display_method_page)) {
                    $should_include_script = true;
                }
                break;
        }

        if ($should_include_script) {
            echo '<script id="rafflys-embed-js" src="' . esc_url('https://app-sorteos.com/embed/embed.js') . '" type="text/javascript" data-promotion="'. esc_attr($promotion_id).'" defer></script>';
        }
    }
}