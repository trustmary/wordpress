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
     * Method for testing API key. Returns organization ID on successful test.
     *
     * @param string $key
     * @return string
     */
    public static function test_apikey($key)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$api_url . self::$endpoint_test);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Apikey ' . $key
        ));

        $return = curl_exec($ch);
        curl_close($ch);

        if (!$return)
            return;

        $json = json_decode($return);

        if (!$json || !isset($json->organization_id))
            return;

        return $json->organization_id;
    }
}
