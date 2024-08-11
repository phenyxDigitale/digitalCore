<?php

/**
 * Class Translate
 *
 * @since 1.8.1.0
 */
class Translate {

    protected static $_plugins = [];

    protected static $_language;

    protected static $_context;

    public static function getListPlugins($iso) {

        $plugs = [];
        $plugins = Plugin::getPluginsDirOnDisk();

        foreach ($plugins as $plugin) {

            if (Plugin::isInstalled($plugin)) {

                if (is_dir(_EPH_PLUGIN_DIR_ . $plugin . '/translations/' . $iso)) {

                    if (is_dir(_EPH_PLUGIN_DIR_ . $plugin . '/translations/' . $iso)) {
                        $plugs[] = $plugin;
                    }

                }

            }

        }

        return $plugs;
    }

    public static function getAdminTranslation($string, $class = 'Phenyx', $addslashes = false, $htmlentities = true, $sprintf = null, $context = null) {

        $file = fopen("testgetAdminTranslation.txt", "w");

        if (empty(static::$_language)) {
            static::$_language = Context::getContext()->language;
        }

        if (empty(static::$_context)) {
            static::$_context = Context::getContext();
        }

        $iso = static::$_language->iso_code;
        $string = str_replace('"', '`', $string);
        //$_LANGADMS = [];
        global $_LANGADMS, $_LANGADM, $_LANGOVADM;

        if (empty($_LANGADMS)) {
            $_LANGADMS = [];

            if (file_exists(_EPH_TRANSLATIONS_DIR_ . $iso . '/admin.php')) {
                include_once _EPH_TRANSLATIONS_DIR_ . $iso . '/admin.php';

                if (is_array($_LANGADM)) {
                    $_LANGADMS = array_merge(
                        $_LANGADMS,
                        $_LANGADM
                    );
                }

            }

        }

        if (file_exists(_EPH_OVERRIDE_TRANSLATIONS_DIR_ . $iso . '/admin.php')) {

            include_once _EPH_OVERRIDE_TRANSLATIONS_DIR_ . $iso . '/admin.php';
        }

        fwrite($file, print_r($_LANGADMS, true));

        Hook::exec('actionAdminTranslate', ['iso' => $iso]);
        $string = preg_replace("/\\\*'/", "\'", $string);

        $key = md5($string);

        if (isset($_LANGADMS[$class . $key])) {

            $str = $_LANGADMS[$class . $key];

        } else

        if (isset($_LANGADM['Phenyx' . $key])) {

            $str = $_LANGADM['Phenyx' . $key];

        } else

        if (isset($_LANGOVADM[$class . $key])) {

            $str = $_LANGOVADM[$class . $key];

        } else {
            $str = Translate::getGenericAdminTranslation($string, $_LANGADM, $key);
        }

        if ($htmlentities) {
            $str = htmlspecialchars($str, ENT_QUOTES, 'utf-8');
        }

        $str = str_replace('"', '&quot;', $str);

        if ($sprintf !== null) {
            $str = Translate::checkAndReplaceArgs($str, $sprintf);
        }

        return ($addslashes ? addslashes($str) : stripslashes($str));
    }

    public static function getFrontTranslation($string, $class, $addslashes = false, $htmlentities = true, $sprintf = null, $context = null) {

        if (empty(static::$_language)) {
            static::$_language = Context::getContext()->language;
        }

        if (empty(static::$_context)) {
            static::$_context = Context::getContext();
        }

        $string = str_replace('"', '`', $string);
        $iso = static::$_language->iso_code;

        global $_LANGFRONTS, $_LANGFRONT, $_LANGOVFRONT;

        if (empty($_LANGFRONTS)) {
            $_LANGFRONTS = [];

            if (file_exists(_EPH_TRANSLATIONS_DIR_ . $iso . '/front.php')) {
                include_once _EPH_TRANSLATIONS_DIR_ . $iso . '/front.php';

                if (is_array($_LANGFRONT)) {
                    $_LANGFRONTS = array_merge(
                        $_LANGFRONTS,
                        $_LANGFRONT
                    );
                }

            }

            Hook::exec('actionFrontTranslate', ['iso' => $iso]);

            if (file_exists(_EPH_OVERRIDE_TRANSLATIONS_DIR_ . $iso . '/front.php')) {

                include_once _EPH_OVERRIDE_TRANSLATIONS_DIR_ . $iso . '/front.php';
            }

        }

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);

