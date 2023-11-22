<?php

namespace Sokeio\Laravel;

use Illuminate\Support\Facades\Facade;

/**
 * 
 * @method static mixed addListener(string|array $hook, mixed $callback,int  $priority)
 * @method static \Sokeio\Laravel\Hook\ActionHook removeListener(string  $hook)
 * @method static array getListeners()
 * @method static mixed fire(string  $action,array  $args)
 *
 * @see \Sokeio\Laravel\Action
 */
class Action extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Sokeio\Laravel\Hook\ActionHook::class;
    }
}
