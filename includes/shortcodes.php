<?php
defined('ABSPATH') or die('No');

/**
 * Class for handling plugin shortcodes
 */
class Trustmary_Shortcodes
{
    /**
     * Constructor. Adds plugin shortcodes.
     */
    public function __construct()
    {
        add_shortcode('trustmary_widget', array($this, 'widget'));
        add_shortcode('trustmary_experiment', array($this, 'experiment'));
    }

    public function widget($atts)
    {
        $attributes = shortcode_atts(array(
            'id' => '',
        ), $atts);

        if (!$attributes['id'])
            return;

        return '<div data-trustmary-widget="' . esc_attr($attributes['id']) . '"></div>';
    }

    public function experiment($atts)
    {
        $attributes = shortcode_atts(array(
            'id' => '',
        ), $atts);

        if (!$attributes['id'])
            return;

        return '<div data-trustmary-experiment="' . esc_attr($attributes['id']) . '"></div>';
    }
}
