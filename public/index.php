<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

error_reporting(E_ERROR | E_PARSE);


require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../app/Core/Loader.php';


try {
    \app\Core\Loader::register();

    $app = new \app\Core\App();

    $app->run();
}catch (Exception $e) {
    throw new Exception($e->getMessage());
}
