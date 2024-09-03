<?php

/**
 * Class HookCore
 *
 * @since 1.9.1.0
 */
class Hook extends PhenyxObjectModel {

    // @codingStandardsIgnoreStart
    /**
     * @var array List of executed hooks on this page
     */
    public static $executed_hooks = [];

    public static $native_plugin;

    public static $hook_instance;

    protected static $_available_plugins_cache = null;

    public $name;

    public $target;

    public $is_tag;

    public $position = false;

    public $metas = [];

    public $generated;

    public $title;

    public $description;

    public $tag_value;

    public $plugins = [];
    
    public $args;

    public $available_plugins = [];
    /**
     * @see PhenyxObjectModel::$definition
     */
    public static $definition = [
        'table'     => 'hook',
        'primary'   => 'id_hook',
        'multilang' => true,
        'fields'    => [
            'name'              => ['type' => self::TYPE_STRING, 'validate' => 'isHookName', 'required' => true, 'size' => 64],
            'target'            => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'position'          => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'is_tag'            => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'metas'             => ['type' => self::TYPE_NOTHING, 'validate' => 'isJSON'],
            'plugins'           => ['type' => self::TYPE_NOTHING],
            'available_plugins' => ['type' => self::TYPE_NOTHING],
            'live_edit'         => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            /* Lang fields */
            'generated'         => ['type' => self::TYPE_BOOL, 'lang' => true],
            'title'             => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'],
            'description'       => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
            'tag_value'         => ['type' => self::TYPE_STRING, 'lang' => true],
        ],
    ];

    public function __construct($id = null, $idLang = null) {
       
        $this->className = get_class($this);
        $this->context = Context::getContext();
        
        if (!isset(PhenyxObjectModel::$loaded_classes[$this->className])) {
            $this->def = PhenyxObjectModel::getDefinition($this->className);            

            if (!Validate::isTableOrIdentifier('id_hook') || !Validate::isTableOrIdentifier('hook')) {
                throw new PhenyxException('Identifier or table format not valid for class ' . $this->className);
                PhenyxLogger::addLog(sprintf($this->l('Identifier or table format not valid for class %s'), $this->className), 3, null, get_class($this));
            }

            PhenyxObjectModel::$loaded_classes[$this->className] = get_object_vars($this);
        } else {

            foreach (PhenyxObjectModel::$loaded_classes[$this->className] as $key => $value) {
                $this->{$key}

                = $value;
            }

        }

        $this->id_lang = (Language::getLanguage($idLang) !== false) ? $idLang : Configuration::get(Configuration::LANG_DEFAULT);
        $this->context->_hook = $this;
        $this->args = Tools::jsonDecode(Configuration::get('_EPH_MAIN_ARG_VALUE_'), true);
        //$this->context->hook_args = $this->getArgs();

        if ($id) {
            $this->id = $id;
            $entityMapper = Adapter_ServiceLocator::get("Adapter_EntityMapper");
            $entityMapper->load($this->id, $idLang, $this, $this->def, false);
            $this->plugins = $this->getPlugins();
            $this->available_plugins = $this->getPossiblePluginList();
            $this->metas = Tools::jsonDecode($this->metas, true);
        }
        
        

    }

    public static function getInstance() {

        if (!static::$hook_instance) {
            static::$hook_instance = new Hook();
        }

        return static::$hook_instance;
    }

    public function add($autoDate = true, $nullValues = false) {

        $this->plugins = Tools::jsonEncode($this->plugins);
        $this->available_plugins = Tools::jsonEncode($this->available_plugins);
        $this->metas = Tools::jsonEncode($this->metas);
        Cache::clean('hook_idsbyname');

        return parent::add($autoDate, $nullValues);
    }

    public function update($nullValues = false) {

        $this->plugins = Tools::jsonEncode($this->plugins);
        $this->available_plugins = Tools::jsonEncode($this->available_plugins);
        $this->metas = Tools::jsonEncode($this->metas);
        $return = parent::update($nullValues);

        return $return;
    }

