<?php
$currentDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;

return array(

    // Copy configuration file and setup the "minify" directory.
    'file:copy' => array(
        array(
            $currentDir . 'app/etc/config/more/minify/events.php',
            $appPath    . 'app/etc/config/more/minify/events.php'
        ),
        array(
            $currentDir . 'public/minify/gitignore',
            $appPath    . 'public/minify/.gitignore'
        )
    )
);
