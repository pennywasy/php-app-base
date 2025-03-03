<?php

error_reporting(E_ERROR | E_PARSE);


require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/app/Core/Loader.php';


try {
    \App\Core\Loader::register();

    $app = new \App\Core\App();

    $app->run();
}catch (Exception $e) {
    throw new Exception($e->getMessage());
}
