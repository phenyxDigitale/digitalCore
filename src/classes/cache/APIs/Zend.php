<?php

namespace EPH\Cache\APIs;

use EPH\Cache\CacheApi;
use EPH\Cache\CacheApiInterface;

/**
 * Our Cache API class
 *
 * @package CacheAPI
 */
class Zend extends CacheApi implements CacheApiInterface {

	/**
	 * {@inheritDoc}
	 */
	public function isSupported($test = false) {

		$supported = function_exists('zend_shm_cache_fetch') || function_exists('output_cache_get');

		if ($test) {
			return $supported;
		}

		return parent::isSupported() && $supported;
	}

	public function connect() {

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getData($key, $ttl = null) {

		$key = $this->prefix . strtr($key, ':/', '-_');

		// Zend's pricey stuff.

		if (function_exists('zend_shm_cache_fetch')) {
			return zend_shm_cache_fetch('EPH::' . $key);
		} else if (function_exists('output_cache_get')) {
			return output_cache_get($key, $ttl);
		}

	}

	/**
	 * {@inheritDoc}
	 */
	public function putData($key, $value, $ttl = null) {

		$key = $this->prefix . strtr($key, ':/', '-_');

		if (function_exists('zend_shm_cache_store')) {
			return zend_shm_cache_store('EPH::' . $key, $value, $ttl);
		} else if (function_exists('output_cache_put')) {
			return output_cache_put($key, $value);
		}

	}

	/**
	 * {@inheritDoc}
	 */
	public function cleanCache($type = '') {

		$this->invalidateCache();

		return zend_shm_cache_clear('EPH');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVersion() {

		return zend_version();
	}

}

?>