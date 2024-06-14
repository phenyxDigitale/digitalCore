<?php

/**
 * Class TimeZone
 *
 * @since 1.9.1.0
 */
class TimeZone extends PhenyxObjectModel {

    public $require_context = false;
    // @codingStandardsIgnoreStart
    
    /** @var string $name */
    public $name;
    // @codingStandardsIgnoreEnd

    /**
     * @see PhenyxObjectModel::$definition
     */
    public static $definition = [
        'table'   => 'timezone',
        'primary' => 'id_timezone',
        'fields'  => [
            'name'  => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
            
        ],
    ];

    public static function getByName($name) {
        
        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
			(new DbQuery())
            ->select('id_timezone')
			->from('timezone')
			->where('`name` LIKE "' . $name.'"')
		);
    }
}
