<?php

namespace App\Core;

use Pecee\SimpleRouter\SimpleRouter;

class Request
{
    /**
     * @return \Pecee\Http\Request
     */
    public static function request(): \Pecee\Http\Request
    {
        return SimpleRouter::request();
    }
}