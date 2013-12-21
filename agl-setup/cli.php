<?php
$currentDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;

return array(

    // Copy configuration file.
    'file:copy' => array(
        $currentDir . 'app/etc/config/more/minify/events.php',
        APP_PATH    . 'app/etc/config/more/minify/events.php'
    )
);
