<?php

/**
 * @since 1.9.1.0
 */
class PhenyxRedis extends PhenyxObjectModel {

    public $require_context = false;
    // @codingStandardsIgnoreStart
    /**
     * @see PhenyxObjectModel::$definition
     */
    public static $definition = [
        'table'     => 'redis_servers',
        'primary'   => 'id_redis_servers',
        'fields'    => [
            'ip'   => ['type' => self::TYPE_STRING, 'copy_post' => false],
            'port' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false],
            'auth' => ['type' => self::TYPE_STRING, 'copy_post' => false],
            'db'   => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false],

        ],
    ];
    public $ip;
    public $port;
    public $auth;
    public $db;

    /**
     * GenderCore constructor.
     *
     * @param int|null $id
     * @param int|null $idLang
     * @param int|null $idCompany
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function __construct($id = null) {

        parent::__construct($id);
    }

    

}
