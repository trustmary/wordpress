<?php
defined('ABSPATH') or die('No');

/**
 * Settings class
 * 
 * Creates settings page to admin and handles setting updates.
 */
class Trustmary_Settings
{
    /**
     * Settings page/menu title
     *
     * @var string
     */
    private $_settings_title = 'Trustmary Widgets';

    /**
     * Option group name
     *
     * @var string
     */
    private $_settings_group = 'trustmary_widgets_option_group';

    /**
     * Settings page slug
     *
     * @var string
     */
    private $_settings_menu_slug = 'trustmary_widgets';

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
     * Settings constructor
     *
     * @param string $config_idenfifier
     * @param array $config
     */
    public function __construct($config_idenfifier, $config)
    {
        $this->_config_idenfifier = $config_idenfifier;
        $this->_config = $config;

        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'init_setting_fields'));
    }

    /**
     * Adds settings page
     *
     * @return void
     */
    public function add_settings_page()
    {
        add_options_page(
            $this->_settings_title,
            $this->_settings_title,
            'manage_options',
            $this->_settings_menu_slug,
            array($this, 'create_admin_page')
        );
    }

    /**
     * Callback function to create the settings page
     *
     * @return void
     */
    public function create_admin_page()
    {
?>
        <h1><?php echo $this->_settings_title; ?></h1>
        <div class="wrap trustmary-widgets-form">
            <form method="post" action="options.php">
                <?php
                settings_fields($this->_settings_group);
                do_settings_sections($this->_config_idenfifier);
                submit_button();
                ?>
            </form>
        </div>
<?php
    }

    /**
     * Initializes setting fields
     *
     * @return void
     */
    public function init_setting_fields()
    {
        register_setting(
            $this->_settings_group,
            $this->_config_idenfifier,
            array($this, 'sanitize')
        );

        add_settings_section(
            $this->_settings_group,
            '',
            array($this, 'print_section_info'),
            $this->_config_idenfifier
        );

        /**
         * Adds api_key option
         */
        add_settings_field(
            'api_key',
            __('API key', 'trustmary-widgets'),
            array($this, 'callback_input_apikey'),
            $this->_config_idenfifier,
            $this->_settings_group,
            array('api_key', __('API key', 'trustmary-widgets'))
        );

        /**
         * Adds radio option to enable/disable automatic script insertion
         */
        add_settings_field(
            'add_scripts',
            __('Add trustmary script automatically', 'trustmary-widgets'),
            array($this, 'callback_input_addscripts'),
            $this->_config_idenfifier,
            $this->_settings_group,
            array('add_scripts', __('Add trustmary script automatically', 'trustmary-widgets'))
        );
    }

    /**
     * Prints option section info description
     *
     * @return void
     */
    public function print_section_info()
    {
    }

    /**
     * Sanitizes input
     *
     * @param string $input
     * @return void
     */
    public function sanitize($input)
    {
        return sanitize_text_field($input);
    }

    /**
     * Callback function for API key input field
     *
     * @param array $args
     * @return void
     */
    public function callback_input_apikey($args)
    {
    }

    /**
     * Callback function for add scripts automatically radio field
     *
     * @param array $args
     * @return void
     */
    public function callback_input_addscripts($args)
    {
    }
}
