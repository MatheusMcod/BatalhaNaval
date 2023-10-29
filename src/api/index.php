<?php

require_once __DIR__.'/core/Core.php';
require_once __DIR__.'/routes/routes.php';

spl_autoload_register(function($file) {
    if(file_exists(__DIR__."/src/api/models/$file.php")) {
        require_once __DIR__."/models/$file.php";
    }
});

$core = new Core();
$core->run($routes);

