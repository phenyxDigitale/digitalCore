<?php

class Upgrader {


	public static function executeSqlRequest($query, $method) {

		switch ($method) {
		case 'execute':
			return Db::getInstance()->execute($query);
			break;
		case 'executeS':
			return Db::getInstance()->executeS($query);
			break;
		case 'getValue':
			return Db::getInstance()->getValue($query);
			break;
		case 'getRow':
			return Db::getInstance()->getRow($query);
			break;
		}

	}
    
     public static function writeNewSettings($version)  {
         
        $seeting_files = _EPH_CONFIG_DIR_.'settings.inc.php';
        
        $mysqlEngine = (defined('_MYSQL_ENGINE_') ? _MYSQL_ENGINE_ : 'InnoDB');
        
        copy($seeting_files, str_replace('.php', '.old.php', $seeting_files));
        $confFile = new AddConfToFile($seeting_files, 'w');
       

        $caches = ['CacheMemcache', 'CacheApc', 'CacheFs', 'CacheMemcached', 'CacheXcache'];

        $datas = [
            ['_EPH_CACHING_SYSTEM_', (defined('_EPH_CACHING_SYSTEM_') && in_array(_EPH_CACHING_SYSTEM_, $caches)) ? _EPH_CACHING_SYSTEM_ : 'CacheFs'],
            ['_DB_NAME_', _DB_NAME_],
            ['_MYSQL_ENGINE_', $mysqlEngine],
            ['_DB_SERVER_', _DB_SERVER_],           
            ['_DB_USER_', _DB_USER_],
            ['_DB_PASSWD_', _DB_PASSWD_],
            ['_DB_PREFIX_', _DB_PREFIX_],                   
            ['_COOKIE_KEY_', _COOKIE_KEY_],
            ['_COOKIE_IV_', _COOKIE_IV_],
            ['_EPH_CREATION_DATE_', defined("_EPH_CREATION_DATE_") ? _PS_CREATION_DATE_ : date('Y-m-d')],
            ['_RIJNDAEL_KEY_', _RIJNDAEL_KEY_],
            ['_RIJNDAEL_IV_', _RIJNDAEL_IV_],
            ['_EPH_VERSION_', $version],
            ['_PHP_ENCRYPTION_KEY_', _PHP_ENCRYPTION_KEY_],
        ];
        
        foreach ($datas as $data) {
            $confFile->writeInFile($data[0], $data[1]);
        }

        if ($confFile->error != false) {           
            return false;
        }
        return true;
    }
	

}
