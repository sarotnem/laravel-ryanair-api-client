<?php

namespace Sarotnem\RyanairApiClient\Facades;

use Illuminate\Support\Facades\Facade;

class RyanairApiClientFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ryanair-api-client';
    }
}
