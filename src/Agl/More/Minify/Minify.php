<?php
namespace Agl\More\Minify;

use \Agl\Core\Agl,
    \Agl\Core\Mvc\View\View,
    \Agl\Core\Mvc\View\ViewInterface,
    \Agl\Core\Mvc\View\Type\Html as HtmlView,
    \Agl\Core\Url\Url,
    \CssMin,
    \Exception,
    \JSMinPlus;

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
     * The minify directory in the app public pool.
     */
    const MINIFY_DIR = 'minify';

    /**
     * The Minify constructor.
     * Chck if the Minify dir exists and is writable (required).
     */
    public function __construct()
    {
        $dir = self::_getAbsoluteMinifyDir();
        if (! is_writable($dir)) {
            throw new Exception("The minify directory '$dir' is not writable");
        }
    }

    /**
     * Return the absolute path to the Minify public directory.
     *
     * @return tring
     */
    private static function _getAbsoluteMinifyDir()
    {
        return APP_PATH
               . Agl::APP_PUBLIC_DIR
               . DS
               . self::MINIFY_DIR
               . DS;
    }

    /**
     * Return the relative path to the minified CSS/JS file.
     *
     * @param string $pFile Filename, without extension
     * @param string $pExt File extension
     * @return tsring
     */
    private static function _getRelativeFile($pFile, $pExt)
    {
        return ROOT
               . Agl::APP_PUBLIC_DIR
               . DS
               . self::MINIFY_DIR
               . DS
               . $pFile
               . $pExt;
    }

    /**
     * Return the absolute path to the minified CSS/JS file.
     *
     * @param string $pFile Filename, without extension
     * @param string $pExt File extension
     * @return string
     */
    private static function _getAbsoluteFile($pFile, $pExt)
    {
        return self::_getAbsoluteMinifyDir()
               . $pFile
               . $pExt;
    }

    /**
     * Return the absolute path of CSS or JS file in the "skin" directory.
     *
     * @param string $pFile Filename, with extension
     * @param string $pSubDir Skin subdirectory
     */
    private static function _getAbsoluteSkinPath($pFile, $pSubDir)
    {
        return APP_PATH
               . Agl::APP_PUBLIC_DIR
               . DS
               . ViewInterface::APP_HTTP_SKIN_DIR
               . DS
               . $pSubDir
               . DS
               . $pFile;
    }

    /**
     * Minify the CSS files if minified file not exists (or if source files was
     * modified) and create HTML tags.
     *
     * @param View $view
     * @return string
     */
    public function getCssCache(View $view)
    {
        $view->loadCss();

        $cssFiles      = $view->cssToArray();
        $cssTags       = array();
        $fileName      = md5(implode($cssFiles));
        $cacheFilePath = self::_getAbsoluteFile($fileName, HtmlView::CSS_EXT);
        $forceReload   = false;

        if (is_readable($cacheFilePath)) {
            $cacheFileUpdatedAt = filemtime($cacheFilePath);

            foreach ($cssFiles as $cssFile) {
                $cssFilePath = self::_getAbsoluteSkinPath($cssFile, $view::APP_HTTP_CSS_DIR);
                if (is_readable($cssFilePath) and filemtime($cssFilePath) > $cacheFileUpdatedAt) {
                    $forceReload = true;
                    break;
                }
            }
        } else {
            $forceReload = true;
        }

        if ($forceReload) {
            $compressor = new CssMin();
            $content    = '';

            foreach($cssFiles as $css) {
                if (! filter_var($css, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) and ! preg_match('/^\/\//', $css)) {
                    $content .= "\n" . file_get_contents(self::_getAbsoluteSkinPath($css, $view::APP_HTTP_CSS_DIR)) . "\n";
                }
            }

            $content = str_replace(
                array(
                    'url(../../',
                    'url(../'
                ), array(
                    'url('  . Url::getSkin($view::APP_HTTP_CSS_DIR),
                    'url('  . Url::getSkin('')
                ), $content);

            $minifiedContent = $compressor->minify($content);

            file_put_contents($cacheFilePath, $minifiedContent);
        }

        foreach($cssFiles as $css) {
            if (filter_var($css, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) or preg_match('/^\/\//', $css)) {
                $cssTags[] = '<link href="' . $css . '" rel="stylesheet" type="text/css">';
            }
        }

        $cssTags[] = '<link href="' . self::_getRelativeFile($fileName, HtmlView::CSS_EXT) . '" rel="stylesheet" type="text/css">';

        return implode("\n", $cssTags);
    }

    /**
     * Minify the JS files if minified file not exists and create HTML tags.
     *
     * @param View $view
     * @return string
     */
    public function getJsCache(View $view)
    {
        $view->loadJs();

        $jsFiles       = $view->jsToArray();
        $jsTags        = array();
        $fileName      = md5(implode($jsFiles));
        $cacheFilePath = self::_getAbsoluteFile($fileName, HtmlView::JS_EXT);
        $forceReload   = false;

        if (is_readable($cacheFilePath)) {
            $cacheFileUpdatedAt = filemtime($cacheFilePath);

            foreach ($jsFiles as $jsFile) {
                $jsFilePath = self::_getAbsoluteSkinPath($jsFile, $view::APP_HTTP_JS_DIR);
                if (is_readable($jsFilePath) and filemtime($jsFilePath) > $cacheFileUpdatedAt) {
                    $forceReload = true;
                    break;
                }
            }
        } else {
            $forceReload = true;
        }

        if ($forceReload) {
            $content = '';

            foreach($jsFiles as $js) {
                if (! filter_var($js, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) and ! preg_match('/^\/\//', $js)) {
                    $content .= "\n" . file_get_contents(self::_getAbsoluteSkinPath($js, $view::APP_HTTP_JS_DIR)) . "\n";
                }
            }

            $minifiedContent = JSMinPlus::minify($content);
            file_put_contents($cacheFilePath, $minifiedContent);
        }

        foreach($jsFiles as $js) {
            if (filter_var($js, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) or preg_match('/^\/\//', $js)) {
                $jsTags[] = '<script src="' . $js . '" type="text/javascript"></script>';
            }
        }

        $jsTags[] = '<script src="' . self::_getRelativeFile($fileName, HtmlView::JS_EXT) . '" type="text/javascript"></script>';

        return implode("\n", $jsTags);
    }
}
