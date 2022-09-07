<?php
defined('ABSPATH') or die('No');

/**
 * A helper class which contains useful methods (i.e. api key encryption)
 * that are being used in other areas of the plugin.
 */
class Trustmary_Helper
{
    /**
     * Encryption method
     *
     * @var string
     */
    public static $encryption_method = 'aes-256-cbc';

    /**
     * Algorithm name for hash value using the HMAC method 
     *
     * @var string
     */
    public static $hash_hmac_algo = 'sha3-512';

    /**
     * Encrypts given string using SECURE_AUTH_KEY, SECURE_AUTH_SALT, 
     * openssl and base64_encode. Returns encrypted string in base64.
     *
     * @param string $string
     * @return string
     */
    public static function encrypt($string)
    {
        $initialization_vector_length = openssl_cipher_iv_length(self::$encryption_method);
        $initialization_vector = openssl_random_pseudo_bytes($initialization_vector_length);

        $encrypted = openssl_encrypt($string, self::$encryption_method, SECURE_AUTH_KEY, OPENSSL_RAW_DATA, $initialization_vector);
        $hmac = hash_hmac(self::$hash_hmac_algo, $encrypted, SECURE_AUTH_SALT, TRUE);

        $output = base64_encode($initialization_vector . $hmac . $encrypted);
        return $output;
    }

    /**
     * Decrypts given encrypted string.
     *
     * @param string $encrypted
     * @return string
     */
    public static function decrypt($encrypted)
    {
        $raw = base64_decode($encrypted);

        $initialization_vector_length = openssl_cipher_iv_length(self::$encryption_method);

        if (strlen($raw) < $initialization_vector_length)
            return '';

        $initialization_vector = substr($raw, 0, $initialization_vector_length);

        $hmac_part = substr($raw, $initialization_vector_length, 64);
        $data_part = substr($raw, $initialization_vector_length + 64);

        $data = openssl_decrypt($data_part, self::$encryption_method, SECURE_AUTH_KEY, OPENSSL_RAW_DATA, $initialization_vector);
        $hmac = hash_hmac(self::$hash_hmac_algo, $data_part, SECURE_AUTH_SALT, TRUE);

        if (hash_equals($hmac_part, $hmac))
            return $data;
        return '';
    }

    /**
     * Obfuscates string by replacing characters with star 
     * that come after the first dash.
     *
     * @param [type] $string
     * @return void
     */
    public static function obfuscate($string)
    {
        $first_dash = strpos($string, '-');
        $display_start = substr($string, 0, $first_dash + 1);
        $obfuscate = preg_replace('/[^-]/', '*', substr($string, $first_dash + 1));

        return $display_start . $obfuscate;
    }

    /**
     * Creates WP admin table based on titles and data (rows).
     *
     * @param array $titles
     * @param array $data
     * @return void
     */
    public static function generate_table($titles, $data)
    {
        $table_html = '<table class="wp-list-table widefat fixed striped posts">'
            . '<thead>'
            . '<tr>';


        foreach ($titles as $key => $label) {
            $table_html .= '<th scope="col" id="' . esc_attr($key) . '" class="manage-column column-author">' . esc_attr($label) . '</th>';
        }

        $table_html .= '</tr>'
            . '</thead>'
            . '<tbody id="the-list">';

        foreach ($data as $index => $row) {
            $table_html .= '<tr id="post-' . esc_attr($index) . '">';

            foreach ($titles as $key => $label) {
                $table_html .= '<td class="column-' . esc_attr($key) . '" data-colname="' . esc_attr($key) . '">';

                if ($key == 'shortcode')
                    $table_html .= '<input type="text" class="copy-shortcode" value="';

                if (is_array($row))
                    $table_html .= esc_attr($row[$key]);
                elseif (is_object($row))
                    $table_html .= esc_attr($row->{$key});

                if ($key == 'shortcode')
                    $table_html .= '" readonly>';

                $table_html .= '</td>';
            }

            $table_html .= '</tr>';
        }

        $table_html .= '</tbody>'
            . '</table>';

        return $table_html;
    }
}