    public function getArgs($force = false) {

        $args = [];

        if ($force) {
            $args_conf_id = $this->getIdByName('actionHookExtraArgs');
        } else {
            $args_conf_id = Configuration::get('_EPH_MAIN_ARG_ID_') ? Configuration::get('_EPH_MAIN_ARG_ID_') : $this->getIdByName('actionHookExtraArgs');
        }

        if ($args_conf_id > 0) {
            Configuration::updateValue('_EPH_MAIN_ARG_ID_', $args_conf_id);
            $plugins = $this->getPluginsFromHook($args_conf_id, null, $force);

            foreach ($plugins as $plugin) {
                $pluginInstance = Plugin::getInstanceByName($plugin['name']);
                $hookCallable = is_callable([$pluginInstance, 'hook' . $plugin['title']]);

                if (($hookCallable) && Plugin::preCall($pluginInstance->name)) {
                    $display = $this->coreCallHook($pluginInstance, 'hook' . $plugin['title'], []);

                    foreach ($display as $key => $value) {

                        if ($key == 'cookie') {
                            $args[$key] = 'construct cookie';
                        } else {
                            $args[$key] = $value;
                        }

                    }

                }

            }

            Configuration::updateValue('_EPH_MAIN_ARG_VALUE_', Tools::jsonEncode($args));
        }

        return $args;
    }

    public function getPlugins($force = false) {

        if (!empty($this->plugins) || $force) {
            return Tools::jsonDecode($this->plugins, true);
        }

        $plugins = $this->getHookPluginExecList($this->name);
        $collection = [];

        if (is_array($plugins)) {

            foreach ($plugins as &$plugin) {
                $tmpInstance = Plugin::getInstanceById($plugin['id_plugin']);

                if (!$tmpInstance->active) {
                    continue;
                }

                $position = Db::getInstance()->getValue(
                    (new DbQuery())
                        ->select('position')
                        ->from('hook_plugin')
                        ->where('`id_hook` =' . $this->id)
                        ->where('`id_plugin` =' . $plugin['id_plugin'])
                );
                $collection[$tmpInstance->name] = [
                    'id_plugin'   => $plugin['id_plugin'],
                    'displayName' => $tmpInstance->displayName,
                    'id_hook'     => $this->id,
                    'position'    => $position,
                ];

            }

        }

        $this->plugins = $collection;
        $this->update();
        return $collection;
    }

    public function getPossiblePluginList($force = false) {

        if (!empty($this->available_plugins) || $force) {
            return Tools::jsonDecode($this->available_plugins, true);
        }

        if ($this->context->cache_enable && is_object($this->context->cache_api)) {
            $cacheId = 'getPossiblePluginList';
            $value = $this->context->cache_api->getData($cacheId);
            $temp = empty($value) ? null : Tools::jsonDecode($value, true);

            if (!empty($temp)) {
                return $temp;
            }

        }

        $collection = [];
        $plugins = Plugin::getPluginsOnDisk();

        foreach ($plugins as $plugin) {

            if (is_array($this->plugins) && array_key_exists($plugin->name, $this->plugins)) {
                continue;
            }

            $tmpInstance = Plugin::getInstanceById($plugin->id);

            if (is_callable([$tmpInstance, 'hook' . ucfirst($this->name)])) {
                $collection[$tmpInstance->name] = [
                    'id_plugin' => $tmpInstance->id,
                    'name'      => $tmpInstance->name,
                ];
            }

        }

        $this->available_plugins = $collection;
        $this->update();

        if ($this->context->cache_enable && is_object($this->context->cache_api)) {
            $temp = $collection === null ? null : Tools::jsonEncode($collection);
            $this->context->cache_api->putData($cacheId, $temp, 1864000);
        }

        return $collection;

    }

