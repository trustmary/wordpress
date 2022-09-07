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
    private $_settings_title = 'Trustmary Settings';

    /**
     * Settings menu title
     *
     * @var string
     */
    private $_settings_menu = 'Settings';

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
    private $_settings_menu_slug = 'trustmary-settings';

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

        add_action('pre_update_option_' . $config_idenfifier, array($this, 'update_settings'), 20, 2);
    }

    /**
     * Adds settings page
     *
     * @return void
     */
    public function add_settings_page()
    {
        add_submenu_page(
            'trustmary-dashboard',
            $this->_settings_title,
            $this->_settings_menu,
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
        settings_errors($this->_config_idenfifier);
?>
        <h1><?php echo esc_attr($this->_settings_title); ?></h1>
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
            array(
                'type' => 'array',
                'sanitize_callback' => array($this, 'sanitize')
            )
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
            esc_attr__('API key', 'trustmary-widgets'),
            array($this, 'callback_input_apikey'),
            $this->_config_idenfifier,
            $this->_settings_group,
            array(
                'name' => 'api_key',
                'label' => esc_attr__(
                    'API key',
                    'trustmary-widgets'
                )
            )
        );

        /**
         * Adds organization_id option
         */
        add_settings_field(
            'organization_id',
            esc_attr__('Organization ID', 'trustmary-widgets'),
            array($this, 'callback_input_organization'),
            $this->_config_idenfifier,
            $this->_settings_group,
            array(
                'name' => 'organization_id',
                'label' => esc_attr__(
                    'Organization ID',
                    'trustmary-widgets'
                )
            )
        );

        /**
         * Adds organization_name option
         */
        add_settings_field(
            'organization_name',
            esc_attr__('Organization Name', 'trustmary-widgets'),
            array($this, 'callback_input_organization'),
            $this->_config_idenfifier,
            $this->_settings_group,
            array(
                'name' => 'organization_name',
                'label' => esc_attr__(
                    'Organization Name',
                    'trustmary-widgets'
                )
            )
        );

        /**
         * Adds radio option to enable/disable automatic script insertion
         */
        add_settings_field(
            'add_scripts',
            esc_attr__('Add Trustmary scripts automatically', 'trustmary-widgets'),
            array($this, 'callback_input_addscripts'),
            $this->_config_idenfifier,
            $this->_settings_group,
            array(
                'name' => 'add_scripts',
                'label' => esc_attr__(
                    'Add trustmary script automatically',
                    'trustmary-widgets'
                )
            )
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
     * @param array $input
     * @return array
     */
    public function sanitize($inputs)
    {
        foreach ($inputs as &$input) {
            $input = htmlentities(sanitize_text_field($input), ENT_QUOTES);
        }

        return $inputs;
    }

    /**
     * Filters values before saving. Encrypts given API key.
     *
     * @param array $updated_values
     * @param array $old_values
     * @return array
     */
    public function update_settings($updated_values, $old_values)
    {
        if (isset($old_values['organization_id']))
            $updated_values['organization_id'] = $old_values['organization_id'];

        if (isset($old_values['organization_name']))
            $updated_values['organization_name'] = $old_values['organization_name'];

        foreach ($updated_values as $key => &$value) {
            if ($key === 'api_key') {
                if (substr_count($value, '*')) {
                    $value = $old_values[$key];
                    continue;
                }

                $key_test = Trustmary_Connect::test_apikey($value);

                if (!$key_test) {
                    add_settings_error(
                        $this->_config_idenfifier,
                        'API_KEY_INVALID',
                        esc_attr__(
                            'API key is invalid.',
                            'trustmary-widgets'
                        ),
                        'error'
                    );
                    $value = '';
                    $updated_values['organization_id'] = '';
                    $updated_values['organization_name'] = '';
                    continue;
                }

                $value = Trustmary_Helper::encrypt($value);
                $updated_values['organization_id'] = $key_test['organization_id'];
                $updated_values['organization_name'] = $key_test['organization_name'];
            }
        }

        return $updated_values;
    }

    /**
     * Callback function for API key input field
     *
     * @param array $args
     * @return void
     */
    public function callback_input_apikey($args)
    {
        $val = isset($this->_config[$args['name']]) ? Trustmary_Helper::obfuscate(Trustmary_Helper::decrypt($this->_config[$args['name']])) : '';
    ?>
        <p>
            <input type="text" name="<?php echo esc_attr($this->_config_idenfifier . '[' . $args['name'] . ']'); ?>" value="<?php echo esc_attr($val); ?>" style="min-width:280px;">
        </p>
    <?php
    }

    /**
     * Callback function for organization ID and name input fields
     *
     * @param array $args
     * @return void
     */
    public function callback_input_organization($args)
    {
        $val = isset($this->_config[$args['name']]) ? $this->_config[$args['name']] :  esc_attr__(
            'Organization info will be fetched automatically using API key.',
            'trustmary-widgets'
        );
    ?>
        <p><strong><?php echo esc_attr($val); ?></strong></p>
    <?php
    }

    /**
     * Callback function for add scripts automatically radio field
     *
     * @param array $args
     * @return void
     */
    public function callback_input_addscripts($args)
    {
        $val = isset($this->_config[$args['name']]) ? $this->_config[$args['name']] : 1;
        $organization_id = isset($this->_config['organization_id']) ? $this->_config['organization_id'] : 'ORGANIZATION_ID';
    ?>
        <p>
            <label>
                <input type="radio" class="toggle-script-block" name="<?php echo esc_attr($this->_config_idenfifier . '[' . $args['name'] . ']'); ?>" value="1" <?php echo esc_attr($val) ? 'checked="checked"' : ''; ?>>
                <span><?php echo esc_attr__('Yes (Scripts will be added automatically)', 'trustmary-widgets'); ?></span>
            </label>
        </p>
        <p>
            <label>
                <input type="radio" class="toggle-script-block" name="<?php echo esc_attr($this->_config_idenfifier . '[' . $args['name'] . ']'); ?>" value="0" <?php echo !esc_attr($val) ? 'checked="checked"' : ''; ?>>
                <span><?php echo esc_attr__('No (I want to add scripts myself, see below)', 'trustmary-widgets'); ?></span>
            </label>
        </p>
        <p id="trustmary-script" style="display: <?php echo !esc_attr($val) ? 'block' : 'none'; ?>;">
            <textarea name="scripts" style="width: 480px;height:210px;cursor:pointer;" onClick="this.select();" readonly><?php echo htmlentities("<script>(function (w,d,s,o,r,js,fjs) {
    w[r]=w[r]||function() {(w[r].q = w[r].q || []).push(arguments)}
    w[r]('app', '" . esc_attr($organization_id) . "');
    if(d.getElementById(o)) return;
    js = d.createElement(s), fjs = d.getElementsByTagName(s)[0];
    js.id = o; js.src = 'https://embed.trustmary.com/embed.js';
    js.async = 1; fjs.parentNode.insertBefore(js, fjs);
  }(window, document, 'script', 'trustmary-embed', 'tmary'));
</script>
"); ?></textarea>
        </p>
<?php
    }
}
