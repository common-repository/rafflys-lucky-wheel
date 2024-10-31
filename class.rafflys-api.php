<?php

// Make sure we don't expose any info if called directly
if ( ! defined( 'ABSPATH' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

class Rafflys_API {

    private static $API_URL = false;

    private static function build_url ($path, $params) {
        if (isset($params['api_key'])) {
            $api_key = $params['api_key'];
        } else {
            $api_key = Rafflys::get_api_key();
        }
        return RAFFLYS_API_URL . $path . '?api_key=' . urlencode($api_key);
    }

    private static function call_api_get ($path, $params=array()) {
        return self::call_api('GET', $path, $params);
    }

    private static function call_api ($method='GET', $path, $params=array()) {
        $url = self::build_url($path, $params);

        if ($method === 'GET') {
            $response = wp_remote_get($url);
        } else {
            $response = wp_remote_post($url, $params);
        }

        // Check if the request was successful
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            // Get the response body data
            $body = wp_remote_retrieve_body($response);

            // Decode the response data as JSON
            $response = json_decode($body, true);
            $data = $response['data'];

            return $data;
        } else {
            return null;
        }
    }

    public static function get_user ($api_key) {
        $response = self::call_api_get('/users/me', array('api_key' => $api_key));
        return $response;
    }

    public static function get_user_promotions ($user_id) {
        $response = self::call_api_get('/users/' . $user_id . '/promotions');
        return $response;
    }
}