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
    }

    /**
     * Function for displaying dashboard page
     *
     * @return void
     */
    public function dashboard()
    {
        echo '<h1>' . __('Dashboard', Trustmary_Widgets::$translate_domain) . '</h1>';

        if (isset($this->_config['api_key']) && $this->_config['api_key']) {
            echo '<h3>' . __('You are good to go!', Trustmary_Widgets::$translate_domain) . '</h3>'
                . '<p>' . __('You can now create popups, review widgets, lead generation forms an d review gathering forms inside Trustmary!', Trustmary_Widgets::$translate_domain) . '</p>'
                . '<a href="https://app.trustmary.com/widget/create" target="_blank" class="button button-primary">' . __('Go to Trustmary', Trustmary_Widgets::$translate_domain) . '</a> '
                . '<a href="' . admin_url('admin.php?page=trustmary-popups') . '" class="button button-primary">' . __('Popups', Trustmary_Widgets::$translate_domain) . '</a> '
                . '<a href="' . admin_url('admin.php?page=trustmary-inline') . '" class="button button-primary">' . __('Inline widgets', Trustmary_Widgets::$translate_domain) . '</a> '
                . '<a href="' . admin_url('admin.php?page=trustmary-experiments') . '" class="button button-primary">' . __('Experiments', Trustmary_Widgets::$translate_domain) . '</a> '
                . '<a href="' . admin_url('admin.php?page=trustmary-reviews') . '" class="button button-primary">' . __('Gather reviews', Trustmary_Widgets::$translate_domain) . '</a> ';
        } else {
            echo '<h3>' . __('Sign Up for Free', Trustmary_Widgets::$translate_domain) . '</h3>'
            . '<a href="https://app.trustmary.com/register" target="_blank" class="button button-primary">' . __('Sign up!', Trustmary_Widgets::$translate_domain) . '</a> ';
            echo '<h3>' . __('Already have an account?', Trustmary_Widgets::$translate_domain) . '</h3>'
            . '<a href="' . admin_url('admin.php?page=trustmary-settings') . '" class="button button-primary">' . __('Set up API key here', Trustmary_Widgets::$translate_domain) . '</a> ';
        }
    }

    /**
     * Function for displaying popups page
     *
     * @return void
     */
    public function popups()
    {
?>
        <h1><?php _e('Popups', Trustmary_Widgets::$translate_domain); ?></h1>
    <?php
    }

    /**
     * Function for displaying inline widgets page
     *
     * @return void
     */
    public function inline()
    {
    ?>
        <h1><?php _e('Inline widgets', Trustmary_Widgets::$translate_domain); ?></h1>
    <?php
    }

    /**
     * Function for displaying experiments page
     *
     * @return void
     */
    public function experiments()
    {
    ?>
        <h1><?php _e('Experiments', Trustmary_Widgets::$translate_domain); ?></h1>
    <?php
    }

    /**
     * Function for displaying gather reviews page
     *
     * @return void
     */
    public function reviews()
    {
    ?>
        <h1><?php _e('Gather reviews', Trustmary_Widgets::$translate_domain); ?></h1>
<?php
    }
}
