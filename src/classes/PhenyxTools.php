<?php

use \Curl\Curl;

/**
 * Class PhenyxToolsCore
 *
 * @since 1.9.1.0
 */
class PhenyxTools {
    
    protected static $instance;
    
    protected $_url;
    
    protected $_crypto_key;
    
    public $context;
    
    public $license;
    
    public function __construct() {
        
        $this->context = Context::getContext();
        
        $this->_url = 'https://ephenyx.io/api';
        $string = Configuration::get('_EPHENYX_LICENSE_KEY_') . '/' . $this->context->company->domain_ssl;
        $this->_crypto_key = Tools::encrypt_decrypt('encrypt', $string, _PHP_ENCRYPTION_KEY_, _COOKIE_KEY_);
        
        $this->license = $this->checkLicense();
        $this->context->license = $this->license;
        
     
    }
    
    public static function getInstance() {

        if (!PhenyxTools::$instance) {
            PhenyxTools:$instance = new PhenyxTools();
        }

        return PhenyxTools::$instance;
    }
    
    public function checkLicense() {

		$data_array = [
			'action' => 'checkLicence',
            'license_key' => Configuration::get('_EPHENYX_LICENSE_KEY_'),
            'crypto_key' => $this->_crypto_key,
		];
		$curl = new Curl();
		$curl->setDefaultJsonDecoder($assoc = true);
		$curl->setHeader('Content-Type', 'application/json');
		$curl->setTimeout(6000);
		$curl->post($this->_url, json_encode($data_array));
		return $curl->response;


	}
    
    public function getPhenyxPlugins() {

		$data_array = [
			'action' => 'getPhenyxPlugins',
            'license_key' => Configuration::get('_EPHENYX_LICENSE_KEY_'),
            'crypto_key' => $this->_crypto_key,
		];
		$curl = new Curl();
		$curl->setDefaultJsonDecoder($assoc = true);
		$curl->setHeader('Content-Type', 'application/json');
		$curl->setTimeout(6000);
		$curl->post($this->_url, json_encode($data_array));
		$md5List = $curl->response;

		if (is_array($md5List)) {
			file_put_contents(
				_EPH_CONFIG_DIR_ . 'xml/plugin_sources.json',
				json_encode($md5List, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
			);
			return true;
		}

		return false;

	}

}
