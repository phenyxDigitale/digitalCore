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
			'action'      => 'checkLicence',
			'license_key' => Configuration::get('_EPHENYX_LICENSE_KEY_'),
			'crypto_key'  => $this->_crypto_key,
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
			'action'      => 'getPhenyxPlugins',
			'license_key' => Configuration::get('_EPHENYX_LICENSE_KEY_'),
			'crypto_key'  => $this->_crypto_key,
		];
		$curl = new Curl();
		$curl->setDefaultJsonDecoder($assoc = true);
		$curl->setHeader('Content-Type', 'application/json');
		$curl->setTimeout(6000);
		$curl->post($this->_url, json_encode($data_array));
		$plugins = $curl->response;

		if (is_array($plugins)) {
			file_put_contents(
				_EPH_CONFIG_DIR_ . 'json/plugin_sources.json',
				json_encode($plugins, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
			);
			return true;
		}

		return false;

	}

	public static function writeNewSettings($version) {

		$seeting_files = _EPH_CONFIG_DIR_ . 'settings.inc.php';

		$mysqlEngine = (defined('_MYSQL_ENGINE_') ? _MYSQL_ENGINE_ : 'InnoDB');

		copy($seeting_files, str_replace('.php', '.old.php', $seeting_files));
		$confFile = fopen($seeting_files, 'w');
		fwrite($confFile, '<?php' . PHP_EOL . PHP_EOL);

		$caches = ['CacheMemcache', 'CacheApc', 'FileBased', 'AwsRedis', 'CacheMemcached', 'CacheXcache'];
		$current_cache = !(empty(Configuration::get('EPH_PAGE_CACHE_TYPE'))) ? Configuration::get('EPH_PAGE_CACHE_TYPE') : 'FileBased';

		$datas = [
			['_EPH_CACHING_SYSTEM_', (defined('_EPH_CACHING_SYSTEM_') && in_array(_EPH_CACHING_SYSTEM_, $caches)) ? _EPH_CACHING_SYSTEM_ : $current_cache],
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
			['_EPH_VENDOR_DIR_', _EPH_VENDOR_DIR_],
			['_PHP_ENCRYPTION_KEY_', _PHP_ENCRYPTION_KEY_],
		];

		if (defined('_FORUM_MODE_')) {
			$datas[] = ['_FORUM_MODE_', _FORUM_MODE_];
		}

		if (defined('_BLOG_MODE_')) {
			$datas[] = ['_BLOG_MODE_', _BLOG_MODE_];
		}

		if (defined('_WIKI_MODE_')) {
			$datas[] = ['_WIKI_MODE_', _WIKI_MODE_];
		}

		if (defined('_EPHENYX_MODE_')) {
			$datas[] = ['_EPHENYX_MODE_', _EPHENYX_MODE_];
		}

		foreach ($datas as $data) {
			fwrite($confFile, 'define(\'' . $data[0] . '\', \'' . self::checkString($data[1]) . '\');' . PHP_EOL);
		}

		return true;
	}

	public static function checkString($string) {

		if (!is_numeric($string)) {
			$string = addslashes($string);
		}

		return $string;
	}

	public static function cleanBackTabs() {

		$query = 'SELECT id_back_tab  FROM `' . _DB_PREFIX_ . 'back_tab_lang` WHERE id_lang = 1 ORDER BY id_back_tab ASC';
		$tabLangs = Db::getInstance()->executeS($query);

		foreach ($tabLangs as $tabLang) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_back_tab`')
					->from('back_tab')
					->where('`id_back_tab` = ' . (int) $tabLang['id_back_tab'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'back_tab_lang` WHERE id_back_tab = ' . $tabLang['id_back_tab'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT id_back_tab  FROM `' . _DB_PREFIX_ . 'employee_access` ORDER BY id_back_tab ASC';
		$tabAccess = Db::getInstance()->executeS($query);

		foreach ($tabAccess as $access) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_back_tab`')
					->from('back_tab')
					->where('`id_back_tab` = ' . (int) $access['id_back_tab'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'employee_access` WHERE id_back_tab = ' . $access['id_back_tab'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT id_back_tab  FROM `' . _DB_PREFIX_ . 'back_tab` ORDER BY id_back_tab ASC';

		$tabs = Db::getInstance()->executeS($query);

		$i = 1;

		foreach ($tabs as $tab) {

			$parents = Db::getInstance()->executes(
				(new DbQuery())
					->select('`id_back_tab`')
					->from('back_tab')
					->where('`id_parent` = ' . (int) $tab['id_back_tab'])
			);

			foreach ($parents as $parent) {
				$sql = 'UPDATE `' . _DB_PREFIX_ . 'back_tab` SET id_parent = ' . $i . ' WHERE id_back_tab = ' . $parent['id_back_tab'];
				Db::getInstance()->execute($sql);
			}

			$sql = 'UPDATE `' . _DB_PREFIX_ . 'back_tab` SET id_back_tab = ' . $i . ' WHERE id_back_tab = ' . $tab['id_back_tab'];
			Db::getInstance()->execute($sql);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'back_tab_lang` SET id_back_tab = ' . $i . ' WHERE id_back_tab = ' . $tab['id_back_tab'];
			Db::getInstance()->execute($sql);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'employee_access` SET id_back_tab = ' . $i . ' WHERE id_back_tab = ' . $tab['id_back_tab'];
			Db::getInstance()->execute($sql);
			$i++;
		}

	}

	public static function cleanMetas() {

		$query = 'SELECT id_meta  FROM `' . _DB_PREFIX_ . 'meta_lang` WHERE id_lang = 1 ORDER BY id_meta ASC';
		$metaLangs = Db::getInstance()->executeS($query);

		foreach ($metaLangs as $metaLang) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_meta`')
					->from('meta')
					->where('`id_meta` = ' . (int) $metaLang['id_meta'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'meta_lang` WHERE id_meta = ' . $metaLang['id_meta'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT id_meta  FROM `' . _DB_PREFIX_ . 'theme_meta` ORDER BY id_meta ASC';
		$themeMetas = Db::getInstance()->executeS($query);

		foreach ($themeMetas as $themeMeta) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_meta`')
					->from('meta')
					->where('`id_meta` = ' . (int) $themeMeta['id_meta'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'theme_meta` WHERE id_meta = ' . $themeMeta['id_meta'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT id_meta  FROM `' . _DB_PREFIX_ . 'meta` ORDER BY id_meta ASC';

		$metas = Db::getInstance()->executeS($query);

		$i = 1;

		foreach ($metas as $meta) {

			$sql = 'UPDATE `' . _DB_PREFIX_ . 'meta` SET id_meta = ' . $i . ' WHERE id_meta = ' . $meta['id_meta'];
			Db::getInstance()->execute($sql);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'meta_lang` SET id_meta = ' . $i . ' WHERE id_meta = ' . $meta['id_meta'];
			Db::getInstance()->execute($sql);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'theme_meta` SET id_meta = ' . $i . ' WHERE id_meta = ' . $meta['id_meta'];
			Db::getInstance()->execute($sql);
			$i++;
		}

	}

	public static function cleanPlugins() {

		$query = 'SELECT DISTINCT(id_plugin)  FROM `' . _DB_PREFIX_ . 'plugin_access`  ORDER BY id_plugin ASC';
		$pluginAccess = Db::getInstance()->executeS($query);

		foreach ($pluginAccess as $access) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_plugin`')
					->from('plugin')
					->where('`id_plugin` = ' . (int) $access['id_plugin'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'plugin_access` WHERE id_plugin = ' . $access['id_plugin'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT DISTINCT(id_plugin)  FROM `' . _DB_PREFIX_ . 'plugin_carrier`  ORDER BY id_plugin ASC';
		$pluginCarriers = Db::getInstance()->executeS($query);

		foreach ($pluginCarriers as $carrier) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_plugin`')
					->from('plugin')
					->where('`id_plugin` = ' . (int) $carrier['id_plugin'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'plugin_carrier` WHERE id_plugin = ' . $carrier['id_plugin'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT DISTINCT(id_plugin)  FROM `' . _DB_PREFIX_ . 'plugin_country`  ORDER BY id_plugin ASC';
		$pluginCountries = Db::getInstance()->executeS($query);

		foreach ($pluginCountries as $country) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_plugin`')
					->from('plugin')
					->where('`id_plugin` = ' . (int) $country['id_plugin'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'plugin_country` WHERE id_plugin = ' . $country['id_plugin'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT DISTINCT(id_plugin)  FROM `' . _DB_PREFIX_ . 'plugin_currency`  ORDER BY id_plugin ASC';
		$pluginCountries = Db::getInstance()->executeS($query);

		foreach ($pluginCountries as $country) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_plugin`')
					->from('plugin')
					->where('`id_plugin` = ' . (int) $country['id_plugin'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'plugin_currency` WHERE id_plugin = ' . $country['id_plugin'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT DISTINCT(id_plugin)  FROM `' . _DB_PREFIX_ . 'plugin_group`  ORDER BY id_plugin ASC';
		$pluginCountries = Db::getInstance()->executeS($query);

		foreach ($pluginCountries as $country) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_plugin`')
					->from('plugin')
					->where('`id_plugin` = ' . (int) $country['id_plugin'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'plugin_group` WHERE id_plugin = ' . $country['id_plugin'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT DISTINCT(id_plugin)  FROM `' . _DB_PREFIX_ . 'hook_plugin`  ORDER BY id_plugin ASC';
		$pluginCountries = Db::getInstance()->executeS($query);

		foreach ($pluginCountries as $country) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_plugin`')
					->from('plugin')
					->where('`id_plugin` = ' . (int) $country['id_plugin'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'hook_plugin` WHERE id_plugin = ' . $country['id_plugin'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT DISTINCT(id_plugin)  FROM `' . _DB_PREFIX_ . 'hook_plugin_exceptions`  ORDER BY id_plugin ASC';
		$pluginCountries = Db::getInstance()->executeS($query);

		foreach ($pluginCountries as $country) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_plugin`')
					->from('plugin')
					->where('`id_plugin` = ' . (int) $country['id_plugin'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'hook_plugin_exceptions` WHERE id_plugin = ' . $country['id_plugin'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT DISTINCT(id_plugin)  FROM `' . _DB_PREFIX_ . 'payment_mode`  ORDER BY id_plugin ASC';
		$pluginCountries = Db::getInstance()->executeS($query);

		foreach ($pluginCountries as $country) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_plugin`')
					->from('plugin')
					->where('`id_plugin` = ' . (int) $country['id_plugin'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'payment_mode` WHERE id_plugin = ' . $country['id_plugin'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT *  FROM `' . _DB_PREFIX_ . 'plugin` ORDER BY id_plugin ASC';
		$plugins = Db::getInstance()->executeS($query);

		foreach ($plugins as $plugin) {

			if (file_exists(_EPH_PLUGIN_DIR_ . $plugin['name'] . '/' . $plugin['name'] . '.php')) {
				continue;
			} else

			if (file_exists(_EPH_SPECIFIC_PLUGIN_DIR_ . $plugin['name'] . '/' . $plugin['name'] . '.php')) {
				continue;
			}
			
			$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'plugin` WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'plugins_perfs` WHERE plugin = \'' . $plugin['name'] . '\'';
			Db::getInstance()->execute($sql);
			$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'plugin_access` WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'plugin_carrier` WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'plugin_country` WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'plugin_currency` WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'plugin_group` WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'hook_plugin` WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'hook_plugin_exceptions` WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'payment_mode` WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);

		}

		$query = 'SELECT id_plugin  FROM `' . _DB_PREFIX_ . 'plugin` ORDER BY id_plugin ASC';
		$plugins = Db::getInstance()->executeS($query);
		$i = 1;

		foreach ($plugins as $plugin) {
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin` SET id_plugin = ' . $i . ' WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);

			$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin_access` SET id_plugin = ' . $i . ' WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin_carrier` SET id_plugin = ' . $i . ' WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin_country` SET id_plugin = ' . $i . ' WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin_currency` SET id_plugin = ' . $i . '  WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin_group` SET id_plugin = ' . $i . '  WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_plugin` SET id_plugin = ' . $i . '  WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_plugin_exceptions` SET id_plugin = ' . $i . '  WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'payment_mode` SET id_plugin = ' . $i . '  WHERE id_plugin = ' . $plugin['id_plugin'];
			Db::getInstance()->execute($sql);
			$i++;

		}

	}

	public static function cleanHook() {

		$query = 'SELECT DISTINCT(id_hook)  FROM `' . _DB_PREFIX_ . 'hook_plugin_exceptions`  ORDER BY id_hook ASC';
		$hooks = Db::getInstance()->executeS($query);

		foreach ($hooks as $hook) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_hook`')
					->from('hook')
					->where('`id_hook` = ' . (int) $hook['id_hook'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'hook_plugin_exceptions` WHERE id_hook = ' . $hook['id_hook'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT DISTINCT(id_hook)  FROM `' . _DB_PREFIX_ . 'hook_lang`  ORDER BY id_hook ASC';
		$hooks = Db::getInstance()->executeS($query);

		foreach ($hooks as $hook) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_hook`')
					->from('hook')
					->where('`id_hook` = ' . (int) $hook['id_hook'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'hook_lang` WHERE id_hook = ' . $hook['id_hook'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT DISTINCT(id_hook)  FROM `' . _DB_PREFIX_ . 'hook_plugin`  ORDER BY id_hook ASC';
		$hooks = Db::getInstance()->executeS($query);

		foreach ($hooks as $hook) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_hook`')
					->from('hook')
					->where('`id_hook` = ' . (int) $hook['id_hook'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'hook_plugin` WHERE id_hook = ' . $hook['id_hook'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT DISTINCT(id_hook)  FROM `' . _DB_PREFIX_ . 'hook_plugin_exceptions`  ORDER BY id_hook ASC';
		$hooks = Db::getInstance()->executeS($query);

		foreach ($hooks as $hook) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_hook`')
					->from('hook')
					->where('`id_hook` = ' . (int) $hook['id_hook'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'hook_plugin_exceptions` WHERE id_hook = ' . $hook['id_hook'];
				Db::getInstance()->execute($sql);
			}

		}

		$query = 'SELECT *  FROM `' . _DB_PREFIX_ . 'hook` ORDER BY id_hook ASC';
		$hooks = Db::getInstance()->executeS($query);

		$i = 1;

		foreach ($hooks as $hook) {
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'hook` SET id_hook = ' . $i . ' WHERE id_hook = ' . $hook['id_hook'];
			Db::getInstance()->execute($sql);

			$sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_plugin_exceptions` SET id_hook = ' . $i . ' WHERE id_hook = ' . $hook['id_hook'];
			Db::getInstance()->execute($sql);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_lang` SET id_hook = ' . $i . ' WHERE id_hook = ' . $hook['id_hook'];
			Db::getInstance()->execute($sql);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_plugin` SET id_hook = ' . $i . ' WHERE id_hook = ' . $hook['id_hook'];
			Db::getInstance()->execute($sql);
			$i++;

		}

		Hook::getArgs(true);
        self::resetPlugin();

	}
    
    public static function resetPlugin() {
        
        $query = 'SELECT *  FROM `' . _DB_PREFIX_ . 'plugin` ORDER BY id_plugin ASC';
		$plugins = Db::getInstance()->executeS($query);
        
        foreach ($plugins as $plugin) {

			if (file_exists(_EPH_PLUGIN_DIR_ . $plugin['name'] . '/' . $plugin['name'] . '.php')) {
				require_once _EPH_PLUGIN_DIR_ . $plugin['name'] . '/' . $plugin['name'] . '.php';
			} else

			if (file_exists(_EPH_SPECIFIC_PLUGIN_DIR_ . $plugin['name'] . '/' . $plugin['name'] . '.php')) {
				require_once _EPH_SPECIFIC_PLUGIN_DIR_ . $plugin['name'] . '/' . $plugin['name'] . '.php';
			}
            
            if (class_exists($plugin['name'], false)) {

                $tmpPlugin = Adapter_ServiceLocator::get($plugin['name']);
                
                if (method_exists($tmpPlugin, 'reset')) {
                    $plugin = Plugin::getInstanceByName($plugin['name']);
                    $plugin->reset();
                    
                }
                
            }
        }
    }

}
