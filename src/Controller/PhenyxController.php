<?php
#[AllowDynamicProperties]
/**
 * Class ControllerCore
 *
 * @since 1.6.6.3
 */
abstract class PhenyxController {

    public static $php_errors = [];

    public $css_files = [];

    public $js_footers = [];

    public $js_files = [];

    public $index_js_files = [];

    public $index_js_def = [];

    public $push_js_files = [];

    public $push_css_files = [];

    public $extracss;
    
    public $mainControllers;

    public $extra_vars;

    public $ajax = false;
    
    public $ajax_submit = false;

    public $ajaxLayout = false;

    public $layout = 'layout.tpl';
    /** @var string Controller type. Possible values: 'front', 'pluginfront', 'admin', 'pluginadmin' */
    public $controller_type;

    public $php_self;

    public $table = 'configuration';

    public $className;

    public $tabAccess;

    public $identifier = false;

    public $link_rewrite;
    
    public $require_context = true;

    protected $context;
    
    protected $_user;
    
    protected $_company;    
    
    protected $_cookie;
    
    protected $_link;
    
    protected $_language;
    
    protected $_smarty;

    /** @var string */
    protected $display;

    protected $display_header;

    protected $display_header_javascript;

    protected $template;

    protected $display_footer;

    protected $content_only = false;

    protected $json = false;

    protected $status = '';

    protected $redirect_after = null;

    protected $total_filesize = 0;

    protected $total_query_time = 0;

    protected $total_global_var_size = 0;

    protected $total_plugins_time = 0;

    protected $total_plugins_memory = 0;

    protected $global_var_size = [];

    protected $total_cache_size;

    protected $plugins_perfs = [];

    protected $hooks_perfs = [];

    protected $array_queries = [];

    protected $profiler = [];

    public $content_ajax = '';

    public $controller_name;

    protected $paragridScript;

    public $contextMenuItems = [];

    public $paramToolBarItems = [];

    public $paramClassName;

    public $paramController_name;

    public $paramTable;

    public $paramIdentifier;

    public $uppervar;

    public $paramDataModel;

    public $paramColModel;

    public $paramCellSave;

    public $requestModel;

    public $requestField = null;

    public $requestCustomModel = null;

    public $requestComplementaryModel;

    public $paramHeight;

    public $paramWidth;

    public $heightModel = 217;

    public $paramPageModel = [
        'type'       => '\'local\'',
        'rPP'        => 100,
        'rPPOptions' => [10, 20, 40, 50, 100, 200, 500],
    ];

    public $showTop = 1;

    public $paramCreate = '';

    public $refresh;

    public $paramComplete;

    public $paramLoad;

    public $paramSelectModelType = 'row';

    public $paramToolbar = [];

    public $columnBorders = 0;

    public $rowBorders = 0;

    public $filterModel = [
        'on'          => true,
        'mode'        => '\'AND\'',
        'header'      => true,
        'type'        => '\'local\'',
        'menuIcon'    => 0,
        'gridOptions' => [
            'numberCell' => [
                'show' => 0,
            ],
            'width'      => '\'flex\'',
            'flex'       => [
                'one' => true,
            ],
        ],
    ];

    public $editorBegin;

    public $editorBlur;

    public $editorEnd;

    public $editorFocus;

    public $rowInit = '';

    public $rowSelect;

    public $selectEnd;

    public $rowClick = '';

    public $rowDblClick = '';

    public $paramChange = '';

    public $showTitle = true;

    public $paramTitle;

    public $summaryData = '';

    public $editModel;

    public $sortModel;

    public $beforeSort;

    public $paramSort;

    public $beforeFilter;

    public $beforeTableView;

    public $dropOn = false;

    public $dragOn = false;

    public $dragdiHelper;

    public $dragclsHandle;

    public $dragModel;

    public $dropModel;

    public $moveNode;

    public $treeModel;

    public $treeExpand;

    public $paramCheck;

    public $showNumberCell = 0;

    public $groupModel;

    public $summaryTitle;

    public $postRenderInterval;

    public $cellDblClick;

    public $paramContextMenu;

    public $functionContextMenu = false;

    public $paramExtraFontcion = '';

    public $gridAfterLoadFunction;

    public $gridFunction;

    public $is_subModel = false;

    public $onlyObject = false;

    public $needRequestModel = true;

    public $needColModel = true;

    public $detailModel;

    public $subDetailModel;

    public $detailContextMenu;

    public $showHeader = 1;

    public $maxHeight;

    public $paramMinWidth;

    public $showToolbar = true;

    public $formulas = false;

    public $paramEditable = 1;

    public $editorKeyUp;

    public $cellClick;

    public $beforeCellClick;

    public $stripeRows = 1;

    public $autoAddRow = 0;

    public $autoAddCol = 0;

    public $animModel = [];

    public $columnTemplate;

    public $tabModel;

    public $paramEditor = 1;

    public $paramhistory;

    public $paramAutoRow;

    public $showBottom = true;

    public $paramWrap = true;

    public $manageHeaderFields = false;

    public $default_language;

    public $ajaxOptions;

    protected $publicName;

    protected $action;

    public $targetController;

    public $errors = [];

    public $warnings = [];

    /** @var bool */
    public $bootstrap = false;

    public $lang = false;

    public $configurationField = 0;

    public $updateableFields;

    protected $fields_form;

    public $fields_value = [];

    protected $toolbar_btn = null;
    /** @var bool Scrolling toolbar */
    protected $toolbar_scroll = true;

    public $token;

    protected $helper;

    protected $submit_action;

    public $base_tpl_form = null;

    public function getExtraPhenyxVars() {

        $extraVars = Hook::exec('actionPhenyxControllerGetExtraVars', ['controller_type' => $this->controller_type], null, true);
        if (is_array($extraVars)) {
           foreach ($extraVars as $plugin => $values) {
               if (is_array($values)) {
                   foreach ($values as $key => $value) {
                       if (isset($value)) {
                           $this->{$key} = $value;
                       } else {
                           $this->{$key};
                       }    
                   }
               }
           }

        }

    }

    public function __construct() {

        if (_EPH_DEBUG_PROFILING_ || _EPH_ADMIN_DEBUG_PROFILING_) {
            $this->profiler[] = $this->stamp('config');
        }

        if (is_null($this->display_header)) {
            $this->display_header = true;
        }

        if (is_null($this->display_header_javascript)) {
            $this->display_header_javascript = true;
        }

        if (is_null($this->display_footer)) {
            $this->display_footer = true;
        }

        if($this->require_context) {
            $context = Context::getContext();
            $this->context = $context;
            $this->_company = $context->company;
            $this->_user = $context->user;
            $this->_cookie = $context->cookie;
            $this->_link = $context->link;
            $this->_language = $context->language;
            $this->_smarty = $context->smarty;
            $this->context->getExtraContextVars();
        }
        $this->context->controller = $this;
        $this->getExtraPhenyxVars();

        $this->ajax = Tools::getValue('ajax') || Tools::isSubmit('ajax');

        if (!headers_sent()
            && isset($_SERVER['HTTP_USER_AGENT'])
            && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false
                || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false)
        ) {
            header('X-UA-Compatible: IE=edge,chrome=1');
        }

        if (_EPH_DEBUG_PROFILING_ || _EPH_ADMIN_DEBUG_PROFILING_) {
            $this->profiler[] = $this->stamp('__construct');
        }

