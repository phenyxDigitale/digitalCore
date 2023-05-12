<?php

/**
 * Class Encryptor
 *
 * @since 1.0.1
 */
class Encryptor {

    const ALGO_BLOWFISH = 0;
    const ALGO_RIJNDAEL = 1;
    const ALGO_PHP_ENCRYPTION = 2;

    /** @var Blowfish|Rijndael|PhpEncryption cipher tool instance */
    protected $cipherTool;

    /** @var Encryptor $instance */
    protected static $instance;

    /** @var Encryptor $standalone */
    protected static $standalone;

    /**
     * Encryptor singleton
     *
     * @return Encryptor instance
     */
    public static function getInstance() {

        if (!static::$instance) {
            $cipherTool = static::getCipherTool();

            if (!$cipherTool) {
                // we need some ciphering capability to encode error message
                static::$instance = new Encryptor(static::getStandaloneCipherTool(__FILE__));
                throw new PhenyxException('No encryption tool available');
            } else {
                static::$instance = new Encryptor($cipherTool);
            }

        }

        return static::$instance;
    }

    /**
     * Encryptor singleton for standalone
     *
     * This encryptor is used in special situations when encryption settings is not
     * set up yet. For example during installation
     *
     * @return Encryptor instance
     */
    public static function getStandaloneInstance($salt) {

        if (!static::$standalone) {
            static::$standalone = new Encryptor(static::getStandaloneCipherTool($salt));
        }

        return static::$standalone;
    }

    /**
     * Creates encryptor instance
     *
     * @param Blowfish|Rijndael|PhpEncryption optional cipher tool to use
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     * @throws PhenyxException
     */
    protected function __construct($cipherTool) {

        $this->cipherTool = $cipherTool;
    }

    /**
     * Encrypt plaintext
     *
     * @param string $content
     *
     * @return bool|string ciphertext
     */
    public function encrypt($content) {

        return $this->cipherTool->encrypt($content);
    }

    /**
     * Decrypt ciphertext
     *
     * @param string $content
     *
     * @return string plaintext
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function decrypt($content) {

        return $this->cipherTool->decrypt($content);
    }

    /**
     * Returns ciphering tool according to settings
     */
    private static function getCipherTool() {

        $algo = (int) Configuration::get('EPH_CIPHER_ALGORITHM');

        if ($algo === static::ALGO_PHP_ENCRYPTION && static::supportsPhpEncryption()) {
            return new PhpEncryption(_PHP_ENCRYPTION_KEY_);
        }

        if ($algo === static::ALGO_RIJNDAEL && static::supportsRijndael()) {
            return new Rijndael(_RIJNDAEL_KEY_, _RIJNDAEL_IV_);
        }

        // always fallback to blowfish

        if (static::supportsBlowfish()) {
            return new Blowfish(_COOKIE_KEY_, _COOKIE_IV_);
        }

        return null;
    }

    /**
     * Returns blowfish ciphering tool used in standalone environment
     */
    private static function getStandaloneCipherTool($salt) {

        return new Blowfish(str_pad('', 56, md5('ps' . $salt)), str_pad('', 56, md5('iv' . $salt)));
    }

    /**
     * Check if PhpEncryption can be used
     */
    private static function supportsPhpEncryption() {

        return defined('_PHP_ENCRYPTION_KEY_') && extension_loaded('openssl') && function_exists('openssl_encrypt');
    }

    /**
     * Check if Rijndael encryption can be used
     */
    private static function supportsRijndael() {

        if (defined('_RIJNDAEL_KEY_') && defined('__RIJNDAEL_IV_')) {
            // Rijndael is supported by openssl directly

            if (extension_loaded('openssl') && function_exists('openssl_encrypt')) {
                return true;
            }

            // if openssl is not present, we can use mcrypt on php < 7.1

            if (function_exists('mcrypt_encrypt') && PHP_VERSION_ID < 70100) {
                return true;
            }

        }

        return false;
    }

    /**
     * Check if Blowfish encryption can be used
     */
    private static function supportsBlowfish() {

        return defined('_COOKIE_KEY_') && defined('_COOKIE_IV_');
    }

}
