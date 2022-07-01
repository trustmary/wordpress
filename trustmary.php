<?php

/**
 * Plugin Name: Trustmary
 * Plugin URI: https://trustmary.com/
 * Description: Display Trustmary Widgets and Experiments on your site.
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
     * An object for admin pages
     *
     * @var Trustmary_Pages
     */
    private $_pages;

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
        require_once plugin_dir_path(__FILE__) . 'includes/pages.php';
        require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';

        $this->_config = get_option($this->_config_idenfifier);
        if (!$this->_config)
            $this->_config = array();

        add_action('wp_head', array($this, 'add_scripts'));
        add_action('admin_menu', array($this, 'admin_pages'));
        add_action('admin_enqueue_scripts', array($this, 'admin_styles'));

        new Trustmary_Settings($this->_config_idenfifier, $this->_config);
        new Trustmary_Shortcodes();
        $this->_pages = new Trustmary_Pages($this->_config);
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

    /**
     * Creates admin menu links and pages
     *
     * @return void
     */
    public function admin_pages()
    {
        add_menu_page('Trustmary', 'Trustmary', 'manage_options', 'trustmary-dashboard', array($this->_pages, 'dashboard'), plugins_url('/assets/images/logo-icon.svg', __FILE__), 30);
        add_submenu_page('trustmary-dashboard', __('Dashboard', Trustmary_Widgets::$translate_domain),  __('Dashboard', Trustmary_Widgets::$translate_domain), 'manage_options', 'trustmary-dashboard', array($this->_pages, 'dashboard'));
        add_submenu_page('trustmary-dashboard', __('Popups', Trustmary_Widgets::$translate_domain),  __('Popups', Trustmary_Widgets::$translate_domain), 'manage_options', 'trustmary-popups', array($this->_pages, 'popups'));
        add_submenu_page('trustmary-dashboard', __('Inline widgets', Trustmary_Widgets::$translate_domain),  __('Inline widgets', Trustmary_Widgets::$translate_domain), 'manage_options', 'trustmary-inline', array($this->_pages, 'inline'));
        add_submenu_page('trustmary-dashboard', __('Experiments', Trustmary_Widgets::$translate_domain),  __('Experiments', Trustmary_Widgets::$translate_domain), 'manage_options', 'trustmary-experiments', array($this->_pages, 'experiments'));
        add_submenu_page('trustmary-dashboard', __('Gather reviews', Trustmary_Widgets::$translate_domain),  __('Gather reviews', Trustmary_Widgets::$translate_domain), 'manage_options', 'trustmary-reviews', array($this->_pages, 'reviews'));
    }

    /**
     * Adds admin css
     *
     * @return void
     */
    public function admin_styles()
    {
        wp_enqueue_style('admin-styles', plugins_url('/assets/css/admin.css', __FILE__));
    }

    /**
     * Inserts javascript to WP head if organization ID has been set and add_scripts setting is on.
     *
     * @return void
     */
    public function add_scripts()
    {
        if (!isset($this->_config['organization_id']) || (isset($this->_config['add_scripts']) && !$this->_config['add_scripts']))
            return;

        echo "<script>(function (w,d,s,o,r,js,fjs) {
w[r]=w[r]||function() {(w[r].q = w[r].q || []).push(arguments)}
w[r]('app', '" . $this->_config['organization_id'] . "');
if(d.getElementById(o)) return;
js = d.createElement(s), fjs = d.getElementsByTagName(s)[0];
js.id = o; js.src = 'https://embed.trustmary.com/embed.js';
js.async = 1; fjs.parentNode.insertBefore(js, fjs);
}(window, document, 'script', 'trustmary-embed', 'tmary'));
</script>";
    }
}

new Trustmary_Widgets();
