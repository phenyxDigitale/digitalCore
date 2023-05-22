<?php

/**
 * Class CacheMemcached
 *
 * @since 1.0.0
 */
class CacheMemcached extends Cache {

    /**
     * @var Memcached
     */
    protected $memcached;

    /**
     * @var bool Connection status
     */
    protected $is_connected = false;

    /**
     * CacheMemcachedCore constructor.
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public function __construct() {

        $this->connect();

        if ($this->is_connected) {
            $this->memcached->setOption(Memcached::OPT_PREFIX_KEY, _DB_PREFIX_);

            if ($this->memcached->getOption(Memcached::HAVE_IGBINARY)) {
                $this->memcached->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
            }

        }

    }

    /**
     * CacheMemcachedCore destructor.
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public function __destruct() {

        $this->close();
    }

    /**
     * Connect to memcached server
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public function connect() {

        if (class_exists('Memcached') && extension_loaded('memcached')) {
            $this->memcached = new Memcached();
        } else {
            return;
        }

        $servers = static::getMemcachedServers();

        if (!$servers) {
            return;
        }

        foreach ($servers as $server) {
            $this->memcached->addServer($server['ip'], $server['port'], (int) $server['weight']);
        }

        $this->is_connected = in_array('255.255.255', $this->memcached->getVersion(), true) === false;
    }

    /**
     * @see Cache::_set()
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    protected function _set($key, $value, $ttl = 0) {

        if (!$this->is_connected) {
            return false;
        }

        return $this->memcached->set($key, $value, $ttl);
    }

    /**
     * @see Cache::_get()
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    protected function _get($key) {

        if (!$this->is_connected) {
            return false;
        }

        return $this->memcached->get($key);
    }

    /**
     * @see Cache::_exists()
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    protected function _exists($key) {

        if (!$this->is_connected) {
            return false;
        }

        return ($this->memcached->get($key) !== false);
    }

    /**
     * @see Cache::_delete()
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    protected function _delete($key) {

        if (!$this->is_connected) {
            return false;
        }

        return $this->memcached->delete($key);
    }

    /**
     * @see Cache::_writeKeys()
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    protected function _writeKeys() {

        if (!$this->is_connected) {
            return false;
        }

        return true;
    }

    /**
     * @see Cache::flush()
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public function flush() {

        if (!$this->is_connected) {
            return false;
        }

        return $this->memcached->flush();
    }

    /**
     * Store a data in cache
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $ttl
     *
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public function set($key, $value, $ttl = 0) {

        return $this->_set($key, $value, $ttl);
    }

    /**
     * Retrieve a data from cache
     *
     * @param string $key
     *
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public function get($key) {

        return $this->_get($key);
    }

    /**
     * Check if a data is cached
     *
     * @param string $key
     *
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public function exists($key) {

        return $this->_exists($key);
    }

    /**
     * Delete one or several data from cache (* joker can be used, but avoid it !)
     *    E.g.: delete('*'); delete('my_prefix_*'); delete('my_key_name');
     *
     * @param string $key
     *
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public function delete($key) {

        if ($key == '*') {
            $this->flush();
        } else if (strpos($key, '*') === false) {
            $this->_delete($key);
        } else {
            $pattern = str_replace('\\*', '.*', preg_quote($key));
            $keys = $this->memcached->getAllKeys();

            foreach ($keys as $key => $data) {

                if (preg_match('#^' . $pattern . '$#', $key)) {
                    $this->_delete($key);
                }

            }

        }

        return true;
    }

    /**
     * Close connection to memcache server
     *
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    protected function close() {

        if (!$this->is_connected) {
            return false;
        }

        return $this->memcached->quit();
    }

    /**
     * Add a memcached server
     *
     * @param string $ip
     * @param int    $port
     * @param int    $weight
     *
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public static function addServer($ip, $port, $weight) {

        return Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'memcached_servers (ip, port, weight) VALUES(\'' . pSQL($ip) . '\', ' . (int) $port . ', ' . (int) $weight . ')', false);
    }

    /**
     * Get list of memcached servers
     *
     * @return array
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public static function getMemcachedServers() {

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'memcached_servers', true, false);
    }

    /**
     * Delete a memcached server
     *
     * @param int $id_server
     *
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public static function deleteServer($id_server) {

        return Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'memcached_servers WHERE id_memcached_server=' . (int) $id_server);
    }

}
