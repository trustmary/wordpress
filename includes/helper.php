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
    private $_encryption_method = 'aes-256-cbc';

    /**
     * Algorithm name for hash value using the HMAC method 
     *
     * @var string
     */
    private $_hash_hmac_algo = 'sha3-512';

    /**
     * Encrypts given string using SECURE_AUTH_KEY, SECURE_AUTH_SALT, 
     * openssl and base64_encode. Returns encrypted string in base64.
     *
     * @param string $string
     * @return string
     */
    public function encrypt($string)
    {
        $initialization_vector_length = openssl_cipher_iv_length($this->_encryption_method);
        $initialization_vector = openssl_random_pseudo_bytes($initialization_vector_length);

        $encrypted = openssl_encrypt($string, $this->_encryption_method, SECURE_AUTH_KEY, OPENSSL_RAW_DATA, $initialization_vector);
        $hmac = hash_hmac($this->_hash_hmac_algo, $encrypted, SECURE_AUTH_SALT, TRUE);

        $output = base64_encode($initialization_vector . $hmac . $encrypted);
        return $output;
    }

    /**
     * Decrypts given encrypted string.
     *
     * @param string $encrypted
     * @return string
     */
    public function decrypt($encrypted)
    {
        $raw = base64_decode($encrypted);

        $initialization_vector_length = openssl_cipher_iv_length($this->_encryption_method);
        $initialization_vector = substr($raw, 0, $initialization_vector_length);

        $hmac_part = substr($raw, $initialization_vector_length, 64);
        $data_part = substr($raw, $initialization_vector_length + 64);

        $data = openssl_decrypt($data_part, $this->_encryption_method, SECURE_AUTH_KEY, OPENSSL_RAW_DATA, $initialization_vector);
        $hmac = hash_hmac($this->_hash_hmac_algo, $data_part, SECURE_AUTH_SALT, TRUE);

        if (hash_equals($hmac_part, $hmac))
            return $data;
        return '';
    }
}
