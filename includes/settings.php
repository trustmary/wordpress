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

        add_action('pre_update_option_' . $config_idenfifier, array($this, 'update_settings'), 20, 2);
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
            __('API key', 'trustmary-widgets'),
            array($this, 'callback_input_apikey'),
            $this->_config_idenfifier,
            $this->_settings_group,
            array(
                'name' => 'api_key',
                'label' => __(
                    'API key',
                    Trustmary_Widgets::$translate_domain
                )
            )
        );

        /**
         * Adds organization_id option
         */
        add_settings_field(
            'organization_id',
            __('Organization ID', 'trustmary-widgets'),
            array($this, 'callback_input_organization_id'),
            $this->_config_idenfifier,
            $this->_settings_group,
            array(
                'name' => 'organization_id',
                'label' => __(
                    'Organization ID',
                    Trustmary_Widgets::$translate_domain
                )
            )
        );

        /**
         * Adds radio option to enable/disable automatic script insertion
         */
        add_settings_field(
            'add_scripts',
            __('Add Trustmary scripts automatically', 'trustmary-widgets'),
            array($this, 'callback_input_addscripts'),
            $this->_config_idenfifier,
            $this->_settings_group,
            array(
                'name' => 'add_scripts',
                'label' => __(
                    'Add trustmary script automatically',
                    Trustmary_Widgets::$translate_domain
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
                        __(
                            'API key is invalid.',
                            Trustmary_Widgets::$translate_domain
                        ),
                        'error'
                    );
                    $value = '';
                    continue;
                }

                $value = Trustmary_Helper::encrypt($value);
                $updated_values['organization_id'] = $key_test;
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
            <input type="text" name="<?php echo $this->_config_idenfifier . '[' . $args['name'] . ']'; ?>" value="<?php echo $val; ?>" style="min-width:280px;">
        </p>
    <?php
    }

    /**
     * Callback function for organization ID input field
     *
     * @param array $args
     * @return void
     */
    public function callback_input_organization_id($args)
    {
        $val = isset($this->_config[$args['name']]) ? $this->_config[$args['name']] :  __(
            'Organization ID will be fetched automatically using API key.',
            Trustmary_Widgets::$translate_domain
        );
    ?>
        <p><strong><?php echo $val; ?></strong></p>
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
                <input type="radio" class="toggle-script-block" name="<?php echo $this->_config_idenfifier . '[' . $args['name'] . ']'; ?>" value="1" <?php echo $val ? 'checked="checked"' : ''; ?>>
                <span><?php _e('Yes (Scripts will be added automatically)', Trustmary_Widgets::$translate_domain); ?></span>
            </label>
        </p>
        <p>
            <label>
                <input type="radio" class="toggle-script-block" name="<?php echo $this->_config_idenfifier . '[' . $args['name'] . ']'; ?>" value="0" <?php echo !$val ? 'checked="checked"' : ''; ?>>
                <span><?php _e('No (I want to add scripts myself, see below)', Trustmary_Widgets::$translate_domain); ?></span>
            </label>
        </p>
        <p id="trustmary-script" style="display: <?php echo !$val ? 'block' : 'none'; ?>;">
            <textarea name="scripts" style="width: 480px;height:210px;cursor:pointer;" onClick="this.select();" readonly><?php echo htmlentities("<script>(function (w,d,s,o,r,js,fjs) {
    w[r]=w[r]||function() {(w[r].q = w[r].q || []).push(arguments)}
    w[r]('app', '" . $organization_id . "');
    if(d.getElementById(o)) return;
    js = d.createElement(s), fjs = d.getElementsByTagName(s)[0];
    js.id = o; js.src = 'https://embed.trustmary.com/embed.js';
    js.async = 1; fjs.parentNode.insertBefore(js, fjs);
  }(window, document, 'script', 'trustmary-embed', 'tmary'));
</script>
"); ?></textarea>
        </p>
        <script type="text/javascript">
            const radios = document.querySelectorAll('.toggle-script-block');
            const script_block = document.getElementById('trustmary-script');

            function toggle_script_block(event) {
                if (parseInt(this.value) === 1)
                    script_block.style.display = 'none';
                else
                    script_block.style.display = 'block';
            }

            radios.forEach((el) => {
                el.addEventListener('change', toggle_script_block);
            });
        </script>
<?php
    }
}
