<?php
/**
 * HTML helper tools
 */
class htmlHelper {
    /**
     * Internal storage of the link-prefix and hypertext protocol values
     * @var string
     */
    protected $_linkPrefix, $_protocol;

    /**
     * Internal list of included CSS & JS files used by $this->_tagBuilder() to assure that files are not included twice
     * @var array
     */
    protected $_includedFiles = array();

    /**
     * Flag array to avoid defining singleton JavaScript & CSS snippets more than once
     * @var array
     */
    protected $_jsSingleton = array(), $_cssSingleton = array();

    /**
     * Data to load at the end of the output, just before </body></html>
     * @var array
     */
    public $eof = array();

    /**
     * Sets the protocol (http/https)
     */
    public function __construct() {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            if (isset($_SERVER['HTTP_HOST'])) {
                $this->_linkPrefix = 'http://' . $_SERVER['HTTP_HOST'];
                if (strpos($_SERVER['HTTP_HOST'], -1) != '/') {
                    $this->_linkPrefix .= '/';
                };
            } else {
                $this->autoSsl = false; //can't change protocols without knowing hostname
            }
            $this->_protocol = 'https://';
        } else {
            $this->_protocol = 'http://';
        }
    }

    /**
     * Creates simple HTML wrappers, accessed via $this->__call()
     *
     * JS and CSS files are never included more than once even if requested twice. If DEBUG mode is enabled than the
     * second request will be added to the debug log as a duplicate. The jsSingleton and cssSingleton methods operate
     * the same as the js & css methods except that they will silently skip duplicate requests instead of logging them.
     *
     * jsInlineSingleton and cssInlineSingleton makes sure a JavaScript or CSS snippet will only be output once, even
     * if echoed out multiple times. Eg.:
     * $helloJs = "function helloWorld() {alert('Hello World');}";
     * echo $html->jsInlineSingleton($helloJs);
     *
     * Adding an optional extra argument to jsInlineSingleton/cssInlineSingleton will return the inline code bare (plus
     * a trailing linebreak), this is used for joint JS/CSS statements:
     * echo $html->jsInline($html->jsInlineSingleton($helloJs, true) . 'helloWorld();');
     *
     * @param string $tagType
     * @param array $args
     * @return string
     */
    protected function _tagBuilder($tagType, $args = array()) {
        $arg = current($args);
        if (DEBUG_MODE && ($arg === '' || (is_array($arg) && empty($arg)))) {
            $errorMsg = 'Missing argument for ' . __CLASS__ . '::' . $tagType . '()';
            debug::log($errorMsg, 'warn');
        }

        if (is_array($arg)) {
            $baseArray = $args; //maintain potential-existence of $args[1]...[n]
            foreach ($arg as $thisArg) {
                $baseArray[0] = $thisArg;
                $return[] = $this->_tagBuilder($tagType, $baseArray);
            }
            $return = implode(PHP_EOL, $return);
        } else {
            switch ($tagType) {
                case 'js': //Optional extra argument to delay output until the end of the file
                case 'jsSingleton':
                case 'css': //Optional extra argument to define CSS media type
                case 'cssSingleton':
                case 'jqueryTheme':
                    if ($tagType == 'jqueryTheme') {
                        $arg = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/'
                             . str_replace(' ', '-', strtolower($arg)) . '/jquery-ui.css';
                        $tagType = 'css';
                    }
                    if (!isset($this->_includedFiles[$tagType][$arg])) {
                        if ($tagType == 'css' || $tagType == 'cssSingleton') {
                            $return = '<link rel="stylesheet" type="text/css" href="' . $arg . '"'
                                    . ' media="' . (isset($args[1]) ? $args[1] : 'all') . '" />';
                        } else {
                            $return = '<script src="' . $arg . '"></script>';
                            if (isset($args[1])) {
                                $this->eof[] = $return;
                                $return = null;
                            }
                        }
                        $this->_includedFiles[$tagType][$arg] = true;
                    } else {
                        $return = null;
                        if (DEBUG_MODE && ($tagType == 'js' || $tagType == 'css')) {
                            debug::log($arg . $tagType . ' file has already been included', 'warn');
                        }
                    }
                    break;
                case 'cssInline': //Optional extra argument to define CSS media type
                    $return = '<style type="text/css" media="' . (isset($args[1]) ? $args[1] : 'all') . '">'
                            . PHP_EOL . '/*<![CDATA[*/'
                            . PHP_EOL . '<!--'
                            . PHP_EOL . $arg
                            . PHP_EOL . '//-->'
                            . PHP_EOL . '/*]]>*/'
                            . PHP_EOL . '</style>';
                    break;
                case 'jsInline': //Optional extra argument to delay output until the end of the file
                    $return = '<script>'
                            . PHP_EOL . '//<![CDATA['
                            . PHP_EOL . '<!--'
                            . PHP_EOL . $arg
                            . PHP_EOL . '//-->'
                            . PHP_EOL . '//]]>'
                            . PHP_EOL . '</script>';
                    if (isset($args[1])) {
                        $this->eof[] = $return;
                        $return = null;
                    }
                    break;
                case 'jsInlineSingleton': //Optional extra argument to supress adding of inline JS/CSS wrapper
                case 'cssInlineSingleton':
                    $tagTypeBase = substr($tagType, 0, -15);
                    $return = null;
                    $md5 = md5($arg);
                    if (!isset($this->{'_' . $tagTypeBase . 'Singleton'}[$md5])) {
                        $this->{'_' . $tagTypeBase . 'Singleton'}[$md5] = true;
                        $return = (!isset($args[1]) || !$args[1] ? $this->{$tagTypeBase . 'Inline'}($arg)
                                                                 : $arg . PHP_EOL);
                    }
                    break;
                case 'div':
                case 'li':
                case 'p':
                case 'h1':
                case 'h2':
                case 'h3':
                case 'h4':
                case 'ul':
                case 'ol':
                    $return = '<' . $tagType;
                    if (isset($args[1]) && is_array($args[1]) && $args[1]) {
                        $return .= ' ' . self::formatProperties($args[1]);
                    }
                    $return .= '>' . $arg . '</' . $tagType . '>' . PHP_EOL;
                    break;
                default:
                    $errorMsg = 'TagType ' . $tagType . ' not valid in ' . __CLASS__ . '::' . __METHOD__;
                    throw new Exception($errorMsg);
                    break;
            }
        }
        return $return;
    }

    /**
     * Creates virtual wrapper methods via $this->_tagBuilder() for the simple wrapper functions including:
     * $html->css, js, cssInline, jsInline, div, li, p and h1-h4
     *
     * @param string $method
     * @param array $arg
     * @return string
     */
    public function __call($method, $args) {
        $validTags = array('css', 'js', 'cssSingleton', 'jsSingleton', 'jqueryTheme',
                           'cssInline', 'jsInline', 'jsInlineSingleton', 'cssInlineSingleton',
                           'div', 'li', 'p', 'h1', 'h2', 'h3', 'h4', 'ul', 'ol');
        if (in_array($method, $validTags)) {
            return $this->_tagBuilder($method, $args);
        } else {
            $errorMsg = 'Call to undefined method ' . __CLASS__ . '::' . $method . '()';
            trigger_error($errorMsg, E_USER_ERROR);
        }
    }

    /**
     * Flag to make sure that header() can only be opened one-at-a-time and footer() can only be used after header()
     * @var boolean
     */
    private $_bodyOpen = false;

    /**
     * Silently updates common XHTML 1.1 invalidations with the proper XHTML 1.1 markup
     * @var Boolean Default true
     */
    public $xhtmlMode = true;

    /**
     * Internal cache for the doctype data
     * @var string
     */
    protected $_docTypeDeclaration, $_isHtml5 = true;

    /**
     * Enables modification of the docType
     *
     * Can either set to an actual doctype definition or to one of the presets (case-insensitive):
     *
     * HTML 5 (this is the default DTD, there is no need to explicitely call setDocType() for HTML 5)
     * XHTML 1.1
     * XHTML (Alias for XHTML 1.1)
     * XHTML 1.0 Strict
     * XHTML 1.0 Transitional
     * XHTML 1.0 Frameset
     * XHTML 1.0 (Alias for XHTML 1.0 Strict)
     * HTML 4.01
     * HTML (Alias for HTML 4.01)
     * XHTML Mobile 1.2
     * XHTML Mobile 1.1
     * XHTML Mobile 1.0
     * Mobile 1.2 (alias for XHTML Mobile 1.2)
     * Mobile 1.1 (alias for XHTML Mobile 1.1)
     * Mobile 1.0 (alias for XHTML Mobile 1.0)
     * Mobile (alias for the most-strict Mobile DTD, currently 1.2)
     *
     * @param string $docType
     */
    public function setDocType($docType) {
        $docType = str_replace(' ', '', strtolower($docType));
        if ($docType == 'xhtml1.0') {
            $docType = 'strict';
        }
        $docType = str_replace(array('xhtmlmobile', 'xhtml1.0'), array('mobile', ''), $docType);
        $this->_isHtml5 = ($docType == 'html5');
        $docTypes = array(
            'html5'        => '<!DOCTYPE html>',
            'xhtml1.1'     => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" '
                           .  '"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
            'strict'       => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" '
                           .  '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
            'transitional' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" '
                           .  '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
            'frameset'     => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" '
                           .  '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
            'html4.01'     => '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" '
                           .  '"http://www.w3.org/TR/html4/strict.dtd">',
            'mobile1.2'    => '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" '
                            . '"http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">',
            'mobile1.1'    => '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.1//EN '
                            . '"http://www.openmobilealliance.org/tech/DTD/xhtml-mobile11.dtd">',
            'mobile1.0'    => '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" '
                            . '"http://www.wapforum.org/DTD/xhtml-mobile10.dtd">'
        );
        //create greatest-version aliases for mobile, xhtml and html
        $docTypes['mobile'] = $docTypes['mobile1.2'];
        $docTypes['xhtml'] = $docTypes['xhtml1.1'];
        $docTypes['html'] = $docTypes['html4.01'];
        $this->_docTypeDeclaration = (isset($docTypes[$docType]) ? $docTypes[$docType] : $docType);
    }

    /**
     * Array used internally by Vork to cache JavaScript and CSS snippets and place them in the head section
     * Changing the contents of this property may cause Vork components to be rendered incorrectly.
     * @var array
     */
    public $vorkHead = array();

    /**
     * Adds a JavaScript or CSS snippet to the head of the document if it has not yet been rendered
     *
     * @param string $container only valid options are: jsInline, cssInline
     * @param string $obj
     * @return string Returns an empty string if snippet is added to head, returns snippet if head is already rendered
     */
    public function addSnippetToHead($container, $obj) {
        $this->vorkHead[$container][] = $obj;
        return (!$this->_bodyOpen ? '' : $obj);
    }

    /**
     * Returns an HTML header and opens the body container
     * This method will trigger an error if executed more than once without first calling
     * the footer() method on the prior usage
     * This is meant to be utilized within layouts, not views (but will work in either)
     *
     * @param array $args Options: (array) metaheader, (array) meta, favicon, animatedFavicon, (string/array) icon,
     *                             head, showBrowserBars
     * @return string
     */
    public function header(array $args) {
        if (!$this->_bodyOpen) {
            $this->_bodyOpen = true;
            extract($args);
            if (!$this->_docTypeDeclaration) {
                $this->setDocType('html5');
            }
            $return = $this->_docTypeDeclaration
                    . PHP_EOL . '<html xmlns="http://www.w3.org/1999/xhtml"'
                    . ' lang="' . (!empty($lang) ? $lang : 'en') . '">'
                    . PHP_EOL . '<head>'
                    . PHP_EOL . '<title>' . $title . '</title>';

            if (!isset($metaheader['Content-Type'])) {
                $metaheader['Content-Type'] = ($this->_isHtml5 ? 'text/html' : 'application/xhtml+xml')
                                            . '; charset=utf-8';
            }
            foreach ($metaheader as $name => $content) {
                $return .= PHP_EOL . '<meta http-equiv="' . $name . '" content="' . $content . '" />';
            }

            if ($this->_isHtml5 && empty($showBrowserBars)) { //makes iOS utilize fullscreen
                $meta['apple-mobile-web-app-capable'] = 'yes';
            }
            $meta['generator'] = 'Vork 3.00';
            foreach ($meta as $name => $content) {
                $return .= PHP_EOL . '<meta name="' . $name . '" content="' . $content . '" />';
            }

            if (isset($favicon)) {
                $return .= PHP_EOL . '<link rel="shortcut icon" href="' . $favicon . '" type="image/x-icon" />';
            }
            if (isset($animatedFavicon)) {
                $return .= PHP_EOL . '<link rel="icon" href="' . $animatedFavicon . '" type="image/gif" />';
            }
            if (isset($icon)) {
                if (!is_array($icon)) {
                    $icon[144] = $icon;
                }
                krsort($icon);
                $largestIcon = current($icon);
                foreach ($icon as $size => $iconImage) {
                    $return .= PHP_EOL . '<link rel="apple-touch-icon-precomposed" href="' . $iconImage
                             . '" sizes="' . $size . 'x' . $size . '">';
                }
                $return .= PHP_EOL . '<link rel="apple-touch-icon-precomposed" href="' . $largestIcon . '">';
            }

            if ($this->vorkHead) { //used internally by Vork tools
                foreach ($this->vorkHead as $container => $objArray) { //works only for inline code, not external files
                    $return .= PHP_EOL . $this->$container(implode(PHP_EOL, $objArray));
                }
            }

            if ($this->_isHtml5 && is::mobile() && empty($showAddressBar)) {
                $hideAddressBarJs = 'window.addEventListener("load", function(e) {
                                        setTimeout(function() {
                                            window.scrollTo(0, 1);
                                        }, 1);
                                    }, false);';
                $return .= PHP_EOL . $this->jsInline($hideAddressBarJs);
            }
            $containers = array('css', 'cssInline', 'js', 'jsInline', 'jqueryTheme');
            foreach ($containers as $container) {
                if (isset($$container)) {
                    $return .= PHP_EOL . $this->$container($$container);
                }
            }

            if (isset($head)) {
                $return .= PHP_EOL . (is_array($head) ? implode(PHP_EOL, $head) : $head);
            }

            $return .= PHP_EOL . '</head>' . PHP_EOL . '<body';
            if (!empty($body)) {
                $return .= ' ' . self::formatProperties($body);
            }
            $return .= '>';
            return $return;
        } else {
            $errorMsg = 'Invalid usage of ' . __METHOD__ . '() - the header has already been returned';
            trigger_error($errorMsg, E_USER_NOTICE);
        }
    }

    /**
     * Returns an HTML footer and optional Google Analytics
     * This method will trigger an error if executed without first calling the header() method
     * This is meant to be utilized within layouts, not views (but will work in either)
     *
     * @param array $args
     * @return string
     */
    public function footer(array $args = array()) {
        if ($this->_bodyOpen) {
            $this->_bodyOpen = false;
            $return = '</body></html>';

            if (isset($args['GoogleAnalytics'])) {
                $return = $this->jsInline('var _gaq = _gaq || []; _gaq.push(["_setAccount", "'
                        . $args['GoogleAnalytics'] . '"]); _gaq.push(["_trackPageview"]); (function() {
    var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
    ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
    (document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(ga);'
                        . '})();') . $return;
            }

            if ($this->eof) {
                if (!is_array($this->eof)) {
                    $this->eof = array($this->eof);
                }
                $return = implode($this->eof) . $return;
            }
            return $return;
        } else {
            $errorMsg = 'Invalid usage of ' . __METHOD__ . '() - header() has not been called';
            trigger_error($errorMsg, E_USER_NOTICE);
        }
    }

    /**
     * Establishes a basic set of JavaScript tools, just echo $html->jsTools() before any JavaScript code that
     * will use the tools.
     *
     * This method will only operate from the first occurrence in your code, subsequent calls will not output anything
     * but you should add it anyway as it will make sure that your code continues to work if you later remove a
     * previous call to jsTools.
     *
     * Tools provided:
     *
     * dom() method is a direct replacement for document.getElementById() that works in all JS-capable
     * browsers Y2k and newer.
     *
     * vork object - defines a global vork storage space; use by appending your own properties, eg.: vork.widgetCount
     *
     * @param Boolean $noJsWrapper set to True if calling from within a $html->jsInline() wrapper
     * @return string
     */
    public function jsTools($noJsWrapper = false) {
        return $this->addSnippetToHead('jsInline', $this->jsInlineSingleton("var vork = function() {}
var dom = function(id) {
    if (typeof document.getElementById != 'undefined') {
        dom = function(id) {return document.getElementById(id);}
    } else if (typeof document.all != 'undefined') {
        dom = function(id) {return document.all[id];}
    } else {
        return false;
    }
    return dom(id);
}", $noJsWrapper));
    }

    /**
     * Load a JavaScript library via Google's AJAX API
     * http://code.google.com/apis/ajaxlibs/documentation/
     *
     * Version is optional and can be exact (1.8.2) or just version-major (1 or 1.8)
     *
     * Usage:
     * echo $html->jsLoad('jquery');
     * echo $html->jsLoad(array('yui', 'mootools'));
     * echo $html->jsLoad(array('yui' => 2.7, 'jquery', 'dojo' => '1.3.1', 'scriptaculous'));
     *
     * //You can also use the Google API format JSON-decoded in which case version is required & name must be lowercase
     * $jsLibs = array(array('name' => 'mootools', 'version' => 1.2, 'base_domain' => 'ditu.google.cn'), array(...));
     * echo $html->jsLoad($jsLibs);
     *
     * @param string $library Currently, only jquery is supported
     * @deprecated: @param mixed $library Can be a string, array(str1, str2...) or , array(name1 => version1, name2 => version2..)
     *                       or JSON-decoded Google API syntax array(array('name' => 'yui', 'version' => 2), array(...))
     * @param mixed $version Optional, int or str, this is only used if $library is a string
     * @param array $options Optional, passed to Google "optionalSettings" argument, only used if $library == str
     * @return str
     */
    public function jsLoad($library, $version = null, array $options = array()) {
        //JSAPI was deprecated & deactivated by Google
        /*
        $versionDefaults = array('swfobject' => 2, 'yui' => 2, 'ext-core' => 3, 'mootools' => 1.2);
        if (!is_array($library)) { //jsLoad('yui')
            $library = strtolower($library);
            if (!$version) {
                $version = (!isset($versionDefaults[$library]) ? 1 : $versionDefaults[$library]);
            }
            $library = array('name' => $library, 'version' => $version);
            $library = array(!$options ? $library : array_merge($library, $options));
        } else {
            foreach ($library as $key => $val) {
                if (!is_array($val)) {
                    if (is_int($key)) { //jsLoad(array('yui', 'prototype'))
                        $val = strtolower($val);
                        $version = (!isset($versionDefaults[$val]) ? 1 : $versionDefaults[$val]);
                        $library[$key] = array('name' => $val, 'version' => $version);
                    } else if (!is_array($val)) { // //jsLoad(array('yui' => '2.8.0r4', 'prototype' => 1.6))
                        $library[$key] = array('name' => strtolower($key), 'version' => $val);
                    }
                }
            }
        }

        $url = $this->_protocol . 'www.google.com/jsapi';
        if (!isset($this->_includedFiles['js'][$url])) { //autoload library
            $this->_includedFiles['js'][$url] = true;
            $url .= '?autoload=' . urlencode(json_encode(array('modules' => array_values($library))));
            $return = $this->js($url);
        } else { //load inline
            foreach ($library as $lib) {
                $js = 'google.load("' . $lib['name'] . '", "' . $lib['version'] . '"';
                if (count($lib) > 2) {
                    unset($lib['name'], $lib['version']);
                    $js .= ', ' . json_encode($lib);
                }
                $jsLoads[] = $js . ');';
            }
            $return = $this->jsInline(implode(PHP_EOL, $jsLoads));
        }
        */

        $version = '1.7.1'; //version settings are ignored for now
        $urls = array( //only jqeuery functionality is retained for now
            'jquery' => 'https://ajax.googleapis.com/ajax/libs/jquery/' . $version . '/jquery.min.js'
        );
        if (isset($urls[$library])) {
            return $this->js($urls[$library]);
        }
    }

    /**
     * Takes an array of key-value pairs and formats them in the syntax of HTML-container properties
     *
     * @param array $properties
     * @return string
     */
    public static function formatProperties(array $properties) {
        $return = array();
        foreach ($properties as $name => $value) {
            $return[] = $name . '="' . get::htmlentities($value) . '"';
        }
        return implode(' ', $return);
    }

    public static function universalProperties(array $properties) {
        $propertyKeys = array('id', 'class', 'style');
        foreach ($propertyKeys as $property) {
            if (isset($properties[$property])) {
                $args[$property] = $properties[$property];
            }
        }
        return (!empty($args) ? self::formatProperties($args) : '');
    }

    /**
     * Creates an anchor or link container
     *
     * @param array $args
     * @return string
     */
    public function anchor(array $args) {
        if (!isset($args['text']) && isset($args['href'])) {
            $args['text'] = $args['href'];
        }
        if (!isset($args['title']) && isset($args['text'])) {
            $args['title'] = $args['text'];
        }
        if (isset($args['title'])) {
            $args['title'] = str_replace(array("\n", "\r"), ' ', strip_tags($args['title']));
        }
        $return = '';
        if (isset($args['ajax'])) {
            $return = $this->jsSingleton('/js/ajax.js');
            $onclick = "return ajax.load('" . $args['ajax'] . "', this.href);";
            $args['onclick'] = (!isset($args['onclick']) ? $onclick : $args['onclick'] . '; ' . $onclick);
            unset($args['ajax']);
        }
        if (isset($args['target'])) {
            if ($args['target'] == '_auto') {
                if (isset($args['href']) && strpos($args['href'], '://')
                    && !preg_match('#^..?tps?\:\/\/.*' . get::$config->SITE_DOMAIN . '(\/.*)?$#i', $args['href'])) {
                    $args['target'] = '_blank';
                }
            }
            if ($args['target'] == '_blank' && $this->xhtmlMode) {
                $onclick = 'window.open(this.href); return false;';
                $args['onclick'] = (!isset($args['onclick']) ? $onclick : $args['onclick'] . '; ' . $onclick);
            }
            if (isset($onclick) || $args['target'] == '_auto') {
                unset($args['target']);
            }
        }
        $text = (isset($args['text']) ? $args['text'] : null);
        unset($args['text']);
        return $return . '<a ' . self::formatProperties($args) . '>' . $text . '</a>';
    }

    /**
     * Sets if links should attempt to automatically determine when to go to unsecure-HTTP and when to stay in SSL
     * @var boolean
     */
    public $autoSsl = false;

    /**
     * Shortcut to access the anchor method
     *
     * @param str $href
     * @param str $text
     * @param array $args
     * @return str
     */
    public function link($href, $text = null, array $args = array()) {
        if (strpos($href, 'http') !== 0 && strpos($href, 'javascript:') !== 0 && $this->autoSsl) {
            $href = $this->_linkPrefix . $href;
        }
        $args['href'] = $href;
        if ($text !== null) {
            $args['text'] = $text;
        }
        return $this->anchor($args);
    }

    /**
     * Returns an image in accessible-XHTML syntax
     *
     * @param array $args
     * @return string
     */
    public function img(array $args) {
        $args['alt'] = (isset($args['alt']) ? str_replace(array("\n", "\r"), ' ', strip_tags($args['alt'])) : '');
        return '<img ' . self::formatProperties($args) . ' />';
    }

    /**
     * Convenience function to simplify access the img() helper method
     *
     * @param string $src
     * @param int $width
     * @param int $height
     * @param string $alt
     * @param array $args
     * @return string
     */
    public function image($src, $width = null, $height = null, $alt = '', array $args = array()) {
        $args['src'] = $src;
        $options = array('width', 'height', 'alt');
        foreach ($options as $option) {
            if ($$option) {
                $args[$option] = $$option;
            }
        }
        return $this->img($args);
    }

    /**
     * Adds PHP syntax highlighting - if no PHP-open <? tag is found the entire string gets treated as PHP
     *
     * @param string $str
     * @return string
     */
    public function phpcode($str) {
        if (strpos($str, '<?') === false) {
            $return = substr(strstr(highlight_string("<?php\n" . $str, true), '<br />'), 6);
            if (substr($return, 0, 7) == '</span>') {
                $return = '<code><span style="color: #000000">' . substr($return, 7);
            } else {
                $return = '<code><span style="color: #0000BB">' . $return;
            }
        } else {
            $return = highlight_string($str, true);
        }
        return $return;
    }

    /**
     * Wrapper display computer-code samples
     *
     * @param str $str
     * @return str
     */
    public function code($str) {
        return '<code>' . str_replace('  ', '&nbsp;&nbsp;', nl2br(get::htmlentities($str))) . '</code>';
    }

    /**
     * Creates a list from an array with automatic nesting and linking.
     * If the keys are URLs then the elements will be linked; be sure that elements to remain unlinked have numeric keys
     * Note: the word "list" is a reserved word in PHP, so thus the name "linkList"
     *
     * @param array $links
     * @param string $listType Optional, if ommitted then an unordered (ul) list will be returned, if an empty string or
     *                         Bool-false is used then no list-wrapper is used (do not mix this with nested lists)
     * @return string
     */
    public function linkList(array $links, $listType = 'ul', $linkArgs = array()) {
        $return = ($listType ? '<' . $listType . '>' : '');
        foreach ($links as $url => $title) {
            $class = array('class' => ($this->alternator() ? 'odd' : 'even'));
            $return .= $this->li(!is_int($url) ? $this->link($url, $title, $linkArgs) :
                                                 (is_array($title) ? $this->linkList($title, $listType) : $title), $class);
        }
        $return .= ($listType ? '</' . $listType . '>' : '');
        return $return;
    }

    /**
     * Display a definition list
     *
     * @param array $definitions Array of key-val definitions, both val can be a string or an array of descriptions
     *                           if a "dt" key exists within the array-val then it will be used as the DT value instead
     *                           of the array-key. The DT value can be either a string or an array of strings.
     * @return string
     */
    public function dl(array $definitions) {
        foreach ($definitions as $term => $desc) {
            if (is_array($desc) && isset($desc['dt'])) {
                $term = (is_array($desc['dt']) ? implode('</dt>' . PHP_EOL . '<dt>', $desc['dt']) : $desc['dt']);
                unset($desc['dt']);
            }
            $return[] = '<dl class="' . ($this->alternator() ? 'odd' : 'even') . '">';
            $return[] = '<dt>' . $term . '</dt>';
            $return[] = '<dd>' . (!is_array($desc) ? $desc : implode('</dd>' . PHP_EOL . '<dd>', $desc)) . '</dd>';
            $return[] = '</dl>';
        }
        return implode(PHP_EOL, $return);
    }

    /**
     * Creates a "breadcrumb trail" of links
     *
     * @param array $links
     * @param string $delimiter Optional, greater-than sign is used if ommitted
     * @return string
     */
    public function crumbs(array $links, $delimiter = ' &gt;&gt; ') {
        if ($links) {
            foreach ($links as $url => $title) {
                $return[] = (!is_int($url) ? $this->link($url, $title) : get::htmlentities($title));
            }
            return implode($delimiter, $return);
        }
    }

    /**
     * Create an embedded Flash movie
     *
     * @param string $filename
     * @param array $args
     * @return string
     */
    public function flash($filename, array $args = array()) {
        $args['object']['type'] = 'application/x-shockwave-flash';
        $args['params']['movie'] = $args['object']['data'] = $filename;
        $args['params']['wmode'] = (!isset($args['wmode']) ? 'opaque' : $args['wmode']);

        $dimensions = array('height', 'width');
        foreach ($dimensions as $dimension) {
            if (isset($args[$dimension])) {
                $args['params'][$dimension] = $args['object'][$dimension] = $args[$dimension];
        }
        }

        $objectProperties = array('id', 'class', 'style');
        foreach ($objectProperties as $property) {
            if (isset($args[$property])) {
                $args['object'][$property] = $args[$property];
            }
        }

        $return = '<object ' . self::formatProperties($args['object']) . '>';
        foreach ($args['params'] as $key => $val) {
            $return .= PHP_EOL . '<param name="' . $key . '" ' . 'value="' . $val . '" />';
        }

        if (!isset($args['noFlash'])) {
            $args['noFlash'] = 'Flash file is missing or Flash plugin is not installed';
        }
        $return .= PHP_EOL . $args['noFlash'] . '</object>';
        return $return;
    }

    /**
     * Embeds a PDF file
     *
     * @param string $filename
     * @param array $args
     * @return string
     */
    public function pdf($filename, array $args = array()) {
        $defaults = array('height' => 400, 'width' => 400, 'noPdf' => $this->link($filename, 'Download PDF'));
        $urlParams = array_diff_key($args, $defaults);
        $urlParams = array_merge(array('navpanes' => 0, 'toolbar' => 0), $urlParams); //defaults
        if (isset($urlParams['search'])) {
            $urlParams['search'] = '"' . urlencode($urlParams['search']) . '"';
        }
        if (isset($urlParams['fdf'])) { //fdf must be last in the URL
            $fdf = $urlParams['fdf'];
            unset($urlParams['fdf']);
            $urlParams['fdf'] = $fdf;
        }
        $filename .= '#' . str_replace('%2B', ' ', http_build_query($urlParams, '', '&amp;'));
        $args = array_merge($defaults, $args);
        $return = '<object type="application/pdf" data="' . $filename
                . '" width="' . $args['width'] . '" height="' . $args['height'] . '">'
                . '<param name="src" value="' . $filename . '" />' . $args['noPdf'] . '</object>';
        return $return;
    }

    public function video($filename, array $args = array()) {
        $args['src'] = $filename;
        if (!isset($args['autostart'])) {
            $args['autostart'] = 'false';
        }
        $return = '<object id="MediaPlayer" classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95" type="application/x-oleobject">';
        foreach ($args as $key => $val) {
            $return .= '<param name="' . $key . '" value="' . $val . '" />';
        }
        $return .= '</object>';
        return $return;
    }

    /**
     * Will return true if the number passed in is even, false if odd.
     *
     * @param int $number
     * @return boolean
     */
    public function isEven($number) {
        return (Boolean) ($number % 2 == 0);
    }

    /**
     * Internal incrementing integar for the alternator() method
     * @var int
     */
    private $alternator = 1;

    /**
     * Returns an alternating Boolean, useful to generate alternating background colors
     * Eg.:
     * $colors = array(true => 'gray', false => 'white');
     * echo '<div style="background: ' . $colors[$html->alternator()] . ';">...</div>'; //gray background
     * echo '<div style="background: ' . $colors[$html->alternator()] . ';">...</div>'; //white background
     * echo '<div style="background: ' . $colors[$html->alternator()] . ';">...</div>'; //gray background
     *
     * @return Boolean
     */
    public function alternator() {
        return $this->isEven(++$this->alternator);
    }

    /**
     * Converts a string to its ascii equivalent
     *
     * @param str $str
     * @return str
     */
    public function str2ascii($str) {
        $ascii = '';
        $strLen = strlen($str);
        for ($i = 0; $i < $strLen; $i++) {
            $ascii .= '&#' . ord($str[$i]) . ';';
        }
        return $ascii;
    }

    /**
     * Assures a unique ID is used for all the IDs used in the email() method
     * @var int
     */
    private $_emailCounter = 0;

    public function getEmailCounter() {
        return $this->_emailCounter;
    }

    /**
     * Returns a spam-resistant email link
     *
     * @param str $email Must be a valid email address
     * @param str $linkText (optional) the text visible on the link, if ommitted the email address will be used
     * @return str
     */
    public function email($email, $linkText = null) {
        $linkText = str_replace('"', '\"', $linkText);
        $var = (!$this->_emailCounter ? 'var ' : '');
        $emailHalves = explode('@', $email);
        if (!isset($emailHalves[1])) {
            return $email;
        }
        $emailDomainParts = explode('.', $emailHalves[1]);
        if (!$this->_emailCounter) {
            $initJs = /*$this->addSnippetToHead('jsInline', */'var doar, noSpm; var doarlink = function(doartext, noSpm) {
    doartext.style.cursor = "pointer";
    doartext.onclick = function() {window.onerror = function() {return true;}; '
                                . 'window.location = "mai" + "lto:" + noSpm.replace("&#0064;", "@");};
    doartext.onmouseover = function() {doartext.style.textDecoration = "underline";}
    doartext.onmouseout = function() {doartext.style.textDecoration = "none";}
}'/*)*/; //commenting out the addSnippetToHead puts in the first occurrence of the inline code
        }
        $id = ++$this->_emailCounter;
        $noScript = $emailHalves[0] . ' -&#0064;- ' . implode('.', $emailDomainParts);
        if (!$this->_isHtml5) {
            $noScript = '<div style="display: inline;">' . $noScript . '</div>';
        }
        return '<span id="doar' . $id . '" class="textLink"></span><noscript>' . $noScript . '</noscript>'
             . $this->jsInline($this->jsTools(true) . (isset($initJs) ? $initJs : '') . '
doar = dom("doar' . $id . '");
if (doar) {
    noSpm = "' . $emailHalves[0] . '";
    noSpm += "&#0064;";
    noSpm += "' . implode('." + "', $emailDomainParts) . '";
    doar.innerHTML = "<span id=\"doartext' . $id . '\">" + ' . ($linkText ? '"' . $linkText . '"' : 'noSpm') . ' + "</span>";
    doarlink(dom("doartext' . $id . '"), noSpm);
}');
    }

    /**
     * Returns a list of notifications if there are any - similar to the Flash feature of Ruby on Rails
     *
     * @param mixed $messages String or an array of strings
     * @param string $class
     * @return string Returns null if there are no notifications to return
     */
    public function getNotifications($messages, $class = 'errormessage') {
        if ($messages) {
            return $this->div((is_array($messages) ? implode('<br />', $messages) : $messages), compact('class'));
        }
    }

    /**
     * Formats a timestamp in human-readable proximity-since/until format
     *
     * @param int $ts Timestamp or a date/time that is in a format that can be cast into a timestamp
     * @return string
     */
    public function howLongAgo($ts) {
        $now = time();
        if (!is_numeric($ts)) {
            $ts = strtotime($ts);
        }
        $ago = ($now > $ts ? 'ago' : 'from now');
        $secondsAgo = abs($now - $ts);
        if ($secondsAgo < 5) {
            $return = 'just now';
        } else if ($secondsAgo < 91) { //5 to 90-seconds
            $return = $secondsAgo . ' seconds ' . $ago;
        } else if ($secondsAgo < 5400) { //2-90 minutes
            $return = round($secondsAgo / 60) . ' minutes ' . $ago;
        } else if ($secondsAgo < 86400) { //2-24 hours
            $return = round($secondsAgo / 3600) . ' hours ' . $ago;
        } else if ($secondsAgo < 172800) { //24-48 hours
            $return = ($now > $ts ? 'yesterday' : 'tomorrow');
        } else if ($secondsAgo < 31536000) { //up to 1-year
            $return = round($secondsAgo / 86400) . ' days ' . $ago;
        } else { //1+ year
            $return = date('M. j, Y', $ts);
        }
        return $return;
    }

    /**
     * Results of last usage of maxlength()
     * @var boolean
     */
    public $withinMaxLength;

    /**
     * Returns a string no longer than a fixed length
     * If the original string exceeds the set length then it is trimmed and then $append string is appended to it
     *
     * @param string $str
     * @param int $len
     * @param string $append Optional, defaults to ...
     * @return string
     */
    public function maxlength($str, $len, $append = '...') {
        $strLen = strlen($str);
        $this->withinMaxLength = ($strLen <= $len);
        if (!$this->withinMaxLength) {
            $appendLen = strlen($append);
            $str = substr($str, 0, ($len - $appendLen));
            $str = substr($str, 0, strrpos($str, ' ')) . $append;
        }
        return $str;
    }

    /**
     * Formats price, including reducing to two-digit precision
     *
     * @param mixed $price
     * @return float (values under 1,000) or string (values 1,000+) - this is due to the comma-separator
     */
    public function price($price) {
        return number_format(round((float) $price, 2), 2);
    }

    /**
     * Formats telephone numbers containing 9 or 10 numeric digits, all other numbers pass through unmodified
     *
     * @param mixed $telephone
     * @return string
     */
    public function telephone($telephone) {
        $tel = preg_replace('/\D/', '', $telephone);
        $telLength = strlen($tel);
        switch ($telLength) {
            case 11:
                if (substr($tel, 0, 1) == '1') { //1-800-555-1212 format
                    $tel = substr($tel, 1);
                } else {
                    break;
                }
            case 10: // N. American format
                $telephone = '(' . substr($tel, 0, 3) . ') ' . substr($tel, 3, 3) . '-' . substr($tel, 6);
                break;
            case 9:  // S. American/Mid-East landline format
                $telephone = '(' . substr($tel, 0, 2) . ') ' . substr($tel, 2, 3) . '-' . substr($tel, 5);
                break;
        }
        return $telephone;
    }
}