    public function getHooksCollection($idLang = null) {

        $collection = [];

        if (is_null($idLang)) {
            $idLang = $this->context->language->id;
        }

        $hooks = new PhenyxCollection('Hook', $idLang);

        foreach ($hooks as $hook) {
            $collection[] = new Hook($hook->id, $idLang);
        }

        return $collection;
    }

    public function getHooks($position = false) {

        $query = new DbQuery();
        $query->select('h.*, hl.*');
        $query->from('hook', 'h');
        $query->leftJoin('hook_lang', 'hl', 'hl.`id_hook` = h.`id_hook` AND hl.`id_lang` = ' . $this->context->language->id);

        if ($position) {
            $query->where('h`position` = 1');
        }

        $query->orderBy('h.`name`');

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS($query);
    }

    public function getNameById($hookId) {

        $cacheId = 'hook_namebyid_' . $hookId;

        if (!CacheApi::isStored($cacheId)) {
            $result = Db::getInstance()->getValue(
                (new DbQuery())
                    ->select('`name`')
                    ->from('hook')
                    ->where('`id_hook` = ' . (int) $hookId)
            );
            CacheApi::store($cacheId, $result);

            return $result;
        }

        return CacheApi::retrieve($cacheId);
    }

    public function getPluginsFromHook($idHook, $idPlugin = null) {

        $hmList = $this->getHookPluginList();
        $pluginList = (isset($hmList[$idHook])) ? $hmList[$idHook] : [];

        if ($idPlugin) {
            return (isset($pluginList[$idPlugin])) ? [$pluginList[$idPlugin]] : [];
        }

        return $pluginList;
    }

    public function getPluginHook() {

        return Db::getInstance()->executeS(
            (new DbQuery())
                ->select('DISTINCT(hm.`id_hook`), h.*')
                ->from('hook_plugin', 'hm')
                ->leftJoin('hook', 'h', 'h.`id_hook` = hm.`id_hook`')
                ->leftJoin('hook_lang', 'hl', 'hl.`id_hook` = h.`id_hook` AND hl.`id_lang` = ' . $this->context->language->id)
                ->leftJoin('plugin', 'm', 'm.`id_plugin` = hm.`id_plugin`')
                ->orderBy('hm.`position` ASC')
                ->where('h.live_edit = 1 AND m.active = 1')
        );
    }

    public function getHookPluginList($use_cache = true) {

        $cacheId = 'hook_plugin_list';

        if (!$use_cache || !CacheApi::isStored($cacheId)) {

            $results = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS('
                SELECT h.id_hook, h.name AS h_name, hl.title, hl.description, h.position, h.live_edit, hm.position AS hm_position, m.id_plugin, m.name, m.active
                FROM `' . _DB_PREFIX_ . 'hook_plugin` hm
                STRAIGHT_JOIN `' . _DB_PREFIX_ . 'hook` h ON (h.id_hook = hm.id_hook)
                STRAIGHT_JOIN `' . _DB_PREFIX_ . 'hook_lang` hl ON (hl.id_hook = h.id_hook AND hl.id_lang = ' . (int) Context::getContext()->language->id . ')
                STRAIGHT_JOIN `' . _DB_PREFIX_ . 'plugin` AS m ON (m.id_plugin = hm.id_plugin)
                ORDER BY hm.position'
            );
            $list = [];

            foreach ($results as $result) {

                if (!isset($list[$result['id_hook']])) {
                    $list[$result['id_hook']] = [];
                }

                $list[$result['id_hook']][$result['id_plugin']] = [
                    'id_hook'     => $result['id_hook'],
                    'title'       => $result['title'],
                    'description' => $result['description'],
                    'hm.position' => $result['position'],
                    'live_edit'   => $result['live_edit'],
                    'm.position'  => $result['hm_position'],
                    'id_plugin'   => $result['id_plugin'],
                    'name'        => $result['name'],
                    'active'      => $result['active'],
                ];
            }

            CacheApi::store($cacheId, $list);

            return $list;
        }

        return CacheApi::retrieve($cacheId);
    }

