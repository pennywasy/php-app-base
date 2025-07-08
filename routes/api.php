<?php

use App\Controllers\TestController;
use Pecee\SimpleRouter\SimpleRouter as Router;
use App\Middlewares\ProccessRawBody;


Router::group([
    'prefix' => 'api/v1',
    'middleware' => [
        ProccessRawBody::class
    ]
], function () {

    Router::post('/foo', [TestController::class, 'create']);
    Router::get('/foo/{id}', [TestController::class, 'read']);
    Router::put('/foo/{id}', [TestController::class, 'update']);
    Router::delete('/foo/{id}', [TestController::class, 'delete']);
});

