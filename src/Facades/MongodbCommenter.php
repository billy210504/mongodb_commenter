<?php

namespace Billy\MongodbCommenter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Billy\MongodbCommenter\MongodbCommenter
 */
class MongodbCommenter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Billy\MongodbCommenter\MongodbCommenter::class;
    }
}
