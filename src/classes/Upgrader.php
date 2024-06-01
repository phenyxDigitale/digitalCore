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
    
}
