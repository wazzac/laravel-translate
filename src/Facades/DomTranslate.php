<?php

namespace Wazza\DomTranslate\Facades;

use Illuminate\Support\Facades\Facade;

class DomTranslate extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'domtranslate';
    }
}
