<?php
defined('ABSPATH') or die('No');

/**
 * Class that handles displaying admin pages.
 */
class Trustmary_Pages
{
    public static function dashboard()
    {
?>
        <h1><?php _e('Dashboard', Trustmary_Widgets::$translate_domain); ?></h1>
<?php
    }
}