    public function exec(
        $hookName,
        $hookArgs = [],
        $idPlugin = null,
        $arrayReturn = false,
        $checkExceptions = true,
        $usePush = false,
        $objectReturn = false
    ) {

        if (!isset($hookArgs['cookie']) || !$hookArgs['cookie']) {
            $hookArgs['cookie'] = $this->context->cookie;
        }

        if (is_array($this->args)) {

            foreach ($this->args as $key => $value) {
                $hookArgs[$key] = $this->context->$key;
            }

        }

        if (!Configuration::get('EPH_PAGE_CACHE_ENABLED')) {

            return $this->execWithoutCache($hookName, $hookArgs, $idPlugin, $arrayReturn, $checkExceptions, $usePush, $objectReturn);
        }

        if (!$pluginList = $this->getHookPluginExecList($hookName)) {
            return '';
        }

        if ($arrayReturn) {

            $return = [];
        } else {
            $return = '';
        }

        if (!$idPlugin) {
            $cachedHooks = $this->getCachedHooks();

            foreach ($pluginList as $m) {
                $idPlugin = (int) $m['id_plugin'];
                $data = $this->execWithoutCache($hookName, $hookArgs, $idPlugin, $arrayReturn, $checkExceptions, $usePush, $objectReturn);

                if (is_array($data)) {
                    $data = array_shift($data);
                }

                if (is_array($data)) {
                    $return[$m['plugin']] = $data;
                } else {
                    $idHook = (int) $this->getIdByName($hookName);

                    if (isset($cachedHooks[$idPlugin][$idHook])) {
                        $dataWrapped = $data;
                    } else {

                        $dataWrapped = $data;
                    }

                    if ($arrayReturn) {
                        $return[$m['plugin']] = $dataWrapped;
                    } else
                    if ($objectReturn) {
                        $return = $dataWrapped;
                    } else {
                        $return .= $dataWrapped;
                    }

                }

            }

        } else {
            $return = static::execWithoutCache($hookName, $hookArgs, $idPlugin, $arrayReturn, $checkExceptions, $usePush, $objectReturn);
        }

        return $return;
    }

