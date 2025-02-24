<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\core\Application;

$app = new Application();

require_once ROOT_DIR . '/config/routes.php';
require_once ROOT_DIR . '/config/middlewares/AuthMiddleware.php';

$result = $app->router->dispatch($app);


echo $result;



