<?php
namespace Agl\More\Minify;

/**
 * Minify CSS and JS files.
 *
 * @category Agl_More
 * @package Agl_More_Minify
 * @version 0.1.0
 */

class Minify
{
    /**
     * JS lib filename.
     */
    const JS_LIB = 'jsmin.php';

    /**
     * CSS lib filename.
     */
    const CSS_LIB = 'cssmin.php';

    /**
     * The minify directory in the app public pool.
     */
    const MINIFY_DIR = 'minify';

    /**
     * The minified CSS file extension.
     */
    const CSS_EXT = '.css';

    /**
     * The minified JS file extension.
     */
    const JS_EXT = '.js';

    /**
     * The Minify constructor.
     * Chck if the Minify dir exists and is writable (required).
     */
    public function __construct()
    {
        $dir = $this->_getAbsoluteMinifyDir();
        if (! is_writable($dir)) {
            throw new \Agl\Exception("The minify directory '$dir' is not writable");
        }
    }

    /**
     * Return the absolute path to the Minify public directory.
     *
     * @return tring
     */
    private function _getAbsoluteMinifyDir()
    {
        return \Agl::app()->getPath()
               . \Agl::APP_PUBLIC_DIR
               . DS
               . self::MINIFY_DIR
               . DS;
    }

    /**
     * Return the relative path to the minified CSS file.
     *
     * @return tring
     */
    private function _getRelativeCssFile($pFile)
    {
        return ROOT
               . \Agl::APP_PUBLIC_DIR
               . DS
               . self::MINIFY_DIR
               . DS
               . $pFile
               . self::CSS_EXT;
    }

    /**
     * Return the absolute path to the minified CSS file.
     *
     * @return tring
     */
    private function _getAbsoluteCssFile($pFile)
    {
        return $this->_getAbsoluteMinifyDir() . $pFile . self::CSS_EXT;
    }

    /**
     * Return the relative path to the minified JS file.
     *
     * @return tring
     */
    private function _getRelativeJsFile($pFile)
    {
        return ROOT
               . \Agl::APP_PUBLIC_DIR
               . DS
               . self::MINIFY_DIR
               . DS
               . $pFile
               . self::JS_EXT;
    }

    /**
     * Return the absolute path to the minified JS file.
     *
     * @return tring
     */
    private function _getAbsoluteJsFile($pFile)
    {
        return $this->_getAbsoluteMinifyDir() . $pFile . self::JS_EXT;
    }

    /**
     * Minify the CSS files if minified file not exists and create HTML tags.
     *
     * @param View $view
     * @return string
     */
    public function getCssCache(\Agl\Core\Mvc\View\View $view)
    {
        $view->loadCss();
        $cssFiles = $view->cssToArray();
        $cssTags  = array();
        $fileName = md5(implode($cssFiles));

        if (! is_readable($this->_getAbsoluteCssFile($fileName))) {
            \Agl::loadModuleLib(__DIR__, self::CSS_LIB);
            $minifiedContent = '';

            foreach($cssFiles as $css) {
                if (! filter_var($css, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) and ! preg_match('/^\/\//', $css)) {
                    $minifiedContent .= "\n" . file_get_contents(
                        \Agl::app()->getPath()
                        . \Agl::APP_PUBLIC_DIR
                        . DS
                        . \Agl\Core\Mvc\View\ViewInterface::APP_HTTP_SKIN_DIR
                        . DS
                        . \Agl::app()->getConfig('@app/global/theme')
                        . DS
                        . $view::APP_HTTP_CSS_DIR
                        . DS
                        . $css
                    ) . "\n";
                }
            }

            $minifiedContent = str_replace('url(../', 'url(' . \Agl::getSkinUrl(''), $minifiedContent);
            $minifiedContent = \CssMin::minify($minifiedContent);
            file_put_contents($this->_getAbsoluteCssFile($fileName), $minifiedContent);
        }

        foreach($cssFiles as $css) {
            if (filter_var($css, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) or preg_match('/^\/\//', $css)) {
                $cssTags[] = '<link href="' . $css . '" rel="stylesheet" type="text/css" media="all">';
            }
        }

        $cssTags[] = '<link href="' . $this->_getRelativeCssFile($fileName) . '" rel="stylesheet" type="text/css" media="all">';

        return implode("\n", $cssTags);
    }

    /**
     * Minify the JS files if minified file not exists and create HTML tags.
     *
     * @param View $view
     * @return string
     */
    public function getJsCache(\Agl\Core\Mvc\View\View $view)
    {
        $view->loadJs();
        $jsFiles  = $view->jsToArray();
        $jsTags   = array();
        $fileName = md5(implode($jsFiles));

        if (! is_readable($this->_getAbsoluteJsFile($fileName))) {
            \Agl::loadModuleLib(__DIR__, self::JS_LIB);
            $minifiedContent = '';

            foreach($jsFiles as $js) {
                if (! filter_var($js, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) and ! preg_match('/^\/\//', $js)) {
                    $minifiedContent .= "\n" . file_get_contents(
                        \Agl::app()->getPath()
                        . \Agl::APP_PUBLIC_DIR
                        . DS
                        . \Agl\Core\Mvc\View\ViewInterface::APP_HTTP_SKIN_DIR
                        . DS
                        . \Agl::app()->getConfig('@app/global/theme')
                        . DS
                        . $view::APP_HTTP_JS_DIR
                        . DS
                        . $js
                    ) . "\n";
                }
            }

            $minifiedContent = \JsMin::minify($minifiedContent);
            file_put_contents($this->_getAbsoluteJsFile($fileName), $minifiedContent);
        }

        foreach($jsFiles as $js) {
            if (filter_var($js, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) or preg_match('/^\/\//', $js)) {
                $jsTags[] = '<script src="' . $js . '" type="text/javascript"></script>';
            }
        }

        $jsTags[] = '<script src="' . $this->_getRelativeJsFile($fileName) . '" type="text/javascript"></script>';

        return implode("\n", $jsTags);
    }
}
