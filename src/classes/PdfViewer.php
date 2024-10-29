<?php

/**
 * Class PdfViewer
 *
 * @since 2.1.0.0
 */
class PdfViewer {
    
    public $name;
    
    public $pages = [];

	public $tableOfContent = [];
    
    public $tableOfContentCloseOnClick = 1;
    
    public $thumbsCloseOnClick = 1;
    
    public $deeplinkingEnabled = 0;
    
    public $deeplinkingPrefix;
    
    public $assets = [];

	public $pdfUrl;
    
    public $pdfBrowserViewerIfMobile = 0;
    
    public $pdfBrowserViewerIfMobile = false;
    public $pdfBrowserViewerIfIE = false;
    public $pdfBrowserViewerFullscreen = true;
    public $pdfBrowserViewerFullscreenTarget = "_blank";
    public $rangeChunkSize = 64;
    public $disableRange = false;
    public $disableStream = true;
    public $disableAutoFetch = true;
    public $pdfAutoLinks = false;

    public $htmlLayer = true; // to implement

    public $rightToLeft = false;

    //page that will be displayed when the book starts
    public $startPage = 0;

    //if the sound is enabled
    public $sound = true;

    public $backgroundColor = "rgb(81, 85, 88)";
    public $backgroundImage = "";
    public $backgroundPattern = "";
    public $backgroundTransparent = false;

    //book default settings
    public $thumbSize = 130;

    public $loadAllPages = false;
    public $loadPagesF = 2;
    public $loadPagesB = 1;

    public $autoplayOnStart = false;
    public $autoplayInterval = 3000;
    public $autoplayLoop = true;

    //UI settings

    public $skin = "light"; //"dark"; "light"; "gradient"
    public $layout = "1"; //"1"; "2"; "3"; "4"

    public $menuOverBook = false;
    public $menuFloating = false;
    public $menuBackground = '';
    public $menuShadow = '';
    public $menuMargin = 0;
    public $menuPadding = 0;
    public $menuTransparent = false;

    public $menu2OverBook = true;
    public $menu2Floating = false;
    public $menu2Background = '';
    public $menu2Shadow = '';
    public $menu2Margin = 0;
    public $menu2Padding = 0;
    public $menu2Transparent = true;

    public $skinColor = '';
    public $skinBackground = '';

    // menu buttons
    public $btnColor = '';
    public $btnBackground = 'none';
    public $btnSize = 14;
    public $btnRadius = 2;
    public $btnMargin = 2;
    public $btnPaddingV = 10;
    public $btnPaddingH = 10;
    public $btnShadow = '';
    public $btnTextShadow = '';
    public $btnBorder = '';
    public $btnColorHover = "";
    public $btnBackgroundHover = '';

    //side navigation arrows
    public $sideBtnColor = '#FFF';
    public $sideBtnBackground = '#00000033';
    public $sideBtnSize = 30;
    public $sideBtnRadius = 0;
    public $sideBtnMargin = 0;
    public $sideBtnPaddingV = 5;
    public $sideBtnPaddingH = 0;
    public $sideBtnShadow = '';
    public $sideBtnTextShadow = '';
    public $sideBtnBorder = '';
    public $sideBtnColorHover = "#FFF";
    public $sideBtnBackgroundHover = '#00000066';


    // menu buttons on transparent menu
    public $floatingBtnColor = "#EEE";
    public $floatingBtnColorHover = "";
    public $floatingBtnBackground = "#00000044";
    public $floatingBtnBackgroundHover = '';
    public $floatingBtnSize = null;
    public $floatingBtnRadius = null;
    public $floatingBtnMargin = null;
    public $floatingBtnPadding = null;
    public $floatingBtnShadow = '';
    public $floatingBtnTextShadow = '';
    public $floatingBtnBorder = '';

    public $btnOrder = [
        'currentPage',
        'btnFirst',
        'btnPrev',
        'btnNext',
        'btnLast',
        'btnZoomIn',
        'btnZoomOut',
        'btnRotateLeft',
        'btnRotateRight',
        'btnAutoplay',
        'btnSearch',
        'btnSelect',
        'btnBookmark',
        'btnNotes',
        'btnToc',
        'btnThumbs',
        'btnShare',
        'btnPrint',
        'btnDownloadPages',
        'btnDownloadPdf',
        'btnSound',
        'btnExpand',
        'btnClose'
    ];

