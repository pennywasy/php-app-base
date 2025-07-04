<?php

use App\Controllers\FileController;
use App\Controllers\ParserController;
use Pecee\SimpleRouter\SimpleRouter as Router;
use App\Middlewares\ProccessRawBody;


Router::group([
    'prefix' => 'api/v1',
    'middleware' => [
        ProccessRawBody::class
    ]
], function () {

});

