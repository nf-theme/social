<?php

namespace NightFury\Social\Facades;

use Illuminate\Support\Facades\Facade;

class Social extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \NightFury\Social\Manager::class;
    }
}