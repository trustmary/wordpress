<?php

/**
 * Plugin Name: Trustmary
 * Plugin URI:  https://trustmary.com/
 * Description: Display Trustmary Widgets and Experiments on your site.
 * Version:     1.0.5
 * Tested up to: 6.0
 * Author:      Trustmary
 * Text Domain: trustmary-widgets
 * Domain Path: /languages
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
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
        add_action('plugins_loaded', array($this, 'init_textdomain'));

        new Trustmary_Settings($this->_config_idenfifier, $this->_config);
        new Trustmary_Shortcodes();
        $this->_pages = new Trustmary_Pages($this->_config);
    }

    /**
     * Checks if necessary functions are available in current PHP build. If not,
     * prevents the activation.
     * 
     * Also checks if SECURE_AUTH_KEY and SECURE_AUTH_SALT have been set 
     * (should be on all up-to-date WP installations).
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
     * Removes plugin configuration on plugin deactivation.
     *
     * @return void
     */
    public function deactivate()
    {
        delete_option($this->_config_idenfifier);
    }

    /**
     * Loads plugin textdomain for translations
     *
     * @return void
     */
    public function init_textdomain()
    {
        load_plugin_textdomain('trustmary-widgets', false, basename(dirname(__FILE__)) . '/languages/');
    }

    /**
     * Creates admin menu links and pages
     *
     * @return void
     */
    public function admin_pages()
    {
        add_menu_page('Trustmary', 'Trustmary', 'manage_options', 'trustmary-dashboard', array($this->_pages, 'dashboard'), plugins_url('/assets/images/logo-icon.svg', __FILE__), 30);
        add_submenu_page('trustmary-dashboard', esc_attr__('Dashboard', 'trustmary-widgets'),  esc_attr__('Dashboard', 'trustmary-widgets'), 'manage_options', 'trustmary-dashboard', array($this->_pages, 'dashboard'));
        add_submenu_page('trustmary-dashboard', esc_attr__('Popups', 'trustmary-widgets'),  esc_attr__('Popups', 'trustmary-widgets'), 'manage_options', 'trustmary-popups', array($this->_pages, 'popups'));
        add_submenu_page('trustmary-dashboard', esc_attr__('Inline widgets', 'trustmary-widgets'),  esc_attr__('Inline widgets', 'trustmary-widgets'), 'manage_options', 'trustmary-inline', array($this->_pages, 'inline'));
        add_submenu_page('trustmary-dashboard', esc_attr__('Experiments', 'trustmary-widgets'),  esc_attr__('Experiments', 'trustmary-widgets'), 'manage_options', 'trustmary-experiments', array($this->_pages, 'experiments'));
        add_submenu_page('trustmary-dashboard', esc_attr__('Gather reviews', 'trustmary-widgets'),  esc_attr__('Gather reviews', 'trustmary-widgets'), 'manage_options', 'trustmary-reviews', array($this->_pages, 'reviews'));
    }

    /**
     * Adds admin css
     *
     * @return void
     */
    public function admin_styles()
    {
        wp_enqueue_style('trustmary-admin-styles', plugins_url('/assets/css/admin.css', __FILE__));
        wp_enqueue_script('trustmary-admin-scripts', plugins_url('/assets/js/admin.js', __FILE__), array(), false, true);
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

        ?><script>(function (w,d,s,o,r,js,fjs) {
w[r]=w[r]||function() {(w[r].q = w[r].q || []).push(arguments)}
w[r]('app', '<?php echo esc_attr($this->_config['organization_id']); ?>');
if(d.getElementById(o)) return;
js = d.createElement(s), fjs = d.getElementsByTagName(s)[0];
js.id = o; js.src = 'https://embed.trustmary.com/embed.js';
js.async = 1; fjs.parentNode.insertBefore(js, fjs);
}(window, document, 'script', 'trustmary-embed', 'tmary'));
</script><?php
    }
}

new Trustmary_Widgets();
