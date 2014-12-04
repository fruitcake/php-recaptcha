<?php

namespace FruitcakeStudio\ReCaptcha\Support\Laravel;

use Illuminate\Support\Facades\Facade as BaseFacade;

/**
 * Laravel Facade for ReCaptcha
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @author Fruitcake Studio (http://fruitcakestudio.com)
 */
class Facade extends BaseFacade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'recaptcha';
    }
}
