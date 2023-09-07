<?php

namespace BytePlatform\Laravel;

use Illuminate\Support\Facades\Facade;

/**
 * 
 * @method static mixed addListener(string|array $hook, mixed $callback,int  $priority)
 * @method static \BytePlatform\Laravel\Hook\ActionHook removeListener(string  $hook)
 * @method static array getListeners()
 * @method static mixed fire(string  $action,array  $args)
 *
 * @see \BytePlatform\Laravel\Action
 */
class Action extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \BytePlatform\Laravel\Hook\ActionHook::class;
    }
}