    public function execWithoutCache(
        $hookName,
        $hookArgs = [],
        $idPlugin = null,
        $arrayReturn = false,
        $checkExceptions = true,
        $usePush = false,
        $objectReturn = false
    ) {

        if (defined('EPH_INSTALLATION_IN_PROGRESS')) {

            return;
        }

        static $disableNonNativePlugins = null;

        if ($disableNonNativePlugins === null) {
            $disableNonNativePlugins = (bool) Configuration::get('EPH_DISABLE_NON_NATIVE_PLUGIN');
        }

        if (($idPlugin && !is_numeric((int) $idPlugin)) || !Validate::isHookName($hookName)) {

            throw new PhenyxException('Invalid id_plugin or hook_name');
        }

        if (!$pluginList = $this->getHookPluginExecList($hookName)) {

            return '';
        }

        if (!$idHook = $this->getIdByName($hookName)) {

            return false;
        }

        Hook::$executed_hooks[$idHook] = $hookName;

        $retroHookName = $this->getRetroHookName($hookName);

        $altern = 0;

        if ($arrayReturn) {
            $output = [];
        } else {
            $output = '';
        }

        if ($disableNonNativePlugins && !isset(Hook::$native_plugin)) {
            Hook::$native_plugin = Plugin::getNativePluginList();
        }

        foreach ($pluginList as $array) {

            if ($idPlugin && $idPlugin != $array['id_plugin']) {
                continue;
            }

            if ((bool) $disableNonNativePlugins && Hook::$native_plugin && count(Hook::$native_plugin) && !in_array($array['plugin'], Hook::$native_plugin)) {

                continue;
            }

            if ($checkExceptions) {
                $exceptions = Plugin::getExceptionsStatic($array['id_plugin'], $array['id_hook'], $this->context);

                $controller = Performer::getInstance()->getController();
                $controllerObj = $this->context->controller;

                if (isset($controllerObj->plugin) && Validate::isLoadedObject($controllerObj->plugin)) {
                    $controller = 'plugin-' . $controllerObj->plugin->name . '-' . $controller;
                }

                if (is_array($exceptions) && in_array($controller, $exceptions)) {

                    continue;
                }

                $matchingName = [
                    'authentication'     => 'auth',
                    'productscomparison' => 'compare',
                ];

                if (isset($matchingName[$controller]) && in_array($matchingName[$controller], $exceptions)) {

                    continue;
                }

                if (Validate::isLoadedObject($this->context->employee) && !Plugin::getPermissionStatic($array['id_plugin'], 'view', $this->context->employee)) {

                    continue;
                }

            }

            if (!($pluginInstance = Plugin::getInstanceByName($array['plugin']))) {

                continue;
            }

            if ($usePush && !$pluginInstance->allow_push) {

                continue;
            }

            $hookCallable = is_callable([$pluginInstance, 'hook' . $hookName]);
            $hookRetroCallable = is_callable([$pluginInstance, 'hook' . $retroHookName]);

            if (($hookCallable || $hookRetroCallable) && Plugin::preCall($pluginInstance->name)) {

                $hookArgs['altern'] = ++$altern;

                if ($usePush && isset($pluginInstance->push_filename) && file_exists($pluginInstance->push_filename)) {
                    Tools::waitUntilFileIsModified($pluginInstance->push_filename, $pluginInstance->push_time_limit);
                }

                if ($hookCallable) {

                    $display = $this->coreCallHook($pluginInstance, 'hook' . $hookName, $hookArgs);
                } else
                if ($hookRetroCallable) {

                    $display = $this->coreCallHook($pluginInstance, 'hook' . $retroHookName, $hookArgs);
                }

                if ($arrayReturn) {
                    $output[$pluginInstance->name] = $display;
                } else
                if ($objectReturn) {
                    $return = $display;
                } else {
                    $output = $display;
                }

            }

        }

        return $output;
    }

