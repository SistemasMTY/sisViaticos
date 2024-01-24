<?php

namespace sisViaticos\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */

     protected static $serialize = true;
    protected $except = [
        //
    ];
}
