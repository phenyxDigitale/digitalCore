<?php


abstract class CacheApi {

	const APIS_FOLDER = 'APIs';
	const APIS_DEFAULT = 'FileBased';

	/**
	 * @var string The maximum SMF version that this will work with.
	 */
	protected $version_compatible = _EPH_VERSION_;

	/**
	 * @var string The minimum SMF version that this will work with.
	 */
	protected $min_eph_version = _EPH_VERSION_;

	/**
	 * @var string The prefix for all keys.
	 */
	protected $prefix = '';

	/**
	 * @var int The default TTL.
	 */
	protected $ttl = 120;
    
    public $boardurl;
    
    public $cachedir;
    
    public $boarddir;

	/**
	 * Does basic setup of a cache method when we create the object but before we call connect.
	 *
	 * @access public
	 */
	public function __construct() {

        $this->boardurl = Context::getContext()->link->getBaseLink();
        $this->cachedir = _EPH_CACHE_DIR_;
        $this->boarddir = _EPH_ROOT_DIR_;
		$this->setPrefix();
	}

	/**
	 * Checks whether we can use the cache method performed by this API.
	 *
	 * @access public
	 * @param bool $test Test if this is supported or enabled.
	 * @return bool Whether or not the cache is supported
	 */
	public function isSupported($test = false) {

		global $cache_enable;

		if ($test) {
			return true;
		}

		return !empty($cache_enable);
	}

	/**
	 * Sets the cache prefix.
	 *
	 * @access public
	 * @param string $prefix The prefix to use.
	 *     If empty, the prefix will be generated automatically.
	 * @return bool If this was successful or not.
	 */
	public function setPrefix($prefix = '') {

		if (!is_string($prefix)) {
			$prefix = '';
		}

		// Use the supplied prefix, if there is one.

		if (!empty($prefix)) {
			$this->prefix = $prefix;

			return true;
		}

		// Ideally the prefix should reflect the last time the cache was reset.

		if (file_exists($this->cachedir . 'index.php')) {
			$mtime = filemtime($this->cachedir . 'index.php');
		}

		// Fall back to the last time that Settings.php was updated.
		else if (file_exists(_EPH_CONFIG_DIR_ . 'settings.inc.php')) {
			$mtime = filemtime(_EPH_CONFIG_DIR_ . 'settings.inc.php');
		}

		// This should never happen, but just in case...
		else {
			$mtime = filemtime(realpath($_SERVER['SCRIPT_FILENAME']));
		}

		$this->prefix = md5($this->boardurl . $mtime) . '-EPH-';

		return true;
	}

	/**
	 * Gets the prefix as defined from set or the default.
	 *
	 * @access public
	 * @return string the value of $key.
	 */
	public function getPrefix() {

		return $this->prefix;
	}

	/**
	 * Sets a default Time To Live, if this isn't specified we let the class define it.
	 *
	 * @access public
	 * @param int $ttl The default TTL
	 * @return bool If this was successful or not.
	 */
	public function setDefaultTTL($ttl = 120) {

		$this->ttl = $ttl;

		return true;
	}

	/**
	 * Gets the TTL as defined from set or the default.
	 *
	 * @access public
	 * @return int the value of $ttl.
	 */
	public function getDefaultTTL() {

		return $this->ttl;
	}

	/**
	 * Invalidate all cached data.
	 *
	 * @return bool Whether or not we could invalidate the cache.
	 */
	public function invalidateCache() {

		if (is_writable($this->cachedir . '/' . 'index.php')) {
			@touch($this->cachedir . '/' . 'index.php');
		}

		return true;
	}

	/**
	 * Closes connections to the cache method.
	 *
	 * @access public
	 * @return bool Whether the connections were closed.
	 */
	public function quit() {

		return true;
	}

	/**
	 * Specify custom settings that the cache API supports.
	 *
	 * @access public
	 * @param array $config_vars Additional config_vars, see ManageSettings.php for usage.
	 */
	public function cacheSettings(array &$config_vars) {}

	/**
	 * Gets the latest version of SMF this is compatible with.
	 *
	 * @access public
	 * @return string the value of $key.
	 */
	public function getCompatibleVersion() {

		return $this->version_compatible;
	}

	/**
	 * Gets the min version that we support.
	 *
	 * @access public
	 * @return string the value of $key.
	 */
	public function getMinimumVersion() {

		return $this->min_eph_version;
	}

	/**
	 * Gets the Version of the Caching API.
	 *
	 * @access public
	 * @return string the value of $key.
	 */
	public function getVersion() {

		return $this->min_eph_version;
	}

	/**
	 * Run housekeeping of this cache
	 * exp. clean up old data or do optimization
	 *
	 * @access public
	 * @return void
	 */
	public function housekeeping() {}

	/**
	 * Gets the class identifier of the current caching API implementation.
	 *
	 * @access public
	 * @return string the unique identifier for the current class implementation.
	 */
	public function getImplementationClassKeyName() {

		$class_name = get_class($this);

		if ($position = strrpos($class_name, '\\')) {
			return substr($class_name, $position + 1);
		} else {
			return get_class($this);
		}

	}

}

?>