        $this->paramCreate = 'function (evt, ui) {
            buildHeadingAction(\'' . 'grid_' . $this->controller_name . '\', \'' . $this->controller_name . '\');
        }';

    }

    public static function getController($className, $auth = false, $ssl = false) {

        return new $className($auth, $ssl);
    }

    public function generateParaGridToolBar() {

        

        $paramToolBarItems = Hook::exec('action' . $this->controller_name . 'generateParaGridToolBar', [], null, true);

        if (is_array($paramToolBarItems)) {
            foreach ($paramToolBarItems as $plugin => $toolBars) {
                if(is_array($toolBars)) {                        
                    foreach($toolBars as $toolBar) {
                        $this->paramToolBarItems[] = $toolBar;
                    }
                }
            }
        }
        $toolBar = new ParamToolBar();
        $toolBar->items = $this->paramToolBarItems;

        return $toolBar->buildToolBar();
    }

    public function generateParaGridContextMenu() {

        $contextMenu = new ParamContextMenu($this->className, $this->controller_name);

        $contextMenuItems = Hook::exec('action' . $this->controller_name . 'generateParaGridContextMenu', ['class' => $this->className, 'contextMenuItems' => $this->contextMenuItems], null, true);

        if (!empty($contextMenuItems)) {

            foreach ($contextMenuItems as $plugin => $contextMenuItem) {
                if (is_array($contextMenuItem)) {
                    foreach ($contextMenuItem as $key => $item) {
                        $this->contextMenuItems[$key] = $item;
                    }
                }

            }

        }

        $contextMenu->items = $this->contextMenuItems;

        return $contextMenu->buildContextMenu();
    }

    public function generateParaGridScript($idObjet = null) {

        $paragrid = new ParamGrid(
            (!empty($this->paramClassName) ? $this->paramClassName : $this->className),
            (!empty($this->paramController_name) ? $this->paramController_name : $this->controller_name),
            (!empty($this->paramTable) ? $this->paramTable : $this->table),
            (!empty($this->paramIdentifier) ? $this->paramIdentifier : $this->identifier)
        );
        $paragrid->paramTable = (!empty($this->paramTable) ? $this->paramTable : $this->table);
        $paragrid->paramController = (!empty($this->paramController_name) ? $this->paramController_name : $this->controller_name);

        $paragrid->uppervar = $this->uppervar;

        $paragrid->dataModel = $this->paramDataModel;
        $paragrid->colModel = $this->paramColModel;

        $paragrid->requestModel = $this->requestModel;
        $paragrid->requestField = $this->requestField;
        $paragrid->requestCustomModel = $this->requestCustomModel;
        $paragrid->requestComplementaryModel = $this->requestComplementaryModel;
        $paragrid->width = $this->paramWidth;
        $paragrid->height = $this->paramHeight;
        $paragrid->heightModel = $this->heightModel;
        $paragrid->showNumberCell = $this->showNumberCell;
        $paragrid->pageModel = $this->paramPageModel;
        $paragrid->showTop = $this->showTop;

        $paragrid->create = $this->paramCreate;

        $paragrid->refresh = $this->refresh;

        $paragrid->complete = $this->paramComplete;
        $paragrid->selectionModelType = $this->paramSelectModelType;

        $paragrid->toolbar = $this->paramToolbar;

        $paragrid->columnBorders = $this->columnBorders;
        $paragrid->rowBorders = $this->rowBorders;

        $paragrid->filterModel = $this->filterModel;

        $paragrid->editorBegin = $this->editorBegin;

        $paragrid->editorBlur = $this->editorBlur;

        $paragrid->editorEnd = $this->editorEnd;

        $paragrid->editorFocus = $this->editorFocus;

        $paragrid->rowInit = $this->rowInit;
        $paragrid->rowSelect = $this->rowSelect;
        $paragrid->selectEnd = $this->selectEnd;
        $paragrid->rowDblClick = $this->rowDblClick;
        $paragrid->cellSave = $this->paramCellSave;
        $paragrid->rowClick = $this->rowClick;
        $paragrid->cellDblClick = $this->cellDblClick;
        $paragrid->cellClick = $this->cellClick;
        $paragrid->change = $this->paramChange;
        $paragrid->showTitle = $this->showTitle;
        $paragrid->title = $this->paramTitle;
        $paragrid->fillHandle = '\'all\'';
        $paragrid->summaryData = $this->summaryData;
        $paragrid->editModel = $this->editModel;

        $paragrid->showBottom = $this->showBottom;

        $paragrid->load = $this->paramLoad;

        $paragrid->sort = $this->paramSort;

        $paragrid->sortModel = $this->sortModel;
        $paragrid->beforeSort = $this->beforeSort;
        $paragrid->beforeFilter = $this->beforeFilter;
        $paragrid->beforeTableView = $this->beforeTableView;
        $paragrid->stripeRows = $this->stripeRows;

        $paragrid->dropOn = $this->dropOn;

        $paragrid->dragOn = $this->dragOn;

        $paragrid->dragdiHelper = $this->dragdiHelper;

        $paragrid->dragclsHandle = $this->dragclsHandle;

        $paragrid->dragModel = $this->dragModel;

        $paragrid->dropModel = $this->dropModel;
        $paragrid->moveNode = $this->moveNode;

        $paragrid->treeModel = $this->treeModel;

        $paragrid->treeExpand = $this->treeExpand;

        $paragrid->check = $this->paramCheck;

        $paragrid->groupModel = $this->groupModel;

        $paragrid->summaryTitle = $this->summaryTitle;
        $paragrid->wrap = $this->paramWrap;

        $paragrid->postRenderInterval = $this->postRenderInterval;

        $paragrid->contextMenu = $this->paramContextMenu;
        $paragrid->functionContextMenu = $this->functionContextMenu;

        $paragrid->gridExtraFunction = $this->paramExtraFontcion;

        $paragrid->gridAfterLoadFunction = $this->gridAfterLoadFunction;

        $paragrid->gridFunction = $this->gridFunction;

        $paragrid->is_subModel = $this->is_subModel;

        $paragrid->onlyObject = $this->onlyObject;

        $paragrid->needRequestModel = $this->needRequestModel;
        $paragrid->needColModel = $this->needColModel;

        $paragrid->detailModel = $this->detailModel;

        $paragrid->subDetailModel = $this->subDetailModel;

        $paragrid->detailContextMenu = $this->detailContextMenu;

        $paragrid->showHeader = $this->showHeader;

        $paragrid->maxHeight = $this->maxHeight;

        $paragrid->minWidth = $this->paramMinWidth;

        $paragrid->showToolbar = $this->showToolbar;

        $paragrid->animModel = $this->animModel;

        $paragrid->formulas = $this->formulas;

        $paragrid->editable = $this->paramEditable;

        $paragrid->editorKeyUp = $this->editorKeyUp;

        $paragrid->autoAddRow = $this->autoAddRow;
        $paragrid->autoAddCol = $this->autoAddCol;
        $paragrid->columnTemplate = $this->columnTemplate;
        $paragrid->tabModel = $this->tabModel;
        $paragrid->editor = $this->paramEditor;
        $paragrid->history = $this->paramhistory;
        $paragrid->autoRow = $this->paramAutoRow;
        $paragrid->beforeCellClick = $this->beforeCellClick;
        
        $extraVars = Hook::exec('action'.$this->controller_name.'ParaGridScript', ['controller_name' => $this->controller_name], null, true);
        if (is_array($extraVars)) {
           foreach ($extraVars as $plugin => $values) {
               if (is_array($values)) {
                   foreach ($values as $key => $value) {
                       if (isset($value)) {
                           $this->{$key} = $value;
                       } else {
                           $this->{$key};
                       }    
                   }
               }
           }

        }

        $option = $paragrid->generateParaGridOption();

        $script = $paragrid->generateParagridScript();

        $this->paragridScript = $script;

        if ($this->is_subModel) {
            return $this->paragridScript;
        }

        return '<script type="text/javascript">' . PHP_EOL . $this->paragridScript . PHP_EOL . '</script>';
    }

    public static function myErrorHandler($errno, $errstr, $errfile, $errline) {

        if (error_reporting() === 0) {
            return false;
        }

        switch ($errno) {
        case E_USER_ERROR:
        case E_ERROR:
            die('Fatal error: ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
            break;
        case E_USER_WARNING:
        case E_WARNING:
            $type = 'Warning';
            break;
        case E_USER_NOTICE:
        case E_NOTICE:
            $type = 'Notice';
            break;
        default:
            $type = 'Unknown error';
            break;
        }

        static::$php_errors[] = [
            'type'    => $type,
            'errline' => (int) $errline,
            'errfile' => str_replace('\\', '\\\\', $errfile), // Hack for Windows paths
            'errno'   => (int) $errno,
            'errstr'  => $errstr,
        ];

        Context::getContext()->smarty->assign('php_errors', static::$php_errors);

        return true;
    }

    public function &__get($property) {

        $camelCaseProperty = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $property))));

        if (property_exists($this, $camelCaseProperty)) {
            return $this->$camelCaseProperty;
        }

        return $this->$property;
    }

    public function __set($property, $value) {

        $blacklist = [
            '_select',
            '_join',
            '_where',
            '_group',
            '_having',
            '_conf',
            '_lang',
        ];

        // Property to camelCase for backwards compatibility
        $snakeCaseProperty = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $property))));

        if (!in_array($property, $blacklist) && property_exists($this, $snakeCaseProperty)) {
            $this->$snakeCaseProperty = $value;
        } else {
            $this->$property = $value;
        }

    }

    public function run() {

        $this->init();

        if (_EPH_DEBUG_PROFILING_ || _EPH_ADMIN_DEBUG_PROFILING_) {
            $this->profiler[] = $this->stamp('init');
        }

        if ($this->checkAccess()) {

            if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className))) {
                $this->setMedia();
            }

            $this->postProcess();

            if (!empty($this->redirect_after)) {
                $this->redirect();
            }

            if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className))) {
                $this->initHeader();
            }

            if ($this->viewAccess()) {
                $this->initContent();
            } else {
                $this->errors[] = Tools::displayError('Access denied.');
            }

            if (!$this->content_only && ($this->display_footer || (isset($this->className) && $this->className))) {
                $this->initFooter();
            }

            if ($this->ajax) {
                $action = Tools::toCamelCase(Tools::getValue('action'), true);

                if (!empty($action) && method_exists($this, 'displayAjax' . $action)) {
                    $this->{'displayAjax' . $action}

                    ();
                } else

                if (method_exists($this, 'displayAjax')) {
                    $this->displayAjax();
                }

            } else {
                $this->display();
            }

        } else {
            $this->initCursedPage();

            if (isset($this->layout)) {
                $this->smartyOutputContent($this->layout);
            }

        }

    }

    public function init() {

        if (_EPH_MODE_DEV_ && $this->controller_type == 'admin') {
            set_error_handler([__CLASS__, 'myErrorHandler']);
        }

        if (!defined('_EPH_BASE_URL_')) {
            define('_EPH_BASE_URL_', Tools::getDomain(true));
        }

        if (!defined('_EPH_BASE_URL_SSL_')) {
            define('_EPH_BASE_URL_SSL_', Tools::getDomainSsl(true));
        }

    }

    public function setMedia($isNewTheme = false) {

        $this->addCSS(
            [
                _EPH_JS_DIR_.'paramgrid/pqSelect/pqselect.min.css',
                _EPH_JS_DIR_.'paramgrid/pqgrid.min.css',
                _EPH_JS_DIR_.'paramgrid/pqgrid.ui.min.css',
            ]
        );

        $this->addJS([
            _EPH_JS_DIR_.'jquery/jquery-3.7.1.min.js',
            _EPH_JS_DIR_.'jquery-ui/jquery-ui.min.js',
            _EPH_JS_DIR_.'paramgrid/pqSelect/pqselect.min.js',
            _EPH_JS_DIR_.'paramgrid/pqgrid.min.js',
            _EPH_JS_DIR_.'paramgrid/localize/pq-localize-fr.js',
            _EPH_JS_DIR_.'paramgrid/pqTouch/pqtouch.min.js',
            _EPH_JS_DIR_.'paramgrid/jsZip-2.5.0/jszip.min.js',
            _EPH_JS_DIR_.'paramgrid/FileSaver.js',
            _EPH_JS_DIR_.'paramgrid/javascript-detect-element-resize/detect-element-resize.js',
            _EPH_JS_DIR_.'paramgrid/javascript-detect-element-resize/jquery.resize.js',

        ]);
    }

    public function ajaxProcessSetLanguage() {

        $idLang = Tools::getValue('id_lang');
        $cookieIdLang = $this->context->cookie->id_lang;
        $configurationIdLang = Configuration::get(Configuration::LANG_DEFAULT);

        $this->context->cookie->id_lang = $idLang;
        $language = new Language($idLang);

        if (Validate::isLoadedObject($language) && $language->active) {
            $this->context->language = $language;
        }

        if (Validate::isUnsignedId($this->context->user->id)) {
            $user = new User($this->context->user->id);

            if ($user->is_admin) {
                $user = new Employee($user->id);
            }

            $user->id_lang = $idLang;
            $user->update();
            $this->context->user = $user;
        }

        die(true);
    }

    public function getUserIpAddr() {

        return $_SERVER['SERVER_ADDR'];
    }

    abstract public function checkAccess();

    abstract public function postProcess();

    abstract protected function redirect();

    abstract public function initHeader();

    abstract public function viewAccess();

    abstract public function initContent();

    abstract public function initFooter();

    abstract public function display();

    abstract public function initCursedPage();

    protected function smartyOutputContent($content) {

        $this->context->cookie->write();
        $html = '';
        $jsTag = 'js_def';
        $this->context->smarty->assign($jsTag, $jsTag);

        if (is_array($content)) {

            foreach ($content as $tpl) {
                $html .= $this->context->smarty->fetch($tpl);
            }

        } else {
            $html = $this->context->smarty->fetch($content);
        }

        $html = trim($html);

        if (!empty($html)) {

            $domAvailable = extension_loaded('dom') ? true : false;
            $defer = (bool) Configuration::get('EPH_JS_DEFER');

            if ($defer && $domAvailable) {
                $html = Media::deferInlineScripts($html);
            }

            $html = trim(str_replace(['</body>', '</html>'], '', $html)) . "\n";

            $this->context->smarty->assign(
                [
                    $jsTag      => Media::getJsDef(),
                    'js_files'  => $defer ? array_unique($this->js_files) : [],
                    'js_inline' => ($defer && $domAvailable) ? Media::getInlineScript() : [],
                ]
            );
            $javascript = $this->context->smarty->fetch(_EPH_ALL_THEMES_DIR_ . 'javascript.tpl');

            if ($defer && (!isset($this->ajax) || !$this->ajax)) {
                echo $html . $javascript;
            } else

            if ($defer && $this->ajax) {

                die(Tools::jsonEncode(['html', $html . $javascript]));

            } else {
                echo preg_replace('/(?<!\$)' . $jsTag . '/', $javascript, $html);
            }

            echo ((!Tools::getIsset($this->ajax) || !$this->ajax) ? '</body></html>' : '');

        } else {
            echo $html;
        }

    }

    protected function ajaxOutputContent($content) {

        $this->context->cookie->write();
        $html = '';
        $jsTag = 'js_def';
        $this->context->smarty->assign($jsTag, $jsTag);

        if (is_array($content)) {

            foreach ($content as $tpl) {
                $html .= $this->context->smarty->fetch($tpl);
            }

        } else {
            $html = $this->context->smarty->fetch($content);
        }

        $html = trim($html);

        $domAvailable = extension_loaded('dom') ? true : false;
        $defer = (bool) Configuration::get('EPH_JS_DEFER');

        $html = trim(str_replace(['</body>', '</html>'], '', $html)) . "\n";
        $this->ajax_head = str_replace(['<head>', '</head>'], '', Media::deferTagOutput('head', $html));
        $page = Media::deferIdOutput('page', $html);
        $this->context->smarty->assign(
            [
                $jsTag      => Media::getJsDef(),
                'js_files'  => $defer ? array_unique($this->js_files) : [],
                'js_inline' => [],
            ]
        );
        $javascript = $this->context->smarty->fetch(_EPH_ALL_THEMES_DIR_ . 'javascript.tpl');

        if ($defer) {
            $templ = $page . $javascript;
            $return = [
                'historyState' => $this->historyState,
                'page_title'   => $this->page_title,
                'ajax_head'    => $this->ajax_head,
                'html'         => $templ,
            ];

        } else {
            $templ = preg_replace('/(?<!\$)' . $jsTag . '/', $javascript, $page);
            $return = [
                'historyState' => $this->historyState,
                'page_title'   => $this->page_title,
                'ajax_head'    => $this->ajax_head,
                'html'         => $templ,
            ];
        }

        die(Tools::jsonEncode($return));

    }

    public function displayHeader($display = true) {

        $this->display_header = $display;
    }

    public function displayHeaderJavaScript($display = true) {

        $this->display_header_javascript = $display;
    }

    public function displayFooter($display = true) {

        $this->display_footer = $display;
    }

    public function setTemplate($template) {

        $this->template = $template;
    }

    public function setRedirectAfter($url) {

        $this->redirect_after = $url;
    }

    public function removeCSS($cssUri, $cssMediaType = 'all', $checkPath = true) {

        
        if (!is_array($cssUri)) {
            $cssUri = [$cssUri];
        }
        
        foreach ($cssUri as $cssFile => $media) {
            if (is_string($cssFile) && strlen($cssFile) > 1) {
                if ($checkPath) {
                    $cssPath = Media::getCSSPath($cssFile, $media);
                } else {
                    $cssPath = [$cssFile => $media];
                }

            } else {
                if ($checkPath) {
                    if(file_exists($media)) {
                        $cssPath = '/' . ltrim(str_replace(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, _EPH_ROOT_DIR_), __EPH_BASE_URI__, $media), '/\\');                                        
                    } else {
                        $cssPath = Media::getCSSPath($media, $cssMediaType);
                    }
                    
                } else {
                    $cssPath = [$media => $cssMediaType];
                }

            }
            
            
            if ($cssPath && isset($this->css_files[$cssPath])) {
                unset($this->css_files[$cssPath]);
            }

        }

    }

    public function removeJS($jsUri, $checkPath = true) {

        if (is_array($jsUri)) {

            foreach ($jsUri as $jsFile) {
                $jsPath = $jsFile;

                if ($checkPath) {
                    $jsPath = Media::getJSPath($jsFile);
                }

                if ($jsPath && in_array($jsPath, $this->js_files)) {
                    unset($this->js_files[array_search($jsPath, $this->js_files)]);
                }

            }

        } else {
            $jsPath = $jsUri;

            if ($checkPath) {
                $jsPath = Media::getJSPath($jsUri);
            }

            if ($jsPath) {
                unset($this->js_files[array_search($jsPath, $this->js_files)]);
            }

        }

    }

    public function addJquery($version = null, $folder = null, $minifier = true) {

        $this->addJS(Media::getJqueryPath($version, $folder, $minifier), false);
    }

    public function addJS($jsUri, $checkPath = true) {

        if (is_array($jsUri)) {

            foreach ($jsUri as $jsFile) {
                $jsFile = explode('?', $jsFile);
                $version = '';

                if (isset($jsFile[1]) && $jsFile[1]) {
                    $version = $jsFile[1];
                }

                $jsPath = $jsFile = $jsFile[0];

                if ($checkPath) {
                    $jsPath = Media::getJSPath($jsFile);
                }

                // $key = is_array($js_path) ? key($js_path) : $js_path;

                if ($jsPath && !in_array($jsPath, $this->js_files)) {
                    $this->js_files[] = $jsPath . ($version ? '?' . $version : '');
                }

            }

        } else {
            $jsUri = explode('?', $jsUri);
            $version = '';

            if (isset($jsUri[1]) && $jsUri[1]) {
                $version = $jsUri[1];
            }

            $jsPath = $jsUri = $jsUri[0];

            if ($checkPath) {
                $jsPath = Media::getJSPath($jsUri);
            }

            if ($jsPath && !in_array($jsPath, $this->js_files)) {
                $this->js_files[] = $jsPath . ($version ? '?' . $version : '');
            }

        }

    }

    public function addFooterJS($jsUri, $checkPath = true) {

        if (is_array($jsUri)) {

            foreach ($jsUri as $jsFile) {
                $jsFile = explode('?', $jsFile);
                $version = '';

                if (isset($jsFile[1]) && $jsFile[1]) {
                    $version = $jsFile[1];
                }

                $jsPath = $jsFile = $jsFile[0];

                if ($checkPath) {
                    $jsPath = Media::getJSPath($jsFile);
                }

                // $key = is_array($js_path) ? key($js_path) : $js_path;

                if ($jsPath && !in_array($jsPath, $this->js_footers)) {
                    $this->js_footers[] = $jsPath . ($version ? '?' . $version : '');
                }

            }

        } else {
            $jsUri = explode('?', $jsUri);
            $version = '';

            if (isset($jsUri[1]) && $jsUri[1]) {
                $version = $jsUri[1];
            }

            $jsPath = $jsUri = $jsUri[0];

            if ($checkPath) {
                $jsPath = Media::getJSPath($jsUri);
            }

            if ($jsPath && !in_array($jsPath, $this->js_footers)) {
                $this->js_footers[] = $jsPath . ($version ? '?' . $version : '');
            }

        }

    }

    public function addJqueryUI($component, $theme = 'base', $checkDependencies = true) {

        if (!is_array($component)) {
            $component = [$component];
        }

        foreach ($component as $ui) {
            $uiPath = Media::getJqueryUIPath($ui, $theme, $checkDependencies);
            $this->addCSS($uiPath['css'], 'all', false);
            $this->addJS($uiPath['js'], false);
        }

    }

    public function addCSS($cssUri, $cssMediaType = 'all', $offset = null, $checkPath = true) {

        if (!is_array($cssUri)) {
            $cssUri = [$cssUri];
        }

        foreach ($cssUri as $cssFile => $media) {

            if (is_string($cssFile) && strlen($cssFile) > 1) {

                if ($checkPath) {
                    $cssPath = Media::getCSSPath($cssFile, $media);
                } else {
                    $cssPath = [$cssFile => $media];
                }

            } else {

                if ($checkPath) {
                    $cssPath = Media::getCSSPath($media, $cssMediaType);
                } else {
                    $cssPath = [$media => is_string($cssMediaType) ? $cssMediaType : 'all'];
                }

            }

            $key = is_array($cssPath) ? key($cssPath) : $cssPath;

            if ($cssPath && (!isset($this->css_files[$key]) || ($this->css_files[$key] != reset($cssPath)))) {
                $size = count($this->css_files);

                if ($offset === null || $offset > $size || $offset < 0 || !is_numeric($offset)) {
                    $offset = $size;
                }

                $this->css_files = array_merge(array_slice($this->css_files, 0, $offset), $cssPath, array_slice($this->css_files, $offset));
            }

        }

    }

    public function pushCSS($cssUri, $cssMediaType = 'all', $offset = null, $checkPath = true) {

        if (!is_array($cssUri)) {

            $cssUri = [$cssUri];
        }

        $result = [];

        foreach ($cssUri as $cssFile => $media) {

            if (is_string($cssFile) && strlen($cssFile) > 1) {

                if ($checkPath) {
                    $cssPath = Media::getCSSPath($cssFile, $media);
                } else {
                    $cssPath = [$cssFile => $media];
                }

            } else {

                if ($checkPath) {
                    $cssPath = Media::getCSSPath($media, $cssMediaType);
                } else {

                    $cssPath = [$media => is_string($cssMediaType) ? $cssMediaType : 'all'];
                }

            }

            $key = is_array($cssPath) ? key($cssPath) : $cssPath;

            if ($cssPath) {
                $size = count($this->push_css_files);

                if ($offset === null || $offset > $size || $offset < 0 || !is_numeric($offset)) {
                    $offset = $size;
                }

                $this->push_css_files = array_merge(array_slice($this->push_css_files, 0, $offset), $cssPath, array_slice($this->push_css_files, $offset));
            }

        }

        return $this->push_css_files;

    }

    public function pushJS($jsUri, $checkPath = true) {

        if (is_array($jsUri)) {

            foreach ($jsUri as $jsFile) {
                $jsFile = explode('?', $jsFile);
                $version = '';

                if (isset($jsFile[1]) && $jsFile[1]) {
                    $version = $jsFile[1];
                }

                $jsPath = $jsFile = $jsFile[0];

                if ($checkPath) {
                    $jsPath = Media::getJSPath($jsFile);
                }

                // $key = is_array($js_path) ? key($js_path) : $js_path;

                if ($jsPath && !in_array($jsPath, $this->push_js_files)) {
                    $this->push_js_files[] = $jsPath . ($version ? '?' . $version : '');
                }

            }

        } else {
            $jsUri = explode('?', $jsUri);
            $version = '';

            if (isset($jsUri[1]) && $jsUri[1]) {
                $version = $jsUri[1];
            }

            $jsPath = $jsUri = $jsUri[0];

            if ($checkPath) {
                $jsPath = Media::getJSPath($jsUri);
            }

            if ($jsPath && !in_array($jsPath, $this->push_js_files)) {
                $this->push_js_files[] = $jsPath . ($version ? '?' . $version : '');
            }

        }

        return $this->push_js_files;

    }

    public function addJqueryPlugin($name, $folder = null, $css = true) {

        if (!is_array($name)) {
            $name = [$name];
        }

        if (is_array($name)) {

            foreach ($name as $plugin) {
                $pluginPath = Media::getJqueryPluginPath($plugin, $folder);

                if (!empty($pluginPath['js'])) {
                    $this->addJS($pluginPath['js'], false);
                }

                if ($css && !empty($pluginPath['css'])) {
                    $this->addCSS(key($pluginPath['css']), 'all', null, false);
                }

            }

        }

    }

    public function isXmlHttpRequest() {

        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    public function manageFieldsVisibility($fields) {

        $return = [];

        if (is_array($fields)) {

            foreach ($fields as $field) {
                $name = '';
                $hidden = false;
                $hiddenable = 'yes';

                foreach ($field as $key => $value) {

                    if ($key == 'title') {
                        $name = $value;
                    }

                    if ($key == 'hidden') {
                        $hidden = $value;
                    }

                    if ($key == 'hiddenable') {
                        $hiddenable = $value;

                        if ($value == 'no') {
                            $name = $field['dataIndx'];
                        }

                    }

                }

                $return[$name] = $field;
                $return[$name]['hidden'] = $hidden;
                $return[$name]['hiddenable'] = $hiddenable;
            }

        }

        return $return;
    }

    public function ajaxProcessOpenTargetController() {

        $this->paragridScript = $this->generateParaGridScript();
        $this->setAjaxMedia();

        $data = $this->createTemplate($this->table . '.tpl');
        $extraVars = Hook::exec('action' . $this->controller_name . 'TargetGetExtraVars', ['controller_type' => $this->controller_type], null, true);

        if (is_array($extraVars)) {
            
            foreach($extraVars as $plugin => $vars)  {
                if(is_array($vars)) {
                    foreach ($vars as $key => $value) {
                        $data->assign($key, $value);
                    }                    
                }
            }
            

        }

        if (is_array($this->extra_vars)) {

            foreach ($this->extra_vars as $key => $value) {
                $data->assign($key, $value);
            }

        }

        if (method_exists($this, 'get' . $this->className . 'Fields')) {
            $data->assign('paragridFields', is_array($this->configurationField) ? $this->configurationField : $this->{'get' . $this->className . 'Fields'}
                ());
        }

        $data->assign([
            'jsDef'              => $this->jsDef,
            'paragridScript'     => $this->paragridScript,
            'manageHeaderFields' => $this->manageHeaderFields,
            'customHeaderFields' => $this->manageFieldsVisibility($this->configurationField),
            'controller'         => $this->controller_name,
            'tableName'          => $this->table,
            'className'          => $this->className,
            'link'               => $this->context->link,
            'id_lang_default'    => $this->default_language,
            'languages'          => Language::getLanguages(false),
            'extraJs'            => $this->push_js_files,
            'extracss'           => $this->extracss,
            'tabs'               => $this->ajaxOptions,
            'bo_imgdir'          => __EPH_BASE_URI__ . 'content/backoffice/' . $this->bo_theme . '/img/',
        ]);

        $li = '<li id="uper' . $this->controller_name . '" data-self="' . $this->link_rewrite . '" data-name="' . $this->page_title . '" data-controller="AdminDashboard"><a href="#content' . $this->controller_name . '">' . $this->publicName . '</a><button type="button" class="close tabdetail" onClick="closeTabObject(\'' . $this->controller_name . '\');" data-id="uper' . $this->controller_name . '"><i class="fa-duotone fa-circle-xmark"></i></button></li>';
        $html = '<div id="content' . $this->controller_name . '" class="panel wpb_text_column wpb_content_element  wpb_slideInUp slideInUp wpb_start_animation animated col-lg-12" style="display: content;">' . $data->fetch() . '</div>';
        $result = [
            'li'         => $li,
            'html'       => $html,
            'page_title' => $this->page_title,
        ];

        if (_EPH_ADMIN_DEBUG_PROFILING_) {
            $result['profiling_mode'] = true;
            $result['profiling'] = $this->displayProfiling();
        }

        die(Tools::jsonEncode($result));
    }

    public function generateTabs(Context $context) {        

        return Tools::generateTabs($context);
    }

    protected function initTabPluginList() {

        $this->tab_plugins_list = BackTab::getTabPluginsList($this->id);

        if (is_array($this->tab_plugins_list['default_list']) && count($this->tab_plugins_list['default_list'])) {
            $this->filter_plugins_list = $this->tab_plugins_list['default_list'];
        } else

        if (is_array($this->tab_plugins_list['slider_list']) && count($this->tab_plugins_list['slider_list'])) {
            $this->addToolBarPluginsListButton();
            $this->addPageHeaderToolBarPluginsListButton();
            $this->context->smarty->assign(
                [
                    'tab_plugins_list'      => implode(',', $this->tab_plugins_list['slider_list']),
                    'admin_plugin_ajax_url' => $this->context->link->getAdminLink('AdminPlugins'),
                    'back_tab_plugins_list' => $this->context->link->getAdminLink(Tools::getValue('controller')),
                    'tab_plugins_open'      => (int) Tools::getValue('tab_plugins_open'),
                ]
            );
        }

    }

    protected function addToolBarPluginsListButton() {

        $this->filterTabPluginList();

        if (is_array($this->tab_plugins_list['slider_list']) && count($this->tab_plugins_list['slider_list'])) {
            $this->toolbar_btn['plugins-list'] = [
                'href' => '#',
                'desc' => $this->la('Recommended Plugins and Services'),
            ];
        }

    }

    protected function filterTabPluginList() {

        static $listIsFiltered = null;

        if ($listIsFiltered !== null) {
            return;
        }

        libxml_use_internal_errors(true);

        $allPluginList = [];

        libxml_clear_errors();

        $this->tab_plugins_list['slider_list'] = array_intersect($this->tab_plugins_list['slider_list'], $allPluginList);

        $listIsFiltered = true;
    }

    protected function addPageHeaderToolBarPluginsListButton() {

        $this->filterTabPluginList();

        if (is_array($this->tab_plugins_list['slider_list']) && count($this->tab_plugins_list['slider_list'])) {
            $this->page_header_toolbar_btn['plugins-list'] = [
                'href' => '#',
                'desc' => $this->la('Recommended Plugins and Services'),
            ];
        }

    }

    public function renderModal() {

        $modal_render = '';

        if (is_array($this->modals) && count($this->modals)) {

            foreach ($this->modals as $modal) {
                $this->context->smarty->assign($modal);
                $modal_render .= $this->context->smarty->fetch('modal.tpl');
            }

        }

        return $modal_render;
    }

    public function ajaxProcessEditObject() {

        $this->checkAccess();

        if ($this->tabAccess['edit'] == 1) {

            $idObject = Tools::getValue('idObject');

            $_GET[$this->identifier] = $idObject;
            $_GET['controller'] = $this->controller_name;
            $_GET['update' . $this->table] = "";

            $html = $this->renderForm();
            $li = '<li id="uperEdit' . $this->controller_name . '" data-controller="AdminDashboard"><a href="#contentEdit' . $this->controller_name . '">' . $this->editObject . '</a><button type="button" onClick="closeEditFormObject(\'' . $this->controller_name . '\');" class="close tabdetail" data-id="uperEdit' . $this->controller_name . '"><i class="fa-duotone fa-circle-xmark"></i></button></li>';
            $html = '<div id="contentEdit' . $this->controller_name . '" class="panel wpb_text_column wpb_content_element  wpb_slideInUp slideInUp wpb_start_animation animated col-lg-12" style="display; flow-root;">' . $html . '</div>';

            $result = [
                'success' => true,
                'li'      => $li,
                'html'    => $html,
            ];

            if (_EPH_ADMIN_DEBUG_PROFILING_) {
                $result['profiling_mode'] = true;
                $result['profiling'] = $this->displayProfiling();
            }

        } else {
            $result = [
                'success' => false,
                'message' => $this->la('Your administrative profile does not allow you to edit this object'),
            ];
        }

        die(Tools::jsonEncode($result));
    }

    public function ajaxProcessDuplicateObject() {

        $this->checkAccess();

        if ($this->tabAccess['edit'] == 1) {

            $idObject = Tools::getValue('idObject');
            $objet = new $this->className($idObject);
            $this->object = $objet->duplicateObject();

            $_GET[$this->identifier] = $this->object->id;
            $_GET['controller'] = $this->controller_name;
            $_GET['update' . $this->table] = "";

            $html = $this->renderForm();

            $li = '<li id="uperEdit' . $this->controller_name . '" data-controller="AdminDashboard"><a href="#contentEdit' . $this->controller_name . '">' . $this->editObject . '</a><button type="button" onClick="closeEditFormObject(\'' . $this->controller_name . '\');" class="close tabdetail" data-id="uperEdit' . $this->controller_name . '"><i class="fa-duotone fa-circle-xmark"></i></button></li>';
            $html = '<div id="contentEdit' . $this->controller_name . '" class="panel wpb_text_column wpb_content_element  wpb_slideInUp slideInUp wpb_start_animation animated col-lg-12" style="display; flow-root;">' . $html . '</div>';

            $result = [
                'success' => true,
                'li'      => $li,
                'html'    => $html,
            ];
        } else {
            $result = [
                'success' => false,
                'message' => $this->la('Your administrative profile does not allow you to edit this object'),
            ];
        }

        die(Tools::jsonEncode($result));
    }

    public function ajaxProcessAddObject() {

        $this->checkAccess();
        $_GET['controller'] = $this->controller_name;
        $_GET['add' . $this->table] = "";
        $_GET['id_parent'] = Tools::getValue('idParent', '');

        $scripHeader = Hook::exec('displayBackOfficeHeader', []);
        $scriptFooter = Hook::exec('displayBackOfficeFooter', []);
        $html = $this->renderForm();

        $li = '<li id="uperAdd' . $this->controller_name . '" data-controller="AdminDashboard"><a href="#contentAdd' . $this->controller_name . '">' . $this->editObject . '</a><button type="button" onClick="closeAddFormObject(\'' . $this->controller_name . '\')" class="close tabdetail" data-id="uperAdd' . $this->controller_name . '"><i class="fa-duotone fa-circle-xmark"></i></button></li>';
        $html = '<div id="contentAdd' . $this->controller_name . '" class="panel wpb_text_column wpb_content_element  wpb_slideInUp slideInUp wpb_start_animation animated col-lg-12" style="display; flow-root;">' . $scripHeader . $html . $scriptFooter . '</div>';

        $result = [
            'li'   => $li,

            'html' => $html,
        ];

        die(Tools::jsonEncode($result));
    }

    public function ajaxProcessDeleteObject() {

        $this->checkAccess();
        $idObject = Tools::getValue('idObject');

        $this->className = Tools::getValue('targetClass');

        $this->object = new $this->className($idObject);

        $this->object->delete();

        $result = [
            'success' => true,
            'message' => $this->la('The deletion was successful.'),
        ];

        die(Tools::jsonEncode($result));
    }

    public function ajaxProcessUpdateObject() {

        $this->checkAccess();
        $idObject = Tools::getValue($this->identifier);
        $this->object = new $this->className($idObject);

        $this->copyFromPost($this->object, $this->table);

        $this->beforeUpdate($this->object);

        $result = $this->object->update();

        $this->afterUpdate($this->object);

        if ($result) {

            $return = [
                'success' => true,
                'message' => sprintf($this->la('Object of type % was successfully updated'), $this->className),
            ];
        } else {
            $return = [
                'success' => false,
                'message' => $this->la('An error occurred while trying to update this object'),
            ];
        }

        die(Tools::jsonEncode($return));
    }

    public function ajaxProcessAddNewAjaxObject() {

        $this->checkAccess();
        $this->object = new $this->className();

        $this->copyFromPost($this->object, $this->table);

        $this->beforeAdd($this->object);

        $result = $this->object->update();

        $this->afterAdd($this->object);

        if ($result) {
            $this->afterAdd($this->object);

        } else {
            $return = [
                'success' => false,
                'message' => $this->la('An error occurred while trying to add this object'),
            ];
            die(Tools::jsonEncode($return));
        }

    }
    
    protected function la($string, $class = null, $addslashes = false, $htmlentities = true) {

        if ($class === null) {
            $class = substr(get_class($this), 0, -10);
        } else

        if (strtolower(substr($class, -10)) == 'controller') {
            /* classname has changed, from AdminXXX to AdminXXXController, so we remove 10 characters and we keep same keys */
            $class = substr($class, 0, -10);
        }

        return Translate::getAdminTranslation($string, $class, $addslashes, $htmlentities);
    }

    protected function isCached($template, $cacheId = null, $compileId = null) {

        Tools::enableCache();
        $res = $this->context->smarty->isCached($template, $cacheId, $compileId);
        Tools::restoreCacheSettings();

        return $res;
    }

    public function getWizardFieldsValues($obj) {

        foreach ($this->fields_form as $fieldset) {

            if (isset($fieldset['input'])) {

                foreach ($fieldset['input'] as $input) {

                    if (!isset($this->fields_value[$input['name']])) {

                        if (isset($input['lang']) && $input['lang']) {

                            foreach ($this->_languages as $language) {
                                $fieldValue = $this->getWizardFieldsValue($obj, $input['name'], $language['id_lang']);

                                if (empty($fieldValue)) {

                                    if (isset($input['default_value']) && is_array($input['default_value']) && isset($input['default_value'][$language['id_lang']])) {
                                        $fieldValue = $input['default_value'][$language['id_lang']];
                                    } else

                                    if (isset($input['default_value'])) {
                                        $fieldValue = $input['default_value'];
                                    }

                                }

                                $this->fields_value[$input['name']][$language['id_lang']] = $fieldValue;
                            }

                        } else {

                            $fieldValue = $this->getWizardFieldsValue($obj, $input['name']);

                            if ($fieldValue === false && isset($input['default_value'])) {
                                $this->fields_value[$input['name']] = $input['default_value'];
                            } else
                            if ($fieldValue === false) {
                                $this->fields_value[$input['name']] = [];
                            } else {
                                $this->fields_value[$input['name']] = $fieldValue;
                            }

                        }

                    }

                }

            }

        }

        foreach ($this->fields_form['steps'] as $fieldset) {

            if (isset($fieldset['input'])) {

                foreach ($fieldset['input'] as $input) {

                    if (!isset($this->fields_value[$input['name']])) {

                        if (isset($input['lang']) && $input['lang']) {

                            foreach ($this->_languages as $language) {
                                $fieldValue = $this->getWizardFieldsValue($obj, $input['name'], $language['id_lang']);

                                if (empty($fieldValue)) {

                                    if (isset($input['default_value']) && is_array($input['default_value']) && isset($input['default_value'][$language['id_lang']])) {
                                        $fieldValue = $input['default_value'][$language['id_lang']];
                                    } else

                                    if (isset($input['default_value'])) {
                                        $fieldValue = $input['default_value'];
                                    }

                                }

                                $this->fields_value[$input['name']][$language['id_lang']] = $fieldValue;
                            }

                        } else {

                            $fieldValue = $this->getWizardFieldsValue($obj, $input['name']);

                            if ($fieldValue === false && isset($input['default_value'])) {
                                $this->fields_value[$input['name']] = $input['default_value'];
                            } else
                            if ($fieldValue === false) {
                                $this->fields_value[$input['name']] = [];
                            } else {
                                $this->fields_value[$input['name']] = $fieldValue;
                            }

                        }

                    }

                }

            }

        }

        return $this->fields_value;
    }

    public function getFieldsValue($obj) {

        foreach ($this->fields_form as $fieldset) {

            if (isset($fieldset['form']['input'])) {

                foreach ($fieldset['form']['input'] as $input) {

                    if (!isset($this->fields_value[$input['name']])) {

                        if (isset($input['lang']) && $input['lang']) {

                            foreach ($this->_languages as $language) {
                                $fieldValue = $this->getFieldValue($obj, $input['name'], $language['id_lang']);

                                if (empty($fieldValue)) {

                                    if (isset($input['default_value']) && is_array($input['default_value']) && isset($input['default_value'][$language['id_lang']])) {
                                        $fieldValue = $input['default_value'][$language['id_lang']];
                                    } else

                                    if (isset($input['default_value'])) {
                                        $fieldValue = $input['default_value'];
                                    }

                                }

                                $this->fields_value[$input['name']][$language['id_lang']] = $fieldValue;
                            }

                        } else {

                            $fieldValue = $this->getFieldValue($obj, $input['name']);

                            if ($fieldValue === false && isset($input['default_value'])) {
                                $this->fields_value[$input['name']] = $input['default_value'];
                            } else
                            if ($fieldValue === false) {
                                $this->fields_value[$input['name']] = [];
                            } else {
                                $this->fields_value[$input['name']] = $fieldValue;
                            }

                        }

                    }

                }

            }

        }

        return $this->fields_value;
    }

    public function getWizardFieldsValue($obj, $key, $idLang = null) {

        if ($idLang) {
            $defaultValue = (isset($obj->id) && $obj->id && isset($obj->{$key}
                [$idLang])) ? $obj->{$key}
            [$idLang] : false;

        } else {
            $defaultValue = isset($obj->{$key}) ? $obj->{$key}
            : false;

        }

        return Tools::getValue($key . ($idLang ? '_' . $idLang : ''), $defaultValue);
    }

    public function renderForm() {

        if (!$this->default_form_language) {
            $this->getLanguages();
        }

        if (Tools::getValue('submitFormAjax')) {
            $this->content .= $this->context->smarty->fetch('form_submit_ajax.tpl');
        }

        $extraFields = Hook::exec('action' . $this->controller_name . 'FormModifier', [], null, true);

        if (is_array($extraFields) && count($extraFields)) {

            foreach ($extraFields as $plugin => $fields) {

                foreach ($fields as $field) {
                    $this->fields_form['input'][] = $field;
                }

            }

        }

        if ($this->fields_form && is_array($this->fields_form)) {

            if (!$this->multiple_fieldsets) {
                $this->fields_form = [['form' => $this->fields_form]];
            }

            // For add a fields via an override of $fields_form, use $fields_form_override

            if (is_array($this->fields_form_override) && !empty($this->fields_form_override)) {
                $this->fields_form[0]['form']['input'] = array_merge($this->fields_form[0]['form']['input'], $this->fields_form_override);
            }

            $fieldsValue = $this->getFieldsValue($this->object);

            if ($this->form_ajax) {
                $fieldsValue['ajax'] = $this->form_ajax;
            }

            $fieldsValue['action'] = $this->form_action;

            if ($this->tabList == true) {
                $this->tpl_form_vars['controller'] = Tools::getValue('controller');
                $this->tpl_form_vars['tabScript'] = $this->generateTabScript(Tools::getValue('controller'));
            }

            $has_editor = false;

            if ($this->composer_editor) {
                $has_editor = true;

            }

            $helper = new HelperForm($this);
            $this->setHelperDisplay($helper);
            $helper->controllerName = $this->controller_name;
            $helper->table = $this->table;
            $helper->header_title = $this->editObject;
            $helper->form_extraCss = $this->extracss;
            $helper->form_extraJs = $this->extraJs;
            $helper->js_def = $this->jsDef;
            $helper->fields_value = $fieldsValue;
            $helper->submit_action = $this->submit_action;
            $helper->tpl_vars = $this->getTemplateFormVars();
            $helper->ajax_submit = $this->ajax_submit;
            $helper->tagHeader = $this->editObject;
            $helper->has_editor = $has_editor;
            $helper->show_cancel_button = (isset($this->show_form_cancel_button)) ? $this->show_form_cancel_button : ($this->display == 'add' || $this->display == 'edit');

            !is_null($this->base_tpl_form) ? $helper->base_tpl = $this->base_tpl_form : '';

            !is_null($this->base_tpl_form) ? $helper->base_tpl = $this->base_tpl_form : '';

            $form = $helper->generateForm($this->fields_form);

            return $form;
        }

    }

    public function renderFormWizard() {

        if (!$this->default_form_language) {
            $this->getLanguages();
        }

        if (Tools::getValue('submitFormAjax')) {
            $this->content .= $this->context->smarty->fetch('form_submit_ajax.tpl');
        }

        if ($this->fields_form && is_array($this->fields_form)) {

            $fieldsValue = $this->getWizardFieldsValues($this->object);

            if ($this->form_ajax) {
                $fieldsValue['ajax'] = $this->form_ajax;
            }

            $fieldsValue['action'] = $this->form_action;

            $has_editor = false;

            if ($this->composer_editor) {
                $has_editor = true;
            }

            $helper = new HelperFormWizard($this);
            $this->setHelperDisplay($helper);
            $helper->controllerName = $this->controller_name;
            $helper->header_title = $this->editObject;
            $helper->className = $this->className;
            $helper->form_extraCss = $this->extracss;
            $helper->form_extraJs = $this->extraJs;
            $helper->js_def = $this->jsDef;
            $helper->fields_value = $fieldsValue;
            $helper->submit_action = $this->submit_action;
            $helper->tpl_vars = $this->getTemplateFormVars();
            $helper->tagHeader = $this->editObject;
            $helper->has_editor = $has_editor;
            $helper->js_def = $this->js_def;
            $helper->show_cancel_button = (isset($this->show_form_cancel_button)) ? $this->show_form_cancel_button : ($this->display == 'add' || $this->display == 'edit');

            !is_null($this->base_tpl_form) ? $helper->base_tpl = $this->base_tpl_form : '';

            $form = $helper->generateForm($this->fields_form);

            return $form;
        }

    }

    public function getLanguages() {

        $cookie = $this->context->cookie;
        $this->allow_employee_form_lang = (int) Configuration::get('EPH_BO_ALLOW_EMPLOYEE_FORM_LANG');

        if ($this->allow_employee_form_lang && !$cookie->employee_form_lang) {
            $cookie->employee_form_lang = (int) $this->default_language;
        }

        $langExists = false;
        $this->_languages = Language::getLanguages(false);

        foreach ($this->_languages as $lang) {

            if (isset($cookie->employee_form_lang) && $cookie->employee_form_lang == $lang['id_lang']) {
                $langExists = true;
            }

        }

        $this->default_form_language = $langExists ? (int) $cookie->employee_form_lang : (int) $this->default_language;

        foreach ($this->_languages as $k => $language) {
            $this->_languages[$k]['is_default'] = (int) ($language['id_lang'] == $this->default_form_language);
        }

        return $this->_languages;
    }

    public function setHelperDisplay(Helper $helper) {

        // tocheck

        if ($this->object && $this->object->id) {
            $helper->id = $this->object->id;
        }

        // @todo : move that in Helper
        $helper->title = '';
        $helper->toolbar_btn = $this->toolbar_btn;
        $helper->show_toolbar = $this->show_toolbar;
        $helper->toolbar_scroll = $this->toolbar_scroll;
        $helper->override_folder = $this->tpl_folder;
        $helper->currentIndex = static::$currentIndex;
        $helper->className = $this->className;
        $helper->table = $this->table;
        $helper->name_controller = Tools::getValue('controller');
        $helper->identifier = $this->identifier;
        $helper->token = $this->token;
        $helper->languages = $this->_languages;
        $helper->default_form_language = $this->default_form_language;
        $helper->allow_employee_form_lang = $this->allow_employee_form_lang;
        $helper->controller_name = $this->controller_name;
        $helper->bootstrap = $this->bootstrap;

        $this->helper = $helper;
    }

    public function getTemplateFormVars() {

        return $this->tpl_form_vars;
    }

    public function ajaxProcessGetAccountTypeRequest() {

        $type = Tools::getValue('type');
        fwrite($file, $type);

        switch ($type) {
        case 'Banks':
            die(Tools::jsonEncode(StdAccount::getBankStdAccount()));
            break;
        case 'Profits':
            die(Tools::jsonEncode(StdAccount::getProfitsStdAccount()));
            break;
        case 'Expenses':
            die(Tools::jsonEncode(StdAccount::getExpensesStdAccount()));
            break;
        case 'VAT':
            die(Tools::jsonEncode(StdAccount::getVATStdAccount()));
            break;
        case 'Supplier':
            die(Tools::jsonEncode(StdAccount::getAccountByidType(4)));
            break;
        case 'Customer':
            die(Tools::jsonEncode(StdAccount::getAccountByidType(5)));
            break;
        case 'Others':
            die(Tools::jsonEncode(StdAccount::getAccountByidType(6)));
            break;
        case 'Capital':
            die(Tools::jsonEncode(StdAccount::getAccountByidType(1)));
            break;
        }

    }

    public function getFieldValue($obj, $key, $idLang = null) {

        if ($idLang) {
            $defaultValue = (isset($obj->id) && $obj->id && isset($obj->{$key}
                [$idLang])) ? $obj->{$key}
            [$idLang] : false;

        } else {
            $defaultValue = isset($obj->{$key}) ? $obj->{$key}
            : false;

        }

        return Tools::getValue($key . ($idLang ? '_' . $idLang : ''), $defaultValue);
    }

    public function getExportFields() {

        if (method_exists($this, 'getFields')) {

            $fields = [];
            $gridFields = $this->{'getFields'}

            ();

            if (is_array($gridFields) && count($gridFields)) {

                foreach ($gridFields as $grifField) {

                    if (isset($grifField['hidden']) && $grifField['hidden'] && isset($grifField['hiddenable']) && $grifField['hiddenable'] == 'no') {
                        continue;
                    }

                    if (isset($grifField['dataIndx'])) {
                        $fields[$grifField['dataIndx']] = $grifField['title'];
                    }

                }

            }

            return $fields;

        }

        return false;

    }

    public function getUpdatableFields() {

        $class = new $this->className();
        return $class->getUpdatableFields();

    }

    public function getUpdatableFieldType($dataIndx) {

        $gridFields = $this->getFields();

        if (is_array($gridFields) && count($gridFields)) {

            foreach ($gridFields as $grifField) {

                if ($grifField['dataIndx'] == $dataIndx) {
                    return $grifField;
                }

            }

        }

    }

    public function removeRequestFields($requests) {

        $objects = [];
        $gridFields = $this->getFields();

        if (is_array($gridFields)) {
            $fields = [];

            foreach ($gridFields as $grifField) {
                $fields[] = $grifField['dataIndx'];
            }

            foreach ($requests as $key => $object) {

                foreach ($object as $field => $value) {

                    if (in_array($field, $fields)) {
                        $objects[$key][$field] = $value;
                    }

                }

            }

        }

        return $objects;

    }

    public function getExportFormatFields() {

        if (method_exists($this, 'getFields')) {

            $fields = [];
            $gridFields = $this->{'getFields'}

            ();

            if (is_array($gridFields) && count($gridFields)) {

                foreach ($gridFields as $grifField) {

                    if (isset($grifField['hidden']) && $grifField['hidden'] && isset($grifField['hiddenable']) && $grifField['hiddenable'] == 'no') {
                        continue;
                    }

                    if (isset($grifField['dataIndx'])) {

                        if (isset($grifField['exWidth'])) {
                            $fields[$grifField['dataIndx']]['width'] = $grifField['exWidth'];
                        }

                        if (isset($grifField['halign'])) {
                            $fields[$grifField['dataIndx']]['halign'] = $grifField['halign'];
                        } else {
                            $fields[$grifField['dataIndx']]['halign'] = 'Alignment::HORIZONTAL_LEFT';
                        }

                        if (isset($grifField['numberFormat'])) {
                            $fields[$grifField['dataIndx']]['numberFormat'] = $grifField['numberFormat'];
                        }

                        if (isset($grifField['dataType']) && $grifField['dataType'] == 'date') {
                            $fields[$grifField['dataIndx']]['date'] = true;

                        }

                        if (isset($grifField['exportType']) && $grifField['exportType'] == 'Image') {
                            $fields[$grifField['dataIndx']]['image'] = true;

                        }

                    }

                }

            }

            return $fields;

        }

        return false;

    }

    public function getRequest($identifier = null) {

        $request = Hook::exec('action' . $this->controller_name . 'getRequestModifier', ['paramRequest' => $this->paramRequest], null, true);

        if (is_array($request)) {

            foreach ($request as $plugin => $result) {

                if (is_array($result)) {
                    $this->paramRequest = $result;
                }

            }

        }

        return null;
    }

    protected function ajaxDie($value = null, $controller = null, $method = null) {

        if ($controller === null) {
            $controller = get_class($this);
        }

        if ($method === null) {
            $bt = debug_backtrace();
            $method = $bt[1]['function'];
        }

        Hook::exec('actionBeforeAjaxDie', ['controller' => $controller, 'method' => $method, 'value' => $value]);
        Hook::exec('actionBeforeAjaxDie' . $controller . $method, ['value' => $value]);

        die($value);
    }

    private function getMemoryColor($n) {

        $n /= 1048576;

        if ($n > 3) {
            return '<span style="color:red">' . sprintf('%0.2f', $n) . '</span>';
        } else

        if ($n > 1) {
            return '<span style="color:#EF8B00">' . sprintf('%0.2f', $n) . '</span>';
        } else

        if (round($n, 2) > 0) {
            return '<span style="color:green">' . sprintf('%0.2f', $n) . '</span>';
        }

        return '<span style="color:green">-</span>';
    }

    private function getPeakMemoryColor($n) {

        $n /= 1048576;

        if ($n > 16) {
            return '<span style="color:red">' . sprintf('%0.1f', $n) . '</span>';
        }

        if ($n > 12) {
            return '<span style="color:#EF8B00">' . sprintf('%0.1f', $n) . '</span>';
        }

        return '<span style="color:green">' . sprintf('%0.1f', $n) . '</span>';
    }

    private function displaySQLQueries($n) {

        if ($n > 150) {
            return '<span style="color:red">' . $n . ' queries</span>';
        }

        if ($n > 100) {
            return '<span style="color:#EF8B00">' . $n . ' queries</span>';
        }

        return '<span style="color:green">' . $n . ' quer' . ($n == 1 ? 'y' : 'ies') . '</span>';
    }

    private function displayRowsBrowsed($n) {

        if ($n > 400) {
            return '<span style="color:red">' . $n . ' rows browsed</span>';
        }

        if ($n > 100) {
            return '<span style="color:#EF8B00">' . $n . '  rows browsed</span>';
        }

        return '<span style="color:green">' . $n . ' row' . ($n == 1 ? '' : 's') . ' browsed</span>';
    }

    private function getPhpVersionColor($version) {

        if (version_compare($version, '5.3') < 0) {
            return '<span style="color:red">' . $version . ' (Upgrade strongly recommended)</span>';
        } else

        if (version_compare($version, '5.4') < 0) {
            return '<span style="color:#EF8B00">' . $version . ' (Consider upgrading)</span>';
        }

        return '<span style="color:green">' . $version . ' (OK)</span>';
    }

    private function getMySQLVersionColor($version) {

        if (version_compare($version, '5.5') < 0) {
            return '<span style="color:red">' . $version . ' (Upgrade strongly recommended)</span>';
        } else

        if (version_compare($version, '5.6') < 0) {
            return '<span style="color:#EF8B00">' . $version . ' (Consider upgrading)</span>';
        }

        return '<span style="color:green">' . $version . ' (OK)</span>';
    }

    private function getLoadTimeColor($n, $kikoo = false) {

        if ($n > 1.6) {
            return '<span style="color:red">' . round($n * 1000) . '</span>' . ($kikoo ? ' ms - You\'d better run your shop on a toaster' : '');
        } else

        if ($n > 0.8) {
            return '<span style="color:#EF8B00">' . round($n * 1000) . '</span>' . ($kikoo ? ' ms - OK... for a shared hosting' : '');
        } else

        if ($n > 0) {
            return '<span style="color:green">' . round($n * 1000) . '</span>' . ($kikoo ? ' ms - Unicorn powered webserver!' : '');
        }

        return '<span style="color:green">-</span>' . ($kikoo ? ' ms - Faster than light' : '');
    }

    private function getTotalQueriyingTimeColor($n) {

        if ($n >= 100) {
            return '<span style="color:red">' . $n . '</span>';
        } else

        if ($n >= 50) {
            return '<span style="color:#EF8B00">' . $n . '</span>';
        }

        return '<span style="color:green">' . $n . '</span>';
    }

    private function getNbQueriesColor($n) {

        if ($n >= 100) {
            return '<span style="color:red">' . $n . '</span>';
        } else

        if ($n >= 50) {
            return '<span style="color:#EF8B00">' . $n . '</span>';
        }

        return '<span style="color:green">' . $n . '</span>';
    }

    private function getTimeColor($n) {

        if ($n > 4) {
            return 'style="color:red"';
        }

        if ($n > 2) {
            return 'style="color:#EF8B00"';
        }

        return 'style="color:green"';
    }

    private function getQueryColor($n) {

        if ($n > 5) {
            return 'style="color:red"';
        }

        if ($n > 2) {
            return 'style="color:#EF8B00"';
        }

        return 'style="color:green"';
    }

    private function getTableColor($n) {

        if ($n > 30) {
            return 'style="color:red"';
        }

        if ($n > 20) {
            return 'style="color:#EF8B00"';
        }

        return 'style="color:green"';
    }

    private function getObjectModelColor($n) {

        if ($n > 50) {
            return 'style="color:red"';
        }

        if ($n > 10) {
            return 'style="color:#EF8B00"';
        }

        return 'style="color:green"';
    }

    protected function stamp($block) {

        return ['block' => $block, 'memory_usage' => memory_get_usage(), 'peak_memory_usage' => memory_get_peak_usage(), 'time' => microtime(true)];
    }

    private function getVarSize($var) {

        $start_memory = memory_get_usage();
        try {
            $tmp = json_decode(json_encode($var));
        } catch (Exception $e) {
            $tmp = $this->getVarData($var);
        }

        $size = memory_get_usage() - $start_memory;
        return $size;
    }

    private function getVarData($var) {

        if (is_object($var)) {
            return $var;
        }

        return (string) $var;
    }

    protected function processProfilingData() {

        global $start_time;

        // Including a lot of files uses memory

        foreach (get_included_files() as $file) {
            $this->total_filesize += filesize($file);
        }

        // Sum querying time

        foreach (Db::getInstance()->queries as $data) {
            $this->total_query_time += $data['time'];
        }

        foreach ($GLOBALS as $key => $value) {

            if ($key != 'GLOBALS') {
                $this->total_global_var_size += ($size = $this->getVarSize($value));

                if ($size > 1024) {
                    $this->global_var_size[$key] = round($size / 1024);
                }

            }

        }

        arsort($this->global_var_size);

        $cache = Cache::retrieveAll();
        $this->total_cache_size = $this->getVarSize($cache);

        // Retrieve plugin perfs
        $result = Db::getInstance()->ExecuteS('
            SELECT *
            FROM ' . _DB_PREFIX_ . 'plugins_perfs
            WHERE session = ' . (int) Plugin::$_log_plugins_perfs_session . '
            AND time_start >= ' . (float) $start_time . '
            AND time_end <= ' . (float) $this->profiler[count($this->profiler) - 1]['time']
        );

        foreach ($result as $row) {
            $tmp_time = $row['time_end'] - $row['time_start'];
            $tmp_memory = $row['memory_end'] - $row['memory_start'];
            $this->total_plugins_time += $tmp_time;
            $this->total_plugins_memory += $tmp_memory;

            if (!isset($this->plugins_perfs[$row['plugin']])) {
                $this->plugins_perfs[$row['plugin']] = ['time' => 0, 'memory' => 0, 'methods' => []];
            }

            $this->plugins_perfs[$row['plugin']]['time'] += $tmp_time;
            $this->plugins_perfs[$row['plugin']]['methods'][$row['method']]['time'] = $tmp_time;
            $this->plugins_perfs[$row['plugin']]['memory'] += $tmp_memory;
            $this->plugins_perfs[$row['plugin']]['methods'][$row['method']]['memory'] = $tmp_memory;

            if (!isset($this->hooks_perfs[$row['method']])) {
                $this->hooks_perfs[$row['method']] = ['time' => 0, 'memory' => 0, 'plugins' => []];
            }

            $this->hooks_perfs[$row['method']]['time'] += $tmp_time;
            $this->hooks_perfs[$row['method']]['plugins'][$row['plugin']]['time'] = $tmp_time;
            $this->hooks_perfs[$row['method']]['memory'] += $tmp_memory;
            $this->hooks_perfs[$row['method']]['plugins'][$row['plugin']]['memory'] = $tmp_memory;
        }

        uasort($this->plugins_perfs, 'phenyxshop_querytime_sort');
        uasort($this->hooks_perfs, 'phenyxshop_querytime_sort');

        $queries = Db::getInstance()->queries;
        uasort($queries, 'phenyxshop_querytime_sort');

        foreach ($queries as $data) {
            $query_row = [
                'time'     => $data['time'],
                'query'    => $data['query'],
                'location' => str_replace('\\', '/', substr($data['stack'][0]['file'], strlen(_EPH_ROOT_DIR_))) . ':' . $data['stack'][0]['line'],
                'filesort' => false,
                'rows'     => 1,
                'group_by' => false,
                'stack'    => [],
            ];

            if (preg_match('/^\s*select\s+/i', $data['query'])) {
                $explain = Db::getInstance()->executeS('explain ' . $data['query']);

                if (isset($explain[0]['Extra']) && stristr($explain[0]['Extra'], 'filesort')) {
                    $query_row['filesort'] = true;
                }

                foreach ($explain as $row) {
                    $query_row['rows'] *= $row['rows'];
                }

                if (stristr($data['query'], 'group by') && !preg_match('/(avg|count|min|max|group_concat|sum)\s*\(/i', $data['query'])) {
                    $query_row['group_by'] = true;
                }

            }

            array_shift($data['stack']);

            foreach ($data['stack'] as $call) {
                $query_row['stack'][] = str_replace('\\', '/', substr($call['file'], strlen(_EPH_ROOT_DIR_))) . ':' . $call['line'];
            }

            $this->array_queries[] = $query_row;
        }

        uasort(PhenyxObjectModel::$debug_list, function ($b, $a) {

            if (count($a) < count($b)) {
                return 1;
            }

            return -1;
        });
        arsort(Db::getInstance()->tables);
        arsort(Db::getInstance()->uniqQueries);
    }

    protected function displayProfilingLinks() {

        $this->content_ajax .= '
        <div id="profiling_link" class="subTabs col-lg-12">
            <ul>
                <li><a href="#stopwatch">Stopwatch SQL</a></li>
                <li><a href="#sql_doubles">Doubles</a></li>
                <li><a href="#stress_tables">Tables stress</a></li>
                ' . (isset(PhenyxObjectModel::$debug_list) ? '<li><a href="#objectModels">ObjectModel instances</a></li>' : '') . '
                <li><a href="#includedFiles">Included Files</a></li>
            </ul>
        <div id="tabs-profilling-content" class="tabs-controller-content">';
    }

    protected function displayProfilingStyle() {

        $this->content_ajax .= '

        <script type="text/javascript" src="https://cdn.rawgit.com/drvic10k/bootstrap-sortable/1.11.2/Scripts/bootstrap-sortable.js"></script>';
    }

    protected function displayProfilingSummary() {

        global $start_time;

        $this->content_ajax .= '
        <div class="col-4">
            <table class="table table-condensed">
                <tr><td>Load time</td><td>' . $this->getLoadTimeColor($this->profiler[count($this->profiler) - 1]['time'] - $start_time, true) . '</td></tr>
                <tr><td>Querying time</td><td>' . $this->getTotalQueriyingTimeColor(round(1000 * $this->total_query_time)) . ' ms</span>
                <tr><td>Queries</td><td>' . $this->getNbQueriesColor(count($this->array_queries)) . '</td></tr>
                <tr><td>Memory peak usage</td><td>' . $this->getPeakMemoryColor($this->profiler[count($this->profiler) - 1]['peak_memory_usage']) . ' Mb</td></tr>
                <tr><td>Included files</td><td>' . count(get_included_files()) . ' files - ' . $this->getMemoryColor($this->total_filesize) . ' Mb</td></tr>
                <tr><td>ephenyx cache</td><td>' . $this->getMemoryColor($this->total_cache_size) . ' Mb</td></tr>
                <tr><td><a href="javascript:void(0);" onclick="$(\'.global_vars_detail\').toggle();">Global vars</a></td><td>' . $this->getMemoryColor($this->total_global_var_size) . ' Mb</td></tr>';

        foreach ($this->global_var_size as $global => $size) {
            $this->content_ajax .= '<tr class="global_vars_detail" style="display:none"><td>- global $' . $global . '</td><td>' . $size . 'k</td></tr>';
        }

        $this->content_ajax .= '
            </table>
        </div>';
    }

    protected function displayProfilingConfiguration() {

        $this->content_ajax .= '
        <div class="col-4">
            <table class="table table-condensed">
                <tr><td>ephenyx version</td><td>' . _EPH_VERSION_ . '</td></tr>
                <tr><td>PhenyxShop (emulated) version</td><td>' . _EPH_VERSION_ . '</td></tr>
                <tr><td>PHP version</td><td>' . $this->getPhpVersionColor(phpversion()) . '</td></tr>
                <tr><td>MySQL version</td><td>' . $this->getMySQLVersionColor(Db::getInstance()->getVersion()) . '</td></tr>
                <tr><td>Memory limit</td><td>' . ini_get('memory_limit') . '</td></tr>
                <tr><td>Max execution time</td><td>' . ini_get('max_execution_time') . 's</td></tr>
                <tr><td>Smarty cache</td><td><span style="color:' . (Configuration::get('EPH_SMARTY_CACHE') ? 'green">enabled' : 'red">disabled') . '</td></tr>
                <tr><td>Smarty Compilation</td><td><span style="color:' . (Configuration::get('EPH_SMARTY_FORCE_COMPILE') == 0 ? 'green">never recompile' : (Configuration::get('EPH_SMARTY_FORCE_COMPILE') == 1 ? '#EF8B00">auto' : 'red">force compile')) . '</td></tr>
            </table>
        </div>';
    }

    protected function displayProfilingRun() {

        global $start_time;

        $this->content_ajax .= '
        <div class="col-4">
            <table class="table table-condensed">
                <tr><th>&nbsp;</th><th>Time</th><th>Cumulated Time</th><th>Memory Usage</th><th>Memory Peak Usage</th></tr>';
        $last = ['time' => $start_time, 'memory_usage' => 0];

        foreach ($this->profiler as $row) {

            if ($row['block'] == 'checkAccess' && $row['time'] == $last['time']) {
                continue;
            }

            $this->content_ajax .= '<tr>
                <td>' . $row['block'] . '</td>
                <td>' . $this->getLoadTimeColor($row['time'] - $last['time']) . ' ms</td>
                <td>' . $this->getLoadTimeColor($row['time'] - $start_time) . ' ms</td>
                <td>' . $this->getMemoryColor($row['memory_usage'] - $last['memory_usage']) . ' Mb</td>
                <td>' . $this->getMemoryColor($row['peak_memory_usage']) . ' Mb</td>
            </tr>';
            $last = $row;
        }

        $this->content_ajax .= '
            </table>
        </div>';
    }

    protected function displayProfilingHooks() {

        $count_hooks = count($this->hooks_perfs);

        $this->content_ajax .= '
        <div class="col-lg-6">
            <table class="table table-condensed">
                <tr>
                    <th>Hook</th>
                    <th>Time</th>
                    <th>Memory Usage</th>
                </tr>';

        foreach ($this->hooks_perfs as $hook => $hooks_perfs) {
            $this->content_ajax .= '
                <tr>
                    <td>
                        <a href="javascript:void(0);" onclick="$(\'.' . $hook . '_plugins_details\').toggle();">' . $hook . '</a>
                    </td>
                    <td>
                        ' . $this->getLoadTimeColor($hooks_perfs['time']) . ' ms
                    </td>
                    <td>
                        ' . $this->getMemoryColor($hooks_perfs['memory']) . ' Mb
                    </td>
                </tr>';

            foreach ($hooks_perfs['plugins'] as $plugin => $perfs) {
                $this->content_ajax .= '
                <tr class="' . $hook . '_plugins_details" style="background-color:#EFEFEF;display:none">
                    <td>
                        =&gt; ' . $plugin . '
                    </td>
                    <td>
                        ' . $this->getLoadTimeColor($perfs['time']) . ' ms
                    </td>
                    <td>
                        ' . $this->getMemoryColor($perfs['memory']) . ' Mb
                    </td>
                </tr>';
            }

        }

        $this->content_ajax .= '  <tr>
                    <th><b>' . ($count_hooks == 1 ? '1 hook' : (int) $count_hooks . ' hooks') . '</b></th>
                    <th>' . $this->getLoadTimeColor($this->total_plugins_time) . ' ms</th>
                    <th>' . $this->getMemoryColor($this->total_plugins_memory) . ' Mb</th>
                </tr>
            </table>
        </div>';
    }

    protected function displayProfilingPlugins() {

        $count_plugins = count($this->plugins_perfs);

        $this->content_ajax .= '
        <div class="col-lg-6">
            <table class="table table-condensed">
                <tr>
                    <th>Plugin</th>

                    <th>Time</th>
                    <th>Memory Usage</th>
                </tr>';

        foreach ($this->plugins_perfs as $plugin => $plugins_perfs) {
            $this->content_ajax .= '
                <tr>
                    <td>
                        <a href="javascript:void(0);" onclick="$(\'.' . $plugin . '_hooks_details\').toggle();">' . $plugin . '</a>
                    </td>
                    <td>
                        ' . $this->getLoadTimeColor($plugins_perfs['time']) . ' ms
                    </td>
                    <td>
                        ' . $this->getMemoryColor($plugins_perfs['memory']) . ' Mb
                    </td>
                </tr>';

            foreach ($plugins_perfs['methods'] as $hook => $perfs) {
                $this->content_ajax .= '
                <tr class="' . $plugin . '_hooks_details" style="background-color:#EFEFEF;display:none">
                    <td>
                        =&gt; ' . $hook . '
                    </td>
                    <td>
                        ' . $this->getLoadTimeColor($perfs['time']) . ' ms
                    </td>
                    <td>
                        ' . $this->getMemoryColor($perfs['memory']) . ' Mb
                    </td>
                </tr>';
            }

        }

        $this->content_ajax .= '  <tr>
                    <th><b>' . ($count_plugins == 1 ? '1 plugin' : (int) $count_plugins . ' plugins') . '</b></th>
                    <th>' . $this->getLoadTimeColor($this->total_plugins_time) . ' ms</th>
                    <th>' . $this->getMemoryColor($this->total_plugins_memory) . ' Mb</th>
                </tr>
            </table>
        </div>';
    }

    protected function displayProfilingStopwatch() {

        $this->content_ajax .= '
        <div id="stopwatch">
            <h2><a name="stopwatch">Stopwatch SQL - ' . count($this->array_queries) . ' queries</a></h2>
            <table class="table table-condensed table-bordered sortable col-lg-12">
                <thead>
                    <tr>
                        <th style="width:50%">Query</th>
                        <th style="width:10%">Time (ms)</th>
                        <th style="width:10%">Rows</th>
                        <th style="width:5%">Filesort</th>
                        <th style="width:5%">Group By</th>
                        <th style="width:20%">Location</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($this->array_queries as $data) {
            $callstack = implode('<br>', $data['stack']);
            $callstack_md5 = md5($callstack);

            $this->content_ajax .= '
                <tr>
                    <td class="pre" style="width:50%; display:table-cell">' . preg_replace("/(^[\s]*)/m", "", htmlspecialchars($data['query'], ENT_NOQUOTES, 'utf-8', false)) . '</td>
                    <td style="width:10%"><span ' . $this->getTimeColor($data['time'] * 1000) . '>' . (round($data['time'] * 1000, 1) < 0.1 ? '< 1' : round($data['time'] * 1000, 1)) . '</span></td>
                    <td>' . (int) $data['rows'] . '</td>
                    <td>' . ($data['filesort'] ? '<span style="color:red">Yes</span>' : '') . '</td>
                    <td>' . ($data['group_by'] ? '<span style="color:red">Yes</span>' : '') . '</td>
                    <td>
                        <a href="javascript:void(0);" onclick="$(\'#callstack_' . $callstack_md5 . '\').toggle();">' . $data['location'] . '</a>
                        <div id="callstack_' . $callstack_md5 . '" style="display:none">' . implode('<br>', $data['stack']) . '</div>
                    </td>
                </tr>';
        }

        $this->content_ajax .= '</table>

        </div>';
    }

    protected function displayProfilingDoubles() {

        $this->content_ajax .= '<div id="sql_doubles">
        <h2><a name="doubles">Doubles</a></h2>
            <table class="table table-condensed">';

        foreach (Db::getInstance()->uniqQueries as $q => $nb) {

            if ($nb > 1) {
                $this->content_ajax .= '<tr><td><span ' . $this->getQueryColor($nb) . '>' . $nb . '</span></td><td class="pre"><pre>' . $q . '</pre></td></tr>';
            }

        }

        $this->content_ajax .= '</table>
        </div>';
    }

    protected function displayProfilingTableStress() {

        $this->content_ajax .= '<div id="stress_tables">
        <h2><a name="tables">Tables stress</a></h2>
        <table class="table table-condensed">';

        foreach (Db::getInstance()->tables as $table => $nb) {
            $this->content_ajax .= '<tr><td><span ' . $this->getTableColor($nb) . '>' . $nb . '</span> ' . $table . '</td></tr>';
        }

        $this->content_ajax .= '</table>
        </div>';
    }

    protected function displayProfilingObjectModel() {

        $this->content_ajax .= '
        <div id="objectModels">
            <h2><a name="objectModels">ObjectModel instances</a></h2>
            <table class="table table-condensed">
                <tr><th>Name</th><th>Instances</th><th>Source</th></tr>';

        foreach (PhenyxObjectModel::$debug_list as $class => $info) {
            $this->content_ajax .= '<tr>
                    <td>' . $class . '</td>
                    <td><span ' . $this->getObjectModelColor(count($info)) . '>' . count($info) . '</span></td>
                    <td>';

            foreach ($info as $trace) {
                $this->content_ajax .= str_replace([_EPH_ROOT_DIR_, '\\'], ['', '/'], $trace['file']) . ' [' . $trace['line'] . ']<br />';
            }

            $this->content_ajax .= '  </td>
                </tr>';
        }

        $this->content_ajax .= '</table>
        </div>';
    }

    protected function displayProfilingFiles() {

        $i = 0;

        $this->content_ajax .= '<div id="includedFiles">
        <h2><a name="includedFiles">Included Files</a></h2>
        <table class="table table-condensed">
            <tr><th>#</th><th>Filename</th></tr>';

        foreach (get_included_files() as $file) {
            $file = str_replace('\\', '/', str_replace(_EPH_ROOT_DIR_, '', $file));

            if (strpos($file, '/tools/profiling/') === 0) {
                continue;
            }

            $this->content_ajax .= '<tr><td>' . (++$i) . '</td><td>' . $file . '</td></tr>';
        }

        $this->content_ajax .= '</table>
        </div>';
    }

    public function displayProfiling() {

        $this->profiler[] = $this->stamp('display');
        // Process all profiling data
        $this->processProfilingData();

        // Add some specific style for profiling information
        //$this->displayProfilingStyle();

        $this->content_ajax .= '<div id="phenyxshop_profiling" class="bootstrap">';

        $this->content_ajax .= 'Summary' . '<div class="row">';
        $this->displayProfilingSummary();
        $this->displayProfilingConfiguration();
        $this->displayProfilingRun();
        $this->content_ajax .= '</div><div class="row">';
        $this->displayProfilingHooks();
        $this->displayProfilingPlugins();
        $this->displayProfilingLinks();

        $this->displayProfilingStopwatch();
        $this->displayProfilingDoubles();
        $this->displayProfilingTableStress();

        if (isset(PhenyxObjectModel::$debug_list)) {
            $this->displayProfilingObjectModel();
        }

        $this->displayProfilingFiles();

        $this->content_ajax .= '</div>';

        return $this->content_ajax;

    }

}
