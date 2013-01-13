<?php
namespace Agl\More\Minify;

use \Agl,
    \Agl\Core\Mvc\View\ViewInterface;

/**
 * Observer for Minify module.
 *
 * @category Agl_More
 * @package Agl_More_Minify
 * @version 0.1.0
 */

class Observer
{
    /**
     * Handle the buffer before it is rendered, minify CSS and JS files and
     * add HTML tags to the page.
     *
     * @param array $pObserver
     * @return bool
     */
    public static function minify(array $pObserver)
    {
        if (! Agl::app()->isCacheEnabled()) {
            return false;
        }

        $view = $pObserver['view'];
        if ($view->getType() != ViewInterface::TYPE_HTML) {
            return false;
        }

        $minify              = Agl::getSingleton(Agl::AGL_MORE_DIR . '/minify/minify');

        $cssTags             = $minify->getCssCache($view);
        $pObserver['buffer'] = str_replace($view->getCssMarker(), $cssTags . "\n", $pObserver['buffer']);

        $jsTags              = $minify->getJsCache($view);
        $pObserver['buffer'] = str_replace($view->getJsMarker(), $jsTags . "\n", $pObserver['buffer']);

        return true;
    }
}
