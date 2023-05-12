<?php

/**
 * Class PageCache
 *
 * @since 1.9.1.0
 * @since 1.0.1 Overridable
 */
class PageCache {

    /**
     * How many seconds should the page remain in cache
     */
    const CACHE_ENTRY_TTL = 86400;

    /**
     * Returns true if full page cache is enabled
     *
     * @since: 1.0.7
     */
    public static function isEnabled() {

        return Cache::isEnabled() && (bool) Configuration::get('EPH_PAGE_CACHE_ENABLED');
    }

    /**
     * Insert new entry for current request into full page cache
     *
     * @param string $template
     *
     * @since 1.0.7
     */
    public static function set($template) {

        if (static::isEnabled()) {
            $key = PageCacheKey::get();

            if ($key) {
                $hash = $key->getHash();
                $cache = Cache::getInstance();
                $cache->set($hash, $template, static::CACHE_ENTRY_TTL);
                static::cacheKey($hash, $key->idCurrency, $key->idLanguage, $key->idCountry, $key->entityType, $key->entityId);
            }

        }

    }

    /**
     * Returns full page cache entry for current request
     *
     * @return string | null
     *
     * @since 1.0.7
     */
    public static function get() {

        if (static::isEnabled()) {

            // check that there were no changes to hook list
            $hookListHash = static::getHookListFingerprint();

            if ($hookListHash != Configuration::get('EPH_HOOK_LIST_HASH')) {
                // drain the cache if the hook list changed
                Configuration::updateValue('EPH_HOOK_LIST_HASH', $hookListHash);
                static::flush();
                return null;
            }

            $key = PageCacheKey::get();

            if ($key) {
                $cache = Cache::getInstance();

                return $cache->get($key->getHash());
            }

        }

        return null;
    }

    /**
     * Register cache key and set its metadata
     *
     * @param string $key
     * @param int    $idCurrency
     * @param int    $idLanguage
     * @param int    $idCountry
     * @param int    $idCompany
     * @param string $entityType
     * @param int    $idEntity
     *
     * @since 1.9.1.0
     */
     public static function cacheKey($key, $idCurrency, $idLanguage, $idCountry,  $entityType, $idEntity) {

        try {
            Db::getInstance()->insert(
                'page_cache',
                [
                    'cache_hash'  => pSQL($key),
                    'id_currency' => (int) $idCurrency,
                    'id_language' => (int) $idLanguage,
                    'id_country'  => (int) $idCountry,
                    'entity_type' => pSQL($entityType),
                    'id_entity'   => (int) $idEntity,
                ],
                false,
                true,
                Db::ON_DUPLICATE_KEY
            );
        } catch (Exception $e) {
            // Hash already inserted
        }

    }

    /**
     * Invalidate an entity from the cache
     *
     * @param string   $entityType
     * @param int|null $idEntity
     *
     * @since 1.9.1.0
     * @throws PhenyxDatabaseExceptionException
     * @throws PhenyxException
     */
    public static function invalidateEntity($entityType, $idEntity = null) {

        $keysToInvalidate = [];

       
        $keysToInvalidate = array_merge(
            $keysToInvalidate,
            static::getKeysToInvalidate($entityType, $idEntity)
        );
        Db::getInstance()->delete(
            'page_cache',
            '`entity_type` = \'' . pSQL($entityType) . '\'' . ($idEntity ? ' AND `id_entity` = ' . (int) $idEntity : '')
        );

        $cache = Cache::getInstance();

        foreach ($keysToInvalidate as $item) {
            $cache->delete($item);
        }

    }

    /**
     * Flush all data
     *
     * @since 1.9.1.0
     * @throws PhenyxDatabaseExceptionException
     * @throws PhenyxException
     */
    public static function flush() {

        if (static::isEnabled()) {
            Cache::getInstance()->flush();
        }

        Db::getInstance()->delete('page_cache');
    }

    /**
     * Get keys to invalidate
     *
     * @param string   $entityType
     * @param int|null $idEntity
     *
     * @return array
     *
     * @throws PhenyxDatabaseExceptionException
     * @throws PhenyxException
     * @since 1.9.1.0
     */
    protected static function getKeysToInvalidate($entityType, $idEntity = null) {

        $sql = new DbQuery();
        $sql->select('`cache_hash`');
        $sql->from('page_cache');
        $sql->where('`entity_type` = \'' . pSQL($entityType) . '\'');

        if ($idEntity) {
            $sql->where('`id_entity` = ' . (int) $idEntity);
        }

        $results = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS($sql);

        if (!is_array($results)) {
            return [];
        }

        return array_column($results, 'cache_hash');
    }

    /**
     * Return normalized list of all hooks that should be cached
     */
    public static function getCachedHooks() {

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

    /**
     * Modify hook cached status
     *
     * If $status is true, hook output will be cached. Otherwise content of
     * this hook will be refreshed with every page load
     *
     * @param int $idPlugin
     * @param int $idHook
     * @param bool $status
     */
    public static function setHookCacheStatus($idPlugin, $idHook, $status) {

        $hookSettings = static::getCachedHooks();
        $idPlugin = (int) $idPlugin;
        $idHook = (int) $idHook;

        if (!isset($hookSettings[$idPlugin])) {
            $hookSettings[$idPlugin] = [];
        }

        if ($status) {
            $hookSettings[$idPlugin][$idHook] = 1;
        } else {
            unset($hookSettings[$idPlugin][$idHook]);

            if (empty($hookSettings[$idPlugin])) {
                unset($hookSettings[$idPlugin]);
            }

        }

        if (Configuration::updateGlobalValue('EPH_PAGE_CACHE_HOOKS', json_encode($hookSettings))) {
            static::flush();

            return true;
        }

        return false;
    }

   
    public static function getHookListFingerprint() {

        $hookList = Hook::getHookPluginList();
        $ctx = hash_init('md5');

        foreach ($hookList as $idHook => $pluginList) {
            hash_update($ctx, $idHook);

            foreach ($pluginList as $idPlugin => $pluginInfo) {
                hash_update($ctx, $idPlugin);
                hash_update($ctx, $pluginInfo['active']);
            }

        }

        return hash_final($ctx);
    }

}
