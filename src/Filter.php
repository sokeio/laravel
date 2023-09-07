<?php

namespace BytePlatform\Laravel;

use Illuminate\Support\Facades\Facade;

/**
 * 
 * @method static mixed addListener(string|array $hook, mixed $callback,int  $priority)
 * @method static \BytePlatform\Laravel\Hook\FilterHook removeListener(string  $hook)
 * @method static array getListeners()
 * @method static mixed fire(string  $action,array  $args)
 *
 * @see \BytePlatform\Laravel\Filter
 */
class Filter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \BytePlatform\Laravel\Hook\FilterHook::class;
    }
}
