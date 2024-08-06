<?php
use Defuse\Crypto\Crypto;
use \Curl\Curl;
/**
 * Class Translation
 *
 * @since 1.9.1.0
 */
class Translation extends PhenyxObjectModel {
    
    protected static $instance;
    
    public $dbParams;
    /**
     * @see PhenyxObjectModel::$definition
     */
    public static $definition = [
        'table'   => 'translation',
        'primary' => 'id_translation',
        'fields'  => [
            'iso_code'    => ['type' => self::TYPE_STRING, 'validate' => 'isLanguageIsoCode', 'required' => true, 'size' => 2],
            'origin'      => ['type' => self::TYPE_HTML, 'required' => true],
            'translation' => ['type' => self::TYPE_HTML, 'required' => true],
            'date_upd' => ['type' => self::TYPE_HTML, 'required' => true],
        ],
    ];
    /** @var string Name */
    public $iso_code;

    public $origin;
    public $translation;
    public $date_upd;
    
    public function __construct($id = null, $full = true, $idLang = null) {

        parent::__construct($id, $idLang);
        $this->dbParams = $this->getdBParam();

    }
    
    public static function getInstance() {

        if (!Translation::$instance) {
            Translation::$instance = new Translation();
        }

        return Translation::$instance;
    }
    
    public function add($autoDate = false, $nullValues = false) {
        
        $result = $this->dispatchTranslation();
        
        return $result;
        
    }

    public function getExistingTranslation($iso_code, $origin) {

        return Db::getCrmInstance($this->dbParams['_DB_USER_'], $this->dbParams['_DB_PASSWD_'], $this->dbParams['_DB_NAME_'])->getValue(
            (new DbQuery())
                ->select('`translation`')
                ->from('translation')
                ->where('`iso_code` = \'' . trim($iso_code) . '\'')
                ->where('`origin` = \'' . bqSQL(trim($origin)) . '\'')
        );
    }

    public static function getExistingTranslationByIso($iso_code) {
        
        //$dbParams = self::getdBParam();

        $javareturn = [];
        $results = Db::getInstance()->executeS(
            (new DbQuery())
                ->select('*')
                ->from('translation')
                ->where('`iso_code` = \'' . trim($iso_code) . '\'')
        );

        foreach ($results as $result) {
            $javareturn[$result['origin']] = $result['translation'];
        }

        return $javareturn;
    }
    
    public function getdBParam() {

		$url = 'https://ephenyx.io/api';
		$string = Configuration::get('_EPHENYX_LICENSE_KEY_') . '/' . $this->context->company->company_url;
		$crypto_key = Tools::encrypt_decrypt('encrypt', $string, _PHP_ENCRYPTION_KEY_, _COOKIE_KEY_);

		$data_array = [
			'action'     => 'getdBParam',
            'license_key' => Configuration::get('_EPHENYX_LICENSE_KEY_'),
			'crypto_key' => $crypto_key,
		];
		$curl = new \Curl\Curl();
		$curl->setDefaultJsonDecoder($assoc = true);
		$curl->setHeader('Content-Type', 'application/json');
		$curl->post($url, json_encode($data_array));
		return $curl->response;

	}
    
    public function dispatchTranslation() {
        
        $result = true;
        $url = 'https://ephenyx.io/api';
		$string = Configuration::get('_EPHENYX_LICENSE_KEY_') . '/' . $this->context->company->company_url;
		$crypto_key = Tools::encrypt_decrypt('encrypt', $string, _PHP_ENCRYPTION_KEY_, _COOKIE_KEY_);
        
        $data_array = [
			'action' => 'createTranslation',
            'object' => $this,
            'crypto_key' => $crypto_key,
		];
		$curl = new Curl();
		$curl->setDefaultJsonDecoder($assoc = true);
		$curl->setHeader('Content-Type', 'application/json');
		$curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
		$curl->post($url, json_encode($data_array));

        
    }
    
    public static function addTranslation($object) {
        
        
        $object = Tools::jsonDecode(Tools::jsonEncode($object), true);
       
        $translation = new Translation();
        foreach($object as $key => $value) {
             if (property_exists($translation, $key)) {
				$translation->{$key} = $value;
			}
            
        }
        
        $result = $translation->add();
        
        return $result;
    }


}