    public function getHookPluginExecList($hookName = null) {

        $context = Context::getContext();
        $list = null;

        if ($this->context->cache_enable && is_object($this->context->cache_api)) {
            $cacheId = 'hook_plugin_exec_list_' . $hookName . ((isset($this->context->user->id)) ? '_' . $this->context->user->id : '');
            $value = $this->context->cache_api->getData($cacheId, 3600);
            $temp = empty($value) ? null : Tools::jsonDecode($value, true);

            if (!empty($temp)) {
                $list = $temp;
            }

        }

        if (is_null($list) || $hookName == 'displayPayment' || $hookName == 'displayPaymentEU') {
            $frontend = true;
            $groups = [];
            $useGroups = Group::isFeatureActive();

            if (isset($context->employee)) {
                $frontend = false;
            } else {
                // Get groups list

                if ($useGroups) {

                    if (isset($this->context->user) && $this->context->user->isLogged()) {
                        $groups = $context->user->getGroups();
                    } else
                    if (isset($this->context->user) && $this->context->user->isLogged(true)) {
                        $groups = [(int) Configuration::get('EPH_GUEST_GROUP')];
                    } else {
                        $groups = [(int) Configuration::get('EPH_UNIDENTIFIED_GROUP')];
                    }

                }

            }

            // SQL Request
            $sql = new DbQuery();
            $sql->select('h.`name` as hook, m.`id_plugin`, h.`id_hook`, m.`name` as plugin, h.`live_edit`, hm.`position`');
            $sql->from('plugin', 'm');
            $sql->innerJoin('hook_plugin', 'hm', 'hm.`id_plugin` = m.`id_plugin`');
            $sql->innerJoin('hook', 'h', 'hm.`id_hook` = h.`id_hook`');
            $sql->where('m.enable_device & ' . (int) Context::getContext()->getDevice());
            $sql->where('m.active = 1');

            if ($hookName != 'displayPayment' && $hookName != 'displayPaymentEU') {
                $sql->where('h.`name` != "displayPayment" AND h.`name` != "displayPaymentEU"');
            }

            // For payment plugins, we check that they are available in the contextual country
            else
            if ($frontend) {

                if (Validate::isLoadedObject($this->context->country)) {
                    $sql->where('((h.`name` = "displayPayment" OR h.`name` = "displayPaymentEU") AND (SELECT `id_country` FROM `' . _DB_PREFIX_ . 'plugin_country` mc WHERE mc.`id_plugin` = m.`id_plugin` AND `id_country` = ' . (int) $context->country->id . '  LIMIT 1) = ' . (int) $this->context->country->id . ')');
                }

                if (Validate::isLoadedObject($this->context->currency)) {
                    $sql->where('((h.`name` = "displayPayment" OR h.`name` = "displayPaymentEU") AND (SELECT `id_currency` FROM `' . _DB_PREFIX_ . 'plugin_currency` mcr WHERE mcr.`id_plugin` = m.`id_plugin` AND `id_currency` IN (' . (int) $context->currency->id . ', -1, -2) LIMIT 1) IN (' . (int) $this->context->currency->id . ', -1, -2))');
                }

                if (Validate::isLoadedObject($this->context->cart)) {
                    $carrier = new Carrier($this->context->cart->id_carrier);

                    if (Validate::isLoadedObject($carrier)) {
                        $sql->where('((h.`name` = "displayPayment" OR h.`name` = "displayPaymentEU") AND (SELECT `id_reference` FROM `' . _DB_PREFIX_ . 'plugin_carrier` mcar WHERE mcar.`id_plugin` = m.`id_plugin` AND `id_reference` = ' . (int) $carrier->id_reference . ' LIMIT 1) = ' . (int) $carrier->id_reference . ')');
                    }

                }

            }

            if ($frontend) {

                if ($useGroups) {
                    $sql->leftJoin('plugin_group', 'mg', 'mg.`id_plugin` = m.`id_plugin`');

                    $sql->where('mg.`id_group` IN (' . implode(', ', $groups) . ')');

                }

            }

            $sql->groupBy('hm.id_hook, hm.id_plugin');
            $sql->orderBy('hm.`position`');

            $list = [];

            if ($result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS($sql)) {

                foreach ($result as $row) {
                    $row['hook'] = strtolower($row['hook']);

                    if (!isset($list[$row['hook']])) {
                        $list[$row['hook']] = [];
                    }

                    $list[$row['hook']][] = [
                        'id_hook'   => $row['id_hook'],
                        'plugin'    => $row['plugin'],
                        'id_plugin' => $row['id_plugin'],
                        'position'  => $row['position'],
                        'live_edit' => $row['live_edit'],
                    ];
                }

            }

            if ($this->context->cache_enable && is_object($this->context->cache_api)) {
                $temp = $list === null ? null : Tools::jsonEncode($list);
                $this->context->cache_api->putData($cacheId, $temp);
            }

        }

        // If hook_name is given, just get list of plugins for this hook

        if (!is_null($hookName) && !is_object($hookName)) {
            $retroHookName = strtolower($this->getRetroHookName($hookName));
            $hookName = strtolower($hookName);

            $return = [];
            $insertedPlugins = [];

            if (isset($list[$hookName])) {
                $return = $list[$hookName];
            }

            foreach ($return as $plugin) {
                $insertedPlugins[] = $plugin['id_plugin'];
            }

            if (isset($list[$retroHookName])) {

                foreach ($list[$retroHookName] as $retroPluginCall) {

                    if (!in_array($retroPluginCall['id_plugin'], $insertedPlugins)) {
                        $return[] = $retroPluginCall;
                    }

                }

            }

            return (count($return) > 0 ? $return : false);
        } else {
            return $list;
        }

    }

