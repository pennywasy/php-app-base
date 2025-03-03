<?php

use Pecee\SimpleRouter\SimpleRouter as Router;
use App\Controllers\BitrixController;
use App\Controllers\CallController;
use App\Middlewares\AuthVerification;
use App\Controllers\RechkaController;

Router::get("/", function () {
    return "Hello, world";
});