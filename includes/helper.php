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


    public static function obfuscate($string)
    {
        $first_dash = strpos($string, '-');
        $display_start = substr($string, 0, $first_dash + 1);
        $obfuscate = preg_replace('/[^-]/', '*', substr($string, $first_dash + 1));

        return $display_start . $obfuscate;
    }
}
