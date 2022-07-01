<?php

/**
 * Plugin Name: Trustmary Widgets
 * Plugin URI: https://trustmary.com/
 * Description: 
 * Version:     1.0.0
 * Author:      Samu Aaltonen
 * Author URI:
 * License:
 * License URI:
 */

/**
 * Prevent direct access to this file
 */
defined('ABSPATH') or die('No');

/**
 * Main class for the plugin
 */
class Trustmary_Widgets
{
    /**
     * Identifier for config array to be stored in WP options
     *
     * @var string
     */
    private $_config_idenfifier = 'trustmary_widgets_config';

    /**
     * An array of plugin configuration
     *
     * @var array
     */
    private $_config;

    /**
     * Domain for translations
     *
     * @var string
     */
    public static $translate_domain = 'trustmary-widgets';

    /**
     * Constructor
     * 
     * Includes plugin files and adds main hooks.
     */
    public function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        require_once plugin_dir_path(__FILE__) . 'includes/helper.php';
        require_once plugin_dir_path(__FILE__) . 'includes/connect.php';
        require_once plugin_dir_path(__FILE__) . 'includes/settings.php';

        $this->_config = get_option($this->_config_idenfifier);
        if (!$this->_config)
            $this->_config = array();

        new Trustmary_Settings($this->_config_idenfifier, $this->_config);
    }

    /**
     * Checks if necessary functions are available in current PHP build. If not,
     * prevents the activation.
     *
     * @return void
     */
    public function activate()
    {
        if (!function_exists('openssl_encrypt')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die('Please upgrade PHP. This plugin requires openssl, which is available from PHP 5.3+.', 'Plugin dependency', array('back_link' => true));
        }
        if (!defined('SECURE_AUTH_KEY') || SECURE_AUTH_KEY === '' || !defined('SECURE_AUTH_SALT') || SECURE_AUTH_SALT === '') {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die('SECURE_AUTH_KEY and SECURE_AUTH_SALT cannot be empty. See wp-config.php for more information.', 'Plugin dependency', array('back_link' => true));
        }
    }

    /**
     * Clears plugin config on plugin deactivation.
     *
     * @return void
     */
    public function deactivate()
    {
        delete_option($this->_config_idenfifier);
    }
}

new Trustmary_Widgets();