    public $currentPage = [
        'enabled' => true,
        'title' => "Current page",
        'vAlign' => 'top',
        'hAlign' => 'left',
        'marginH' => 0,
        'marginV' => 0,
        'color' => '',
        'background' => ''
    ];

    public $btnFirst = [
        'enabled' => false,
        'title' => "First page",
        'iconFA' => "flipbook-icon-angle-double-left",
        'iconM' => "flipbook-icon-first_page"
    ];

    public $btnPrev = [
        'enabled' => true,
        'title' => "Previous page",
        'iconFA' => "flipbook-icon-angle-left",
        'iconM' => "flipbook-icon-keyboard_arrow_left"
    ];

    public $btnNext = [
        'enabled' => true,
        'title' => "Next page",
        'iconFA' => "flipbook-icon-angle-right",
        'iconM' => "flipbook-icon-keyboard_arrow_right"
    ];

    public $btnLast = [
        'enabled' => false,
        'title' => "Last page",
        'iconFA' => "flipbook-icon-angle-double-right",
        'iconM' => "flipbook-icon-last_page"
    ];

    public $btnZoomIn = [
        'enabled' => true,
        'title' => "Zoom in",
        'iconFA' => "flipbook-icon-plus",
        'iconM' => "flipbook-icon-add"
    ];

    public $btnZoomOut = [
        'enabled' => true,
        'title' => "Zoom out",
        'iconFA' => "flipbook-icon-minus",
        'iconM' => "flipbook-icon-remove1"
   ];

    public $btnRotateLeft = [
        'enabled' => false,
        'title' => "Rotate left",
        'iconFA' => "flipbook-icon--undo"
    ];

    public $btnRotateRight = [
        'enabled' => false,
        'title' => "Rotate right",
        'iconFA' => "flipbook-icon--redo"
    ];

    public $btnAutoplay = [
        'enabled' => true,
        'title' => "Autoplay",
        'iconFA' => "flipbook-icon-play",
        'iconM' => "flipbook-icon-play_arrow",
        'iconFA_alt' => "flipbook-icon-pause",
        'iconM_alt' => "flipbook-icon-pause1",
    ];

    public $btnSearch = [
        'enabled' => false,
        'title' => "Search",
        'iconFA' => "flipbook-icon-search",
        'iconM' => "flipbook-icon-search1"
    ];

    public $btnSelect = [
        'enabled' => true,
        'title' => "Select tool",
        'iconFA' => "flipbook-icon-i-cursor",
        'iconM' => "flipbook-icon-text_format"
    ];

    public $btnBookmark = [
        'enabled' => true,
        'title' => "Bookmark",
        'iconFA' => "flipbook-icon-bookmark",
        'iconM' => "flipbook-icon-bookmark1"
    ];

    public $btnNotes = [
        'enabled' => false,
        'title' => "Notes",
        'iconFA' => "flipbook-icon-comment",
        'iconM' => "flipbook-icon-chat_bubble"
    ];

    public $btnToc = [
        'enabled' => true,
        'title' => "Table of Contents",
        'iconFA' => "flipbook-icon-list-ol",
        'iconM' => "flipbook-icon-toc"
    ];

    public $btnThumbs = [
        'enabled' => true,
        'title' => "Pages",
        'iconFA' => "flipbook-icon-th-large",
        'iconM' => "flipbook-icon-view_module"
    ];

    public $btnShare = [
        'enabled' => true,
        'title' => "Share",
        'iconFA' => "flipbook-icon-share-alt",
        'iconM' => "flipbook-icon-share1",
        'hideOnMobile' => true
    ];

    public $btnPrint = [
        'enabled' => true,
        'title' => "Print",
        'iconFA' => "flipbook-icon-print",
        'iconM' => "flipbook-icon-local_printshop",
        'hideOnMobile' => true
    ];

    public $btnDownloadPages = [
        'enabled' => true,
        'title' => "Download pages",
        'iconFA' => "flipbook-icon-download",
        'iconM' => "flipbook-icon-file_download",
        'url' => "images/pages.zip",
        'name' => "allPages.zip"
    ];

    public $btnDownloadPdf = [
        'forceDownload' => false,
        'enabled' => true,
        'title' => "Download PDF",
        'iconFA' => "flipbook-icon-file",
        'iconM' => "flipbook-icon-picture_as_pdf",
        'url' => null,
        'openInNewWindow' => true,
        'name' => "allPages.pdf"
    ];

