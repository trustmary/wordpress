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
     * Constructor
     * 
     * Includes plugin files and adds main hooks.
     */
    public function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        include(plugin_dir_path(__FILE__) . 'includes/settings.php');
        $this->_config = get_option($this->_config_idenfifier);

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
        if (!defined('AUTH_KEY') || AUTH_KEY === '') {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die('AUTH_KEY cannot be empty. See wp-config.php.', 'Plugin dependency', array('back_link' => true));
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