    public function getRetroHookName($hookName) {

        $aliasList = $this->getHookAliasList();

        if (isset($aliasList[strtolower($hookName)])) {
            return $aliasList[strtolower($hookName)];
        }

        $retroHookName = array_search($hookName, $aliasList);

        if ($retroHookName === false) {
            return '';
        }

        return $retroHookName;
    }

    public function getHookAliasList() {

        $cacheId = 'hook_alias';

        if (!CacheApi::isStored($cacheId)) {
            $hookAliasList = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'hook_alias`');
            $hookAlias = [];

            if ($hookAliasList) {

                foreach ($hookAliasList as $ha) {
                    $hookAlias[strtolower($ha['alias'])] = $ha['name'];
                }

            }

            CacheApi::store($cacheId, $hookAlias);

            return $hookAlias;
        }

        return CacheApi::retrieve($cacheId);
    }

    public function getIdByName($hookName) {

        $hookName = strtolower($hookName);

        if (!Validate::isHookName($hookName)) {
            return false;
        }

        $cacheId = 'hook_idsbyname';

        if (!CacheApi::isStored($cacheId)) {
            // Get all hook ID by name and alias
            $hookIds = [];
            $db = Db::getInstance(_EPH_USE_SQL_SLAVE_);
            $result = $db->executeS(
                '
            SELECT `id_hook`, `name`
            FROM `' . _DB_PREFIX_ . 'hook`
            UNION
            SELECT `id_hook`, ha.`alias` AS name
            FROM `' . _DB_PREFIX_ . 'hook_alias` ha
            INNER JOIN `' . _DB_PREFIX_ . 'hook` h ON ha.name = h.name', false
            );

            while ($row = $db->nextRow($result)) {
                $hookIds[strtolower($row['name'])] = $row['id_hook'];
            }

            CacheApi::store($cacheId, $hookIds);
        } else {
            $hookIds = CacheApi::retrieve($cacheId);
        }

        return (isset($hookIds[$hookName]) ? $hookIds[$hookName] : false);
    }

    public function coreCallHook($plugin, $method, $params) {

        // Define if we will log plugins performances for this session

        if (Plugin::$_log_plugins_perfs === null) {
            $modulo = _EPH_DEBUG_PROFILING_ ? 1 : Configuration::get('EPH_log_plugins_perfs_MODULO');
            Plugin::$_log_plugins_perfs = ($modulo && mt_rand(0, $modulo - 1) == 0);

            if (Plugin::$_log_plugins_perfs) {
                Plugin::$_log_plugins_perfs_session = mt_rand();
            }

        }

        // Immediately return the result if we do not log performances

        if (!Plugin::$_log_plugins_perfs) {
            return $plugin->{$method}

            ($params);
        }

        // Store time and memory before and after hook call and save the result in the database

        // Call hook
        $r = $plugin->{$method}

        ($params);

        return $r;
    }

    public function getCachedHooks() {

        $hookSettings = json_decode(Configuration::get('EPH_PAGE_CACHE_HOOKS'), true);

        if (!is_array($hookSettings)) {
            return [];
        }

        $cachedHooks = [];

        foreach ($hookSettings as $idPlugin => $hookArr) {
            $idPlugin = (int) $idPlugin;

            if ($idPlugin) {
                $pluginHooks = [];

                foreach ($hookArr as $idHook => $bool) {
                    $idHook = (int) $idHook;

                    if ($idHook && $bool) {
                        $pluginHooks[$idHook] = 1;
                    }

                }

                if ($pluginHooks) {
                    $cachedHooks[$idPlugin] = $pluginHooks;
                }

            }

        }

        return $cachedHooks;
    }

}
