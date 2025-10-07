<?php

use App\Controllers\ViewController;
use Pecee\SimpleRouter\SimpleRouter as Router;

Router::get('/', [ViewController::class, 'handle']);
Router::get('/{any}', [ViewController::class, 'handle']);