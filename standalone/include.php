<?php
//THIS FILE IS INCLUDED TO MAKE THE PAGES LOAD WITH THE FRAMEWORK, IT IS NOT USED IN THE ACTUAL WEBSITE
date_default_timezone_set('America/New_York');
define('DEBUG_MODE', false);
class get {
        public static function htmlentities($string, $quoteStyle = ENT_COMPAT, $charset = 'UTF-8', $doubleEncode = false) {
        return htmlentities($string, (!is_null($quoteStyle) ? $quoteStyle : ENT_COMPAT),
                            (!is_null($charset) ? $charset : 'UTF-8'), $doubleEncode);
    }
    public static function helper($helper) {
        include_once $helper . '.php';
        $className =  $helper . 'Helper';
        return new $className;
    }
    public static function component($helper) {
        include_once $helper . '.php';
        $className =  $helper . 'Component';
        return new $className;
    }
    public static $loadedObjects = array();
        protected static function initXhtmlentities($quoteStyle, $charset, $doubleEncode) {
        $chars = get_html_translation_table(HTML_ENTITIES, $quoteStyle);
        if (isset($chars)) {
            unset($chars['<'], $chars['>'], $chars['&']);
            $charMaps[$quoteStyle]['ISO-8859-1'][true] = $chars;
            $charMaps[$quoteStyle]['ISO-8859-1'][false] = array_combine(array_values($chars), $chars);
            $charMaps[$quoteStyle]['UTF-8'][true] = array_combine(array_map('utf8_encode', array_keys($chars)), $chars);
            $charMaps[$quoteStyle]['UTF-8'][false] = array_merge($charMaps[$quoteStyle]['ISO-8859-1'][false],
                                                                 $charMaps[$quoteStyle]['UTF-8'][true]);
            self::$loadedObjects['xhtmlEntities'] = $charMaps;
        }
        if (!isset($charMaps[$quoteStyle][$charset][$doubleEncode])) {
            if (!isset($chars)) {
                $invalidArgument = 'quoteStyle = ' . $quoteStyle;
            } else if (!isset($charMaps[$quoteStyle][$charset])) {
                $invalidArgument = 'charset = ' . $charset;
            } else {
                $invalidArgument = 'doubleEncode = ' . (string) $doubleEncode;
            }
            trigger_error('Undefined argument sent to xhtmlentities() method: ' . $invalidArgument, E_USER_NOTICE);
        }
    }
    public static function xhtmlentities($string, $quoteStyle = ENT_NOQUOTES, $charset = 'UTF-8',
                                         $doubleEncode = false) {
        $quoteStyles = array(ENT_NOQUOTES, ENT_QUOTES, ENT_COMPAT);
        $quoteStyle = (!in_array($quoteStyle, $quoteStyles) ? current($quoteStyles) : $quoteStyle);
        $charset = ($charset != 'ISO-8859-1' ? 'UTF-8' : $charset);
        $doubleEncode = (Boolean) $doubleEncode;
        if (!isset(self::$loadedObjects['xhtmlEntities'][$quoteStyle][$charset][$doubleEncode])) {
            self::initXhtmlentities($quoteStyle, $charset, $doubleEncode);
        }
        $string = strtr($string, self::$loadedObjects['xhtmlEntities'][$quoteStyle][$charset][$doubleEncode]);
        return preg_replace('/\&(?!([a-zA-Z]{3}[a-zA-Z0-9]{0,3}|#\d{1,8}|#x[a-zA-Z0-9]{2,8});)/', '&amp;', $string);
    }
    public static $vyOptions = array(
        'fosterCurrent' => 'Foster care (currently)',
        'adoptable' => 'Foster care AND seeking adoptive family',
        'fosterFormer' => 'Previously in foster care but not now',
        'agedOut' => 'Aged-out or emancipated from foster care',
        'homeless' => 'Homeless, runaway or living in a shelter',
        'jd' => 'Justice involved (have been arrested)',
        'probation' => 'On probation or parole',
        'incarcerated' => 'Have been incarcerated (in jail/prison/juvie)',
        'acs' => 'ACS preventative services',
        'parent' => 'Parent or pregnant',
        'disabled' => 'Legally-disabled',
        'specialNeeds' => 'Have special needs',
        'other' => 'Other'
      );
}
$html = get::helper('html');
$form = get::helper('form');
$tools = get::helper('tools');