    public $btnSound = [
        'enabled' => true,
        'title' => "Volume",
        'iconFA' => "flipbook-icon-volume-up",
        'iconFA_alt' => "flipbook-icon-volume-off",
        'iconM' => "flipbook-icon-volume_up",
        'iconM_alt' => "flipbook-icon-volume_mute",
        'hideOnMobile' => true
    ];

    public $btnExpand = [
        'enabled' => true,
        'title' => "Toggle fullscreen",
        'iconFA' => "flipbook-icon-expand",
        'iconM' => "flipbook-icon-fullscreen",
        'iconFA_alt' => "flipbook-icon-compress",
        'iconM_alt' => "flipbook-icon-fullscreen_exit"
    ];

    public $btnClose = [
        'title' => "Close",
        'iconFA' => "flipbook-icon-times",
        'iconM' => "flipbook-icon-clear",
        'hAlign' => 'right',
        'vAlign' => 'top',
        'size' => 20
    ];

    public $btnShareIfMobile = false;
    public $btnSoundIfMobile = false;
    public $btnPrintIfMobile = false;

    public $sideNavigationButtons = true;

    public $hideMenu = false;

    //share
    public $shareUrl = null;
    public $shareTitle = null;
    public $shareImage = null;

    public $whatsapp = [
        'enabled' => true,
        'icon' => 'flipbook-icon-whatsapp'
    ];

    public $twitter = [
        'enabled' => true,
        'icon' => 'flipbook-icon-twitter'
    ];

    public $facebook = [
        'enabled' => true,
        'icon' => 'flipbook-icon-facebook'
    ];

    public $pinterest = [
        'enabled' => true,
        'icon' => 'flipbook-icon-pinterest-p'
    ];

    public $email = [
        'enabled' => true,
        'icon' => 'flipbook-icon-envelope'
    ];

    public $linkedin = [
        'enabled' => true,
        'icon' => 'flipbook-icon-linkedin'
    ];

    public $digg = [
        'enabled' => false,
        'icon' => 'flipbook-icon-digg'
    ];

    public $reddit = [
        'enabled' => false,
        'icon' => 'flipbook-icon-reddit-alien'
    ];

    public $pdf = [
        'annotationLayer' => false,
    ];

    public $pageTextureSize = 2048;
    public $pageTextureSizeSmall = 1500;
    public $thumbTextureSize = 300;

    public $pageTextureSizeMobile = 1500;
    public $pageTextureSizeMobileSmall = 1024;

    //flip animation type; can be "2d"; "3d" ; "webgl"; "swipe"
    public $viewMode = 'webgl';
    public $singlePageMode = false;
    public $singlePageModeIfMobile = false;
    public $zoomMin = .95;
    public $zoomMax2 = null;

    public $zoomSize = null;
    public $zoomStep = 2;
    public $zoomTime = 300;
    public $zoomReset = false;
    public $zoomResetTime = 300;

    public $wheelDisabledNotFullscreen = false;
    public $arrowsDisabledNotFullscreen = false;
    public $arrowsAlwaysEnabledForNavigation = true;
    public $touchSwipeEnabled = true;

    public $responsiveView = true;
    public $responsiveViewRatio = 1; // use responsive view only in portrait mode
    public $responsiveViewTreshold = 768;
    public $minPixelRatio = 1; //between 1 and 2; 1.5 = best ratio performance FPS / image quality

    public $pageFlipDuration = 1;

    public $contentOnStart = false;
    public $thumbnailsOnStart = false;
    public $searchOnStart = false;

    public $sideMenuOverBook = true;
    public $sideMenuOverMenu = false;
    public $sideMenuOverMenu2 = true;
    public $sideMenuPosition = 'left';

    //lightbox settings

    public $lightBox = false;
    public $lightBoxOpened = false;
    public $lightBoxFullscreen = false;
    public $lightboxCloseOnClick = false;
    public $lightboxResetOnOpen = true;
    public $lightboxBackground = null; 
    public $lightboxBackgroundColor = null;
    public $lightboxBackgroundPattern = null;
    public $lightboxBackgroundImage = null;
    public $lightboxStartPage = null;
    public $lightboxMarginV = '0';
    public $lightboxMarginH = '0';
    public $lightboxCSS = '';
    public $lightboxPreload = false;
    public $lightboxShowMenu = false;  // show menu while book is loading so lightbox can be closed
    public $lightboxCloseOnBack = true; 

