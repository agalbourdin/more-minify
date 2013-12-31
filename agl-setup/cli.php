<?php
$currentDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;

return array(

    // Copy configuration file.
    'file:copy' => array(
        $currentDir . 'app/etc/config/more/minify/events.php',
        $appPath    . 'app/etc/config/more/minify/events.php'
    ),

    // Create "minify" directory.
    'dir:create' => array(
        $appPath . 'public/minify/'
    )
);