        if (isset($_LANGFRONTS[$class . $key])) {
            $str = $_LANGFRONTS[$class . $key];
        } else

        if (isset($_LANGOVFRONT[$class . $key])) {
            $str = $_LANGFRONT[$class . $key];
        } else {
            $str = Translate::getGenericFrontTranslation($string, $_LANGOVFRONT, $key);
        }

        if ($htmlentities) {
            $str = htmlspecialchars($str, ENT_QUOTES, 'utf-8');
        }

        $str = str_replace('"', '&quot;', $str);

        if ($sprintf !== null) {
            $str = Translate::checkAndReplaceArgs($str, $sprintf);
        }

        return ($addslashes ? addslashes($str) : stripslashes($str));
    }

    public static function getClassTranslation($string, $class, $addslashes = false, $htmlentities = true, $sprintf = null, $context = null) {

        if (empty(static::$_language)) {
            static::$_language = Context::getContext()->language;
        }

        $string = str_replace('"', '`', $string);
        $iso = static::$_language->iso_code;

        global $_LANGCLASSS, $_LANGCLASS, $_LANGOVCLASS;

        if (is_null($string)) {
            return $string;
        }

        if (empty($_LANGCLASSS == null)) {
            $_LANGCLASSS = [];

            if (file_exists(_EPH_TRANSLATIONS_DIR_ . $iso . '/class.php')) {
                include_once _EPH_TRANSLATIONS_DIR_ . $iso . '/class.php';

                if (is_array($_LANGCLASS)) {
                    $_LANGCLASSS = array_merge(
                        $_LANGCLASSS,
                        $_LANGCLASS
                    );
                }

            }

            //Hook::exec('actionClassTranslate', ['iso' => $iso]);

            if (file_exists(_EPH_OVERRIDE_TRANSLATIONS_DIR_ . $iso . '/class.php')) {

                include_once _EPH_OVERRIDE_TRANSLATIONS_DIR_ . $iso . '/class.php';
            }

        }

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);

        if (isset($_LANGCLASSS[$class . $key])) {

            $str = $_LANGCLASSS[$class . $key];

        } else

        if (isset($_LANGOVCLASS[$class . $key])) {

            $str = $_LANGOVCLASS[$class . $key];

        } else {
            $str = Translate::getGenericFrontTranslation($string, $_LANGCLASS, $key);
        }

        if ($htmlentities) {
            $str = htmlspecialchars($str, ENT_QUOTES, 'utf-8');
        }

        $str = str_replace('"', '&quot;', $str);

        if ($sprintf !== null) {
            $str = Translate::checkAndReplaceArgs($str, $sprintf);
        }

        return ($addslashes ? addslashes($str) : stripslashes($str));
    }

    public static function getPluginTranslation($plugin, $string, $source, $sprintf = null, $js = false, $context = null) {

        $string = str_replace('"', '`', $string);
        global $_PLUGINS, $_PLUGIN, $_LANGADM;

        if (empty($string)) {
            return $string;
        }

        static $langCache = [];

        $name = $plugin instanceof Plugin ? $plugin->name : $plugin;

        if (!is_null($context)) {

            if (!isset($context->language)) {
                $context->language = Tools::jsonDecode(Tools::jsonEncode(Language::construct('Language', Configuration::get('EPH_LANG_DEFAULT'))));
            }

            $language = $context->language;

            if (!isset($context->theme)) {
                $context->company = Company::initialize();
                $context->theme = new Theme((int) $context->company->id_theme);
            }

            $theme = $context->theme->directory;
        } else {
            $context = Context::getContext();

            if (!isset($context->language)) {
                $context->language = Tools::jsonDecode(Tools::jsonEncode(Language::construct('Language', Configuration::get('EPH_LANG_DEFAULT'))));
            }

            $language = $context->language;

            if (!isset($context->theme)) {
                $context->company = Company::initialize();
                $context->theme = new Theme((int) $context->company->id_theme);
            }

            $theme = $context->theme->directory;
        }

        if (!is_null($context->theme->plugin)) {
            $path = _EPH_PLUGIN_DIR_ . $context->theme->plugin . '/views/themes/' . $theme . '/';
        } else {
            $path = _SHOP_ROOT_DIR_ . '/themes/' . $theme . '/';
        }

        if (isset($context->language)) {

            $filesByPriority = [
                $path . 'plugins/' . $name . '/translations/' . $language->iso_code . '.php',
                _EPH_TRANSLATIONS_DIR_ . $language->iso_code . '/admin.php',
                _EPH_PLUGIN_DIR_ . $name . '/translations/' . $language->iso_code . '.php',
                _EPH_SPECIFIC_PLUGIN_DIR_ . $name . '/translations/' . $language->iso_code . '.php',
            ];

            foreach ($filesByPriority as $file) {

                if (file_exists($file)) {
                    include_once $file;

                    $_PLUGINS = !empty($_PLUGINS) ? $_PLUGINS + $_PLUGIN : $_PLUGIN;
                }

            }

        }

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);

        $cacheKey = $name . '|' . $string . '|' . $source . '|' . (int) $js;
        $ret = null;

        if (!isset($langCache[$cacheKey])) {

            if ($_PLUGINS == null) {

                if ($sprintf !== null) {
                    $string = Translate::checkAndReplaceArgs($string, $sprintf);
                }

                return str_replace('"', '&quot;', $string);
            }

            $currentKey = trim(strtolower('<{' . $name . '}' . $theme . '>' . $source) . '_' . $key);
            $defaultKey = trim(strtolower('<{' . $name . '}ephenyx>' . $source) . '_' . $key);
            $PhenyxShopKey = trim(strtolower('<{' . $name . '}phenyxshop>' . $source) . '_' . $key);

            if ('controller' == substr($source, -10, 10)) {
                $file = substr($source, 0, -10);
                $currentKeyFile = strtolower('<{' . $name . '}' . $theme . '>' . $file) . '_' . $key;
                $defaultKeyFile = strtolower('<{' . $name . '}ephenyx>' . $file) . '_' . $key;
                $PhenyxShopKeyFile = strtolower('<{' . $name . '}phenyxshop>' . $file) . '_' . $key;
            }

            if (isset($currentKeyFile) && !empty($_PLUGINS[$currentKeyFile])) {
                $ret = stripslashes($_PLUGINS[$currentKeyFile]);
            } else

            if (isset($defaultKeyFile) && !empty($_PLUGINS[$defaultKeyFile])) {
                $ret = stripslashes($_PLUGINS[$defaultKeyFile]);
            } else

            if (isset($PhenyxShopKeyFile) && !empty($_PLUGINS[$PhenyxShopKeyFile])) {
                $ret = stripslashes($_PLUGINS[$PhenyxShopKeyFile]);
            } else

            if (!empty($_PLUGINS[$currentKey])) {
                $ret = stripslashes($_PLUGINS[$currentKey]);
            } else

            if (!empty($_PLUGINS[$defaultKey])) {
                $ret = stripslashes($_PLUGINS[$defaultKey]);
            } else

            if (!empty($_PLUGINS[$PhenyxShopKey])) {
                $ret = stripslashes($_PLUGINS[$PhenyxShopKey]);
            } else

            if (!empty($_PLUGINS)) {

                foreach ($_PLUGINS as $k => $value) {

                    if (str_ends_with($k, $key) && !empty($value)) {
                        $ret = stripslashes($value);
                    }

                }

            } else

            if (!empty($_LANGADM)) {
                $ret = stripslashes(Translate::getGenericAdminTranslation($string, $_LANGADM, $key));
            } else

            if (is_null($ret)) {
                $ret = stripslashes($string);
            }

            if ($sprintf !== null) {
                $ret = Translate::checkAndReplaceArgs($ret, $sprintf);
            }

            if ($js) {
                $ret = addslashes($ret);
            } else

            if (!is_null($ret)) {
                $ret = htmlspecialchars($ret, ENT_COMPAT, 'UTF-8');
            }

            if ($sprintf === null) {
                $langCache[$cacheKey] = $ret;
            } else {
                return $ret;
            }

        }

        return $langCache[$cacheKey];
    }

    public static function checkAndReplaceArgs($string, $args) {

        if (preg_match_all('#(?:%%|%(?:[0-9]+\$)?[+-]?(?:[ 0]|\'.)?-?[0-9]*(?:\.[0-9]+)?[bcdeufFosxX])#', $string, $matches) && !is_null($args)) {

            if (!is_array($args)) {
                $args = [$args];
            }

            return vsprintf($string, $args);
        }

        return $string;
    }

    public static function getGenericAdminTranslation($string, &$langArray, $key = null) {

        $string = preg_replace("/\\\*'/", "\'", $string);

        if (is_null($key)) {
            $key = md5($string);
        }

        if (isset($langArray['AdminController' . $key])) {
            $str = $langArray['AdminController' . $key];
        } else

        if (isset($langArray['Helper' . $key])) {
            $str = $langArray['Helper' . $key];
        } else

        if (isset($langArray['AdminTab' . $key])) {
            $str = $langArray['AdminTab' . $key];
        } else {
            // note in 1.5, some translations has moved from AdminXX to helper/*.tpl
            $str = $string;
        }

        return $str;
    }

    public static function getGenericFrontTranslation($string, &$langArray, $key = null) {

        $string = preg_replace("/\\\*'/", "\'", $string);

        if (is_null($key)) {
            $key = md5($string);
        }

        if (isset($langArray['FrontController' . $key])) {
            $str = $langArray['FrontController' . $key];
        } else {
            // note in 1.5, some translations has moved from AdminXX to helper/*.tpl
            $str = $string;
        }

        $extra = null;
        $extra = Hook::exec('actionGenericFrontTranslation', ['langArray' => $langArray, 'key' => $key]);

        if (!is_null($extra)) {
            $str = $extra;
        }

        return $str;
    }

    public static function getPdfTranslation($string, $file, $sprintf = null, $context = null) {

        if (empty(static::$_language)) {
            static::$_language = Context::getContext()->language;
        }

        $string = str_replace('"', '`', $string);
        $iso = static::$_language->iso_code;

        global $_LANGPDFS, $_LANGPDF;

        if (!Validate::isLangIsoCode($iso)) {
            $this->l(sprintf('Invalid iso lang (%s)', Tools::safeOutput($iso)));
        }

        if (empty($_LANGPDFS)) {
            $_LANGPDFS = [];
            $overrideI18NFile = _EPH_THEME_DIR_ . 'pdf/lang/' . $iso . '.php';
            $i18NFile = _EPH_TRANSLATIONS_DIR_ . $iso . '/pdf.php';

            if (file_exists($overrideI18NFile)) {
                $i18NFile = $overrideI18NFile;
            }

            if (!include ($i18NFile)) {
                $this->l(sprintf('Cannot include PDF translation language file : %s', $i18NFile));
            }

            if (is_array($_LANGPDF)) {
                $_LANGPDFS = array_merge(
                    $_LANGPDFS,
                    $_LANGPDF
                );
            }

        }

        Hook::exec('actionPdfTranslate', ['iso' => $iso]);

        if (!isset($_LANGPDFS) || !is_array($_LANGPDFS)) {
            return str_replace('"', '&quot;', $string);
        }

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);

        $str = (array_key_exists($file . $key, $_LANGPDFS) ? $_LANGPDFS[$file . $key] : $string);

        if ($sprintf !== null) {
            $str = Translate::checkAndReplaceArgs($str, $sprintf);
        }

        return $str;
    }

    public static function getMailsTranslation($string, $file, $sprintf = null, $context = null) {

        if (empty(static::$_language)) {
            static::$_language = Context::getContext()->language;
        }

        $string = str_replace('"', '`', $string);
        $_LANGMAILS = [];
        global $_LANGMAIL;

        if (!is_null($context)) {

            if (!isset($context->language)) {
                $context->language = Tools::jsonDecode(Tools::jsonEncode(Language::construct('Language', Configuration::get('EPH_LANG_DEFAULT'))));
            }

            $iso = $context->language->iso_code;
        } else {
            $iso = Context::getContext()->language->iso_code;
        }

        if (!Validate::isLangIsoCode($iso)) {
            $this->l(sprintf('Invalid iso lang (%s)', Tools::safeOutput($iso)));
        }

        $i18NFile = _EPH_TRANSLATIONS_DIR_ . $iso . '/mail.php';
        Hook::exec('actionMailsTranslate', ['iso' => $iso]);

        if (!include ($i18NFile)) {
            $this->l(sprintf('Cannot include PDF translation language file : %s', $i18NFile));
        }

        $_LANGMAILS = !empty($_LANGMAIL) ? $_LANGMAILS + $_LANGMAIL : $_LANGMAIL;

        if (!isset($_LANGMAILS) || !is_array($_LANGMAILS)) {
            return str_replace('"', '&quot;', $string);
        }

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);

        $str = (array_key_exists($file . $key, $_LANGMAILS) ? $_LANGMAILS[$file . $key] : $string);

        if ($sprintf !== null) {
            $str = Translate::checkAndReplaceArgs($str, $sprintf);
        }

        return $str;
    }

    public static function smartyPostProcessTranslation($string, $params) {

        return Translate::postProcessTranslation($string, $params);
    }

    public static function postProcessTranslation($string, $params) {

        // If tags were explicitely provided, we want to use them *after* the translation string is escaped.

        if (!empty($params['tags'])) {

            foreach ($params['tags'] as $index => $tag) {
                // Make positions start at 1 so that it behaves similar to the %1$d etc. sprintf positional params
                $position = $index + 1;
                // extract tag name
                $match = [];

                if (preg_match('/^\s*<\s*(\w+)/', $tag, $match)) {
                    $opener = $tag;
                    $closer = '</' . $match[1] . '>';

                    $string = str_replace('[' . $position . ']', $opener, $string);
                    $string = str_replace('[/' . $position . ']', $closer, $string);
                    $string = str_replace('[' . $position . '/]', $opener . $closer, $string);
                }

            }

        }

        return $string;
    }

    public static function ppTags($string, $tags) {

        return Translate::postProcessTranslation($string, ['tags' => $tags]);
    }

    public static function getInstallerTranslation($string, $class, $addslashes = false, $htmlentities = true, $sprintf = null) {

        global $_LANGINSTALL;

        if ($_LANGINSTALL == null) {

            if (!is_null($context)) {

                if (!isset($context->language)) {
                    $context->language = Tools::jsonDecode(Tools::jsonEncode(Language::construct('Language', Configuration::get('EPH_LANG_DEFAULT'))));
                }

                $iso = $context->language->iso_code;
            } else {
                $iso = Context::getContext()->language->iso_code;
            }

            if (empty($iso)) {
                try {
                    $iso = Language::getIsoById((int) Context::getContext()->language->id);
                } catch (PhenyxException $e) {
                    $iso = 'en';
                }

            }

            if (file_exists(_EPH_TRANSLATIONS_DIR_ . $iso . '/front.php')) {
                include_once _EPH_TRANSLATIONS_DIR_ . $iso . '/front.php';
            }

            Hook::exec('actionFrontTranslate', ['iso' => $iso]);

            if (file_exists(_EPH_OVERRIDE_TRANSLATIONS_DIR_ . $iso . '/front.php')) {

                include_once _EPH_OVERRIDE_TRANSLATIONS_DIR_ . $iso . '/front.php';
            }

        }

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);

        if (isset($_LANGFRONT[$class . $key])) {
            $str = $_LANGFRONT[$class . $key];
        } else

        if (isset($_LANGOVFRONT[$class . $key])) {
            $str = $_LANGFRONT[$class . $key];
        } else {
            $str = Translate::getGenericFrontTranslation($string, $_LANGOVFRONT, $key);
        }

        if ($htmlentities) {
            $str = htmlspecialchars($str, ENT_QUOTES, 'utf-8');
        }

        $str = str_replace('"', '&quot;', $str);

        if ($sprintf !== null) {
            $str = Translate::checkAndReplaceArgs($str, $sprintf);
        }

        return ($addslashes ? addslashes($str) : stripslashes($str));
    }

}
