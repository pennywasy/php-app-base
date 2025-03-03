<?php

namespace App\Core;

use Pecee\SimpleRouter\SimpleRouter;

class Response
{
    /**
     * @return \Pecee\Http\Response
     */
    public static function response(): \Pecee\Http\Response
    {
        return SimpleRouter::response();
    }
}
