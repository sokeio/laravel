<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Sokeio\Laravel\Action;
use Sokeio\Laravel\Filter;
use Sokeio\Laravel\Pipe\Pipe;

define('THEME_SCOPE', 'theme::scope.');
if (!function_exists('addAction')) {
    /**
     * @param  string | array  $hook
     * @param $callback
     * @param  int  $priority
     * @param  int  $arguments
     */
    function addAction($hook, $callback, int $priority = 20, int $arguments = 1)
    {
        Action::addListener($hook, $callback, $priority, $arguments);
    }
}

if (!function_exists('removeAction')) {
    /**
     * @param  string  $hook
     */
    function removeAction($hook, $callback = null)
    {
        Action::removeListener($hook, $callback);
    }
}
if (!function_exists('doAction')) {
    /**
     * @param  string  $hook
     */
    function doAction()
    {
        $args = func_get_args();
        Action::fire(array_shift($args), $args);
    }
}

if (!function_exists('addFilter')) {
    /**
     * @param  string | array  $hook
     * @param $callback
     * @param  int  $priority
     * @param  int  $arguments
     */
    function addFilter($hook, $callback, int $priority = 20, int $arguments = 1)
    {
        Filter::addListener($hook, $callback, $priority, $arguments);
    }
}
if (!function_exists('removeFilter')) {
    /**
     * @param  string  $hook
     */
    function removeFilter($hook, $callback)
    {
        Filter::removeListener($hook, $callback);
    }
}

if (!function_exists('applyFilters')) {
    /**
     * @return mixed
     */
    function applyFilters()
    {
        $args = func_get_args();

        return Filter::fire(array_shift($args), $args);
    }
}

if (!function_exists('getHooks')) {
    /**
     * @param  string|null  $name
     * @param  bool  $isFilter
     * @return array
     */
    function getHooks(?string $name = null, bool $isFilter = true): array
    {
        if ($isFilter) {
            $listeners = Filter::getListeners();
        } else {
            $listeners = Action::getListeners();
        }

        if (empty($name)) {
            return $listeners;
        }

        return Arr::get($listeners, $name, []);
    }
}


if (!function_exists('hasHooks')) {
    /**
     * @param  string|null  $name
     * @param  bool  $isFilter
     * @return bool
     */
    function hasHooks(?string $name = null, bool $isFilter = true): bool
    {
        if (!$name || empty($name)) {
            return false;
        }
        if ($isFilter) {
            $listeners = Filter::getListeners();
        } else {
            $listeners = Action::getListeners();
        }

        return count(Arr::get($listeners, $name, [])) > 0;
    }
}


if (!function_exists('viewScope')) {
    /**
     * Get the evaluated view contents for the given view and Support for theme
     * 
     * @param  string|null  $view
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @param  string|null   $viewDefault
     * @param  bool  $isModal
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    function viewScope($view = null, $data = [], $viewDefault = null)
    {
        $tempView = $view;
        $arr = explode('::', $view);
        $len = count($arr);
        if ($len == 1 && View::exists(THEME_SCOPE . $arr[0])) {
            $tempView = THEME_SCOPE . $arr[0];
        }
        if ($len == 2 && View::exists(THEME_SCOPE . $arr[1])) {
            $tempView = THEME_SCOPE . $arr[1];
        }
        if ($len == 2 && View::exists(THEME_SCOPE . $arr[0] . '.' . $arr[1])) {
            $tempView = THEME_SCOPE . $arr[0] . '.' . $arr[1];
        }
        if ($viewDefault && !View::exists($tempView)) {
            $tempView = $viewDefault;
        }
        return view($tempView, $data);
    }
}

if (!function_exists('hasViewScope')) {
    /**
     * Get the evaluated view contents for the given view and Support for theme
     * 
     * @param  string|null  $view
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    function hasViewScope($view = null)
    {
        if (!$view) {
            return false;
        }
        $arr = explode('::', $view);
        $len = count($arr);
        $flg = false;
        if ($len == 1 && View::exists(THEME_SCOPE . $arr[0])) {
            $flg = true;
        }
        if ($len == 2 && View::exists(THEME_SCOPE . $arr[1])) {
            $flg = true;
        }
        if ($len == 2 && View::exists(THEME_SCOPE . $arr[0] . '.' . $arr[1])) {
            $flg = true;
        }

        return $flg || View::exists($view);
    }
}



if (!function_exists('isEnum')) {
    function isEnum(object $potentialEnum): bool
    {
        return $potentialEnum instanceof StringBackedEnum
            || $potentialEnum instanceof BackedEnum
            || $potentialEnum instanceof IntBackedEnum;
    }
}

if (!function_exists('pipe')) {
    function pipe(mixed $passable = null): Pipe
    {
        return $passable ? app(Pipe::class)->send($passable) : app(Pipe::class);
    }
}