    // WebGL settings

    public $disableImageResize = true;  //disable image resize to power of 2 (needed for anisotropic filtering)

    public $pan = 0; 
    public $panMax = 10; 
    public $panMax2 = 2; 
    public $panMin = -10; 
    public $panMin2 = -2; 
    public $tilt = 0; 
    public $tiltMax = 0; 
    public $tiltMax2 = 0; 
    public $tiltMin = -20; 
    public $tiltMin2 = -5; 

    public $rotateCameraOnMouseMove = false; 
    public $rotateCameraOnMouseDrag = true; 

    public $lights = true; 
    public $lightColor = 0xFFFFFF; 
    public $lightPositionX = 0; 
    public $lightPositionZ = 1400; 
    public $lightPositionY = 350; 
    public $lightIntensity = .6; 

    public $shadows = true; 
    public $shadowMapSize = 1024; 
    public $shadowOpacity = .2; 
    public $shadowDistance = 0; 

    public $pageRoughness = 1; 
    public $pageMetalness = 0; 

    public $pageHardness = 2; 
    public $coverHardness = 2; 
    public $pageSegmentsW = 10; 
    public $pageSegmentsH = 1; 

    public $pageMiddleShadowSize = 2; 
    public $pageMiddleShadowColorL = "#999999"; 
    public $pageMiddleShadowColorR = "#777777"; 

    public $antialias = false; 

    // preloader

    public $preloaderText = ''; 

    public $fillPreloader = [
        'enabled' => false,
        'imgEmpty' => "images/logo_light.png",
        'imgFull' => "images/logo_dark.png",
    ];

    // logo

    public $logoImg = '';  //url of logo image
    public $logoUrl = '';  // url target 
    public $logoCSS = 'position:absolute;'; 
    public $logoHideOnMobile = false; 

    public $printMenu = true; 
    public $downloadMenu = true; 

    public $cover = true; 
    public $backCover = true; 

    public $pdfTextLayer = true; 
    public $annotationLayer = true; 

    public $googleAnalyticsTrackingCode = null; 

    public $minimumAndroidVersion = 6; 

    public $linkColor = 'rgba(0, 0, 0, 0)'; 
    public $linkColorHover = 'rgba(255, 255, 0, 1)'; 
    public $linkOpacity = 0.4; 
    public $linkTarget = '_blank';  // _blank - new window;  _self - same window


    public $rightClickEnabled = true; 

    public $pageNumberOffset = 0;  // to start book page count at different page;  example Cover;  1;  2;  ... -> pageNumberOffset = 1

    public $flipSound = true; 
    public $backgroundMusic = false; 
    public $doubleClickZoomDisabled = false; 
    public $pageDragDisabled = false; 
    public $pageClickAreaWdith = '10%';  // width of the page that behaves like next / previous page button

    public $noteTypes = [
        [
            'id' => 1,
            'title' => "User",
            'color' => "green",
            'enabled' => true
        ],
        [
            'id' => 2,
            'title' => "Group",
            'color' => "yellow",
            'enabled' => true
        ],
        [
            id => 3,
            'title' => "Admin",
            'color' => "blue",
            'enabled' => true
        ],
    ]; 

    public $pageRangeStart = null; 
    public $pageRangeEnd = null; 

    public $strings = [
        'print' => "Print",
        'printLeftPage' => "Print left page",
        'printRightPage' => "Print right page",
        'printCurrentPage' => "Print current page",
        'printAllPages' => "Print all pages",

        'download' => "Download",
        'downloadLeftPage' => "Download left page",
        'downloadRightPage' => "Download right page",
        'downloadCurrentPage' => "Download current page",
        'downloadAllPages' => "Download all pages",

        'bookmarks' => "Bookmarks",
        'bookmarkLeftPage' => "Bookmark left page",
        'bookmarkRightPage' => "Bookmark right page",
        'bookmarkCurrentPage' => "Bookmark current page",

        'search' => "Search",
        'findInDocument' => "Find in document",
        'pagesFoundContaining' => "pages found containing",
        'noMatches' => "No matches",
        'matchesFound' => 'matches found',
        'page' => 'Page',
        'matches' => 'matches',

        'thumbnails' => "Thumbnails",
        'tableOfContent' => "Table of Contents",
        'share' => "Share",
        'notes' => "Notes",

        'pressEscToClose' => "Press ESC to close",

        'password' => "Password",
        'addNote' => "Add note",
        'typeInYourNote' => "Type in your note..."


    ];

