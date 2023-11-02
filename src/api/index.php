<?php

require_once __DIR__.'/core/Core.php';
require_once __DIR__.'/routes/routes.php';

$core = new Core();
$core->run($routes);

