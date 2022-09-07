<?php
defined('ABSPATH') or die('No');

/**
 * A static class for API requests in the plugin.
 */
class Trustmary_Connect
{
    /**
     * API URL
     *
     * @var string
     */
    public static $api_url = 'https://api.trustmary.io/v1/';

    /**
     * Endpoint for testing API key
     *
     * @var string
     */
    public static $endpoint_test = 'test';

    /**
     * Endpoint for widgets
     *
     * @var string
     */
    public static $endpoint_widgets = 'widgets';

    /**
     * Endpoint for experiments
     *
     * @var string
     */
    public static $endpoint_experiments = 'experiments';

    /**
     * Method for testing API key. Returns an array consisting organization ID 
     * and organization name on successful test.
     *
     * @param string $key
     * @return string
     */
    public static function test_apikey($key)
    {
        $response = wp_remote_get(
            self::$api_url . self::$endpoint_test,
            array(
                'timeout' => 30,
                'headers' => array(
                    'Authorization' => 'Apikey ' . $key
                )
            )
        );

        if (is_wp_error($response))
            return;

        $json = json_decode(wp_remote_retrieve_body($response));

        if (!$json || !isset($json->organization_id) || !isset($json->organization_name))
            return;

        return array(
            'organization_id' => $json->organization_id,
            'organization_name' => $json->organization_name
        );
    }

    /**
     * Returns inline widgets from API
     *
     * @param string $key
     * @return array
     */
    public static function fetch_inline_widgets($key)
    {
        $response = wp_remote_get(
            self::$api_url . self::$endpoint_widgets,
            array(
                'timeout' => 30,
                'headers' => array(
                    'Authorization' => 'Apikey ' . $key
                )
            )
        );

        if (is_wp_error($response))
            return;

        $json = json_decode(wp_remote_retrieve_body($response));

        if (!$json || !isset($json->widgets))
            return array();

        $widgets = array();

        foreach ($json->widgets as $widget) {
            if (isset($widget->template_type) && $widget->template_type === 'inpage')
                $widgets[] = array(
                    'id' => $widget->id,
                    'name' => $widget->name,
                    'shortcode' => '[trustmary_widget id=&quot;' . $widget->id . '&quot;]',
                );
        }

        return $widgets;
    }

    /**
     * Returns experiments from API
     *
     * @param string $key
     * @return array
     */
    public static function fetch_experiments($key)
    {
        $response = wp_remote_get(
            self::$api_url . self::$endpoint_experiments,
            array(
                'timeout' => 30,
                'headers' => array(
                    'Authorization' => 'Apikey ' . $key
                )
            )
        );

        if (is_wp_error($response))
            return;

        $json = json_decode(wp_remote_retrieve_body($response));

        if (!$json || !isset($json->experiments))
            return array();

        $experiments = array();

        foreach ($json->experiments as $experiment) {
            $experiments[] = array(
                'id' => $experiment->id,
                'name' => $experiment->name,
                'shortcode' => '[trustmary_experiment id=&quot;' . $experiment->id . '&quot;]',
            );
        }

        return $experiments;
    }
}
