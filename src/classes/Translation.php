<?php

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
            'iso_code'         => ['type' => self::TYPE_STRING, 'validate' => 'isLanguageIsoCode', 'required' => true, 'size' => 2],
            'origin'           => ['type' => self::TYPE_HTML,  'required' => true],
            'translation'      => ['type' => self::TYPE_HTML,  'required' => true],
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
			->where('`iso_code` = \'' . trim($iso_code).'\'')
			->where('`origin` = \'' . trim($origin).'\'')
		);
   }

}