    //mobile devices settings - override any setting for mobile devices
    public $mobile = [

        'shadows' => false,
        'pageSegmentsW' => 5

    ];
    
    public $extraVars = [];
    
    public $declared = [];
    
    public $target;

	public function __construct($params, $target, $extraVars = []) {
        $this->target = $target;
        $this->extraVars = $extraVars;
        foreach($params as $key => $value) {
            if (property_exists($this, $key)) {
                $this->declared[] = $key;
                $this->{$key} = $value;
            }
        }
	}
    
    public function buildViewerScript() {
        
        $html = '<script type="text/javascript">'. PHP_EOL;
        if(count($this->extraVars)) {
            foreach($this->extraVars as $key => $value) {
                $html .= 'var '.$key.' = "'.$value.'"'. PHP_EOL;
            }
        }
        $html .= '$(document).ready(function () {'. PHP_EOL;
        $html .= '$("#'.$this->target.'").flipBook({'. PHP_EOL;
        foreach($this->declared[] as $key) {
            if (property_exists($this, $key)) {
                if (is_array($this->{$key})) {
				    $html .= $this->deployArrayScript($key, $this->{$key}). PHP_EOL;
				} else {
				    $html .= $key . ': ' . $this->{$key} . ',' . PHP_EOL;
				}

                
            }
            
        }
        $html .= '});'. PHP_EOL;
        $html .= '})'. PHP_EOL;
        $html .= '</script>'. PHP_EOL;
        
        return $html;
        
    }
    
    
	public function deployArrayScript($option, $value, $sub = false) {

		if ($sub) {

			if (is_string($option) && is_array($value) && !Tools::is_assoc($value)) {
				$jsScript = $option . ': [' . PHP_EOL;

				foreach ($value as $suboption => $value) {

					if (is_array($value)) {
						$jsScript .= '          ' . $this->deployArrayScript($suboption, $value, true);
					} else

					if (is_string($suboption)) {
						$jsScript .= '          ' . $suboption . ': ' . $value . ',' . PHP_EOL;
					} else {
						$jsScript .= '          ' . $value . ',' . PHP_EOL;
					}

				}

				$jsScript .= '          ],' . PHP_EOL;
				return $jsScript;

			} else {

				if (is_string($option)) {
					$jsScript = $option . ': {' . PHP_EOL;
				} else {
					$jsScript = ' {' . PHP_EOL;
				}

			}

		} else {

			if (is_string($option)) {
				$jsScript = $option . ': {' . PHP_EOL;
			} else {
				$jsScript = ' {' . PHP_EOL;
			}

		}

		foreach ($value as $suboption => $value) {

			if (is_array($value)) {
				$jsScript .= '          ' . $this->deployArrayScript($suboption, $value, true);
			} else

			if (is_string($suboption)) {
				$jsScript .= '          ' . $suboption . ': ' . $value . ',' . PHP_EOL;
			} else {
				$jsScript .= '          ' . $value . ',' . PHP_EOL;
			}

		}

		if ($sub) {
			$jsScript .= '          },' . PHP_EOL;
		} else {
			$jsScript .= '      },' . PHP_EOL;
		}

		return $jsScript;

	}

	protected function l($string, $class = 'PdfViewer', $addslashes = false, $htmlentities = true) {

		// if the class is extended by a plugin, use plugins/[plugin_name]/xx.php lang file
		$currentClass = get_class($this);

		if (Plugin::getPluginNameFromClass($currentClass)) {
			$string = str_replace('\'', '\\\'', $string);

			return Context::getContext()->translations->getPluginTranslation(Plugin::$classInPlugin[$currentClass], $string, $currentClass);
		}

		global $_LANGADM;

		if ($class == __CLASS__) {
			$class = 'PdfViewer';
		}

		$key = md5(str_replace('\'', '\\\'', $string));
		$str = (array_key_exists(get_class($this) . $key, $_LANGADM)) ? $_LANGADM[get_class($this) . $key] : ((array_key_exists($class . $key, $_LANGADM)) ? $_LANGADM[$class . $key] : $string);
		$str = $htmlentities ? htmlentities($str, ENT_QUOTES, 'utf-8') : $str;

		return str_replace('"', '&quot;', ($addslashes ? addslashes($str) : stripslashes($str)));
	}


}
