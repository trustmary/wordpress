<?php
defined('ABSPATH') or die('No');

/**
 * Class that handles displaying admin pages.
 */
class Trustmary_Pages
{
    /**
     * An array of plugin configuration
     *
     * @var array
     */
    private $_config;

    /**
     * Pages class constructor. Gets plugin config as parameter.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->_config = $config;

        add_action('admin_init', array($this, 'check_settings'));
    }

    /**
     * Checks if api has been set successfully before letting user to plugin subpages. 
     * If not, redirects the user to settinsg page.
     *
     * @return void
     */
    public function check_settings()
    {
        $page = isset($_GET['page']) ? $_GET['page'] : '';
        if (!in_array($page, array(
            'trustmary-popups',
            'trustmary-inline',
            'trustmary-experiments',
            'trustmary-reviews'
        )))
            return;

        if (!isset($this->_config['api_key']) || !$this->_config['api_key']) {
            wp_redirect(admin_url('admin.php?page=trustmary-settings'));
            exit;
        }
    }

    /**
     * Function for displaying dashboard page
     *
     * @return void
     */
    public function dashboard()
    {
        echo '<h1>' . esc_attr__('Dashboard', 'trustmary-widgets') . '</h1>';

        if (isset($this->_config['api_key']) && $this->_config['api_key']) {
            echo '<h3>' . esc_attr__('You are good to go!', 'trustmary-widgets') . '</h3>'
                . '<p>' . esc_attr__('You can now create popups, review widgets, lead generation forms and review gathering forms inside Trustmary!', 'trustmary-widgets') . '</p>'
                . '<a href="https://app.trustmary.com/widget/create" target="_blank" class="button button-primary">' . esc_attr__('Go to Trustmary', 'trustmary-widgets') . '</a> '
                . '<a href="' . admin_url('admin.php?page=trustmary-popups') . '" class="button button-primary">' . esc_attr__('Popups', 'trustmary-widgets') . '</a> '
                . '<a href="' . admin_url('admin.php?page=trustmary-inline') . '" class="button button-primary">' . esc_attr__('Inline widgets', 'trustmary-widgets') . '</a> '
                . '<a href="' . admin_url('admin.php?page=trustmary-experiments') . '" class="button button-primary">' . esc_attr__('Experiments', 'trustmary-widgets') . '</a> '
                . '<a href="' . admin_url('admin.php?page=trustmary-reviews') . '" class="button button-primary">' . esc_attr__('Gather reviews', 'trustmary-widgets') . '</a> ';
        } else {
            echo '<h3>' . esc_attr__('Sign Up for Free', 'trustmary-widgets') . '</h3>'
                . '<a href="https://app.trustmary.com/register?t=free&s=eyJ1dG1fc291cmNlIjoid29yZHByZXNzIiwidXRtX21lZGl1bSI6InBsdWdpbiIsInV0bV9jYW1wYWlnbiI6InNpZ24tdXAifQ==" target="_blank" class="button button-primary">' . esc_attr__('Sign up!', 'trustmary-widgets') . '</a> ';
            echo '<h3>' . esc_attr__('Already have an account?', 'trustmary-widgets') . '</h3>'
                . '<a href="' . admin_url('admin.php?page=trustmary-settings') . '" class="button button-primary">' . esc_attr__('Set up API key here', 'trustmary-widgets') . '</a> ';
        }
    }

    /**
     * Function for displaying popups page
     *
     * @return void
     */
    public function popups()
    {
        echo '<h1>' . esc_attr__('Popups', 'trustmary-widgets') . '</h1>'
            . '<h3>' . esc_attr__('Create a new popup now!', 'trustmary-widgets') . '</h3>'
            . '<a href="https://app.trustmary.com/widget/create" target="_blank" class="button button-primary">' . esc_attr__('Create new popup!', 'trustmary-widgets') . '</a>';
    }

    /**
     * Function for displaying inline widgets page
     *
     * @return void
     */
    public function inline()
    {
        $widgets = Trustmary_Connect::fetch_inline_widgets(Trustmary_Helper::decrypt($this->_config['api_key']));

        echo '<h1>' . esc_attr__('Inline widgets', 'trustmary-widgets') . '</h1>'
            . '<p>' . esc_attr__('You can copy the shortcode by clicking the shortcode field.', 'trustmary-widgets') . '</p>';

        echo Trustmary_Helper::generate_table(array(
            'id' => esc_attr__('ID', 'trustmary-widgets'),
            'name' => esc_attr__('Name', 'trustmary-widgets'),
            'shortcode' => esc_attr__('Shortcode', 'trustmary-widgets'),
        ), $widgets);
    }

    /**
     * Function for displaying experiments page
     *
     * @return void
     */
    public function experiments()
    {
        $experiments = Trustmary_Connect::fetch_experiments(Trustmary_Helper::decrypt($this->_config['api_key']));

        echo '<h1>' . esc_attr__('Experiments', 'trustmary-widgets') . '</h1>'
            . '<p>' . esc_attr__('You can copy the shortcode by clicking the shortcode field.', 'trustmary-widgets') . '</p>';

        echo Trustmary_Helper::generate_table(array(
            'id' => esc_attr__('ID', 'trustmary-widgets'),
            'name' => esc_attr__('Name', 'trustmary-widgets'),
            'shortcode' => esc_attr__('Shortcode', 'trustmary-widgets'),
        ), $experiments);
    }

    /**
     * Function for displaying gather reviews page
     *
     * @return void
     */
    public function reviews()
    {
        echo '<h1>' . esc_attr__('Gather reviews', 'trustmary-widgets') . '</h1>'
            . '<h3>' . esc_attr__('Create review gathering form and gather more reviews!', 'trustmary-widgets') . '</h3>'
            . '<a href="https://app.trustmary.com/survey/create/testimonial" target="_blank" class="button button-primary">' . esc_attr__('Create new review form!', 'trustmary-widgets') . '</a>';
    }
}
