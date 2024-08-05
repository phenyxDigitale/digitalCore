<?php
use Defuse\Crypto\Crypto;
use \Curl\Curl;
/**
 * Class Translation
 *
 * @since 1.9.1.0
 */
class Translation extends PhenyxObjectModel {

    public $require_context = false;
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
        ],
    ];
    /** @var string Name */
    public $iso_code;

    public $origin;
    public $translation;

    public static function getExistingTranslation($iso_code, $origin) {

        return Db::getInstance()->getValue(
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
    
    public static function getdBParam() {

		$url = 'https://ephenyx.io/api';
        $company = Context::getContext()->company;
		$string = Configuration::get('_EPHENYX_LICENSE_KEY_') . '/' . $company->company_url;
		$crypto_key = Tools::encrypt_decrypt('encrypt', $string, _PHP_ENCRYPTION_KEY_, Configuration::get('_EPHENYX_LICENSE_KEY_'));

		$data_array = [
			'action'     => 'getdBParam',
			'crypto_key' => $crypto_key,
		];
		$curl = new \Curl\Curl();
		$curl->setDefaultJsonDecoder($assoc = true);
		$curl->setHeader('Content-Type', 'application/json');
		$curl->post($url, json_encode($data_array));
		return $curl->response;

	}


}
