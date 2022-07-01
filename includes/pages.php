<?php
defined('ABSPATH') or die('No');

/**
 * Class that handles displaying admin pages.
 */
class Trustmary_Pages
{
    /**
     * Function for displaying dashboard page
     *
     * @return void
     */
    public static function dashboard()
    {
?>
        <h1><?php _e('Dashboard', Trustmary_Widgets::$translate_domain); ?></h1>
    <?php
    }

    /**
     * Function for displaying popups page
     *
     * @return void
     */
    public static function popups()
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
    public static function inline()
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
    public static function experiments()
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
    public static function reviews()
    {
    ?>
        <h1><?php _e('Gather reviews', Trustmary_Widgets::$translate_domain); ?></h1>
<?php
    }
}
