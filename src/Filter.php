<?php

namespace Sokeio\Laravel;

use Illuminate\Support\Facades\Facade;

/**
 * 
 * @method static mixed addListener(string|array $hook, mixed $callback,int  $priority)
 * @method static \Sokeio\Laravel\Hook\FilterHook removeListener(string  $hook)
 * @method static array getListeners()
 * @method static mixed fire(string  $action,array  $args)
 *
 * @see \Sokeio\Laravel\Filter
 */
class Filter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Sokeio\Laravel\Hook\FilterHook::class;
    }
}
