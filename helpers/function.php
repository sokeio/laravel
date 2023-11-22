<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Sokeio\Laravel\Action;
use Sokeio\Laravel\Filter;
use Sokeio\Laravel\Pipe\Pipe;

if (!function_exists('add_action')) {
    /**
     * @param  string | array  $hook
     * @param $callback
     * @param  int  $priority
     * @param  int  $arguments
     */
    function add_action($hook, $callback, int $priority = 20, int $arguments = 1)
    {
        Action::addListener($hook, $callback, $priority, $arguments);
    }
}

if (!function_exists('remove_action')) {
    /**
     * @param  string  $hook
     */
    function remove_action($hook, $callback = null)
    {
        Action::removeListener($hook, $callback);
    }
}
if (!function_exists('do_action')) {
    /**
     * @param  string  $hook
     */
    function do_action()
    {
        $args = func_get_args();
        Action::fire(array_shift($args), $args);
    }
}

if (!function_exists('add_filter')) {
    /**
     * @param  string | array  $hook
     * @param $callback
     * @param  int  $priority
     * @param  int  $arguments
     */
    function add_filter($hook, $callback, int $priority = 20, int $arguments = 1)
    {
        Filter::addListener($hook, $callback, $priority, $arguments);
    }
}
if (!function_exists('remove_filter')) {
    /**
     * @param  string  $hook
     */
    function remove_filter($hook, $callback)
    {
        Filter::removeListener($hook, $callback);
    }
}

if (!function_exists('apply_filters')) {
    /**
     * @return mixed
     */
    function apply_filters()
    {
        $args = func_get_args();

        return Filter::fire(array_shift($args), $args);
    }
}

if (!function_exists('get_hooks')) {
    /**
     * @param  string|null  $name
     * @param  bool  $isFilter
     * @return array
     */
    function get_hooks(?string $name = null, bool $isFilter = true): array
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


if (!function_exists('has_hooks')) {
    /**
     * @param  string|null  $name
     * @param  bool  $isFilter
     * @return bool
     */
    function has_hooks(?string $name = null, bool $isFilter = true): bool
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


if (!function_exists('view_scope')) {
    /**
     * Get the evaluated view contents for the given view and Support for theme
     * 
     * @param  string|null  $view
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @param  bool  $isModal
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    function view_scope($view = null, $data = [], $mergeData = [], $isModal = false)
    {
        $tempView = $view;
        if ($view && $arr = explode('::', $view)) {

            if (count($arr) == 1) {
                if (View::exists('theme::scope.' . $arr[0])) {
                    $tempView = 'theme::scope.' . $arr[0];
                }
            } else if (count($arr) == 2) {
                if (View::exists('theme::scope.' . $arr[1])) {
                    $tempView = 'theme::scope.' . $arr[1];
                }
                if (View::exists('theme::scope.' . $arr[0] . '.' . $arr[1])) {
                    $tempView = 'theme::scope.' . $arr[0] . '.' . $arr[1];
                }
            }
        }
        $data['isModal'] = $isModal;
        if ($isModal) {
            $viewModal = 'theme::common.modal.index';
            if (isset($data['viewModal']) && View::exists($data['viewModal'])) {
                $viewModal = $data['viewModal'];
            }
            if (View::exists($viewModal)) {
                $data['formViewInclude'] = $tempView;
                return view($viewModal, $data, $mergeData);
            }
        }
        return view($tempView, $data, $mergeData);
    }
}

if (!function_exists('has_view_scope')) {
    /**
     * Get the evaluated view contents for the given view and Support for theme
     * 
     * @param  string|null  $view
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    function has_view_scope($view = null)
    {
        if ($view && $arr = explode('::', $view)) {

            if (count($arr) == 1) {
                if (View::exists('theme::scope.' . $arr[0]))
                    return true;
            } else if (count($arr) == 2) {
                if (View::exists('theme::scope.' . $arr[0] . '.' . $arr[1]))
                    return true;
                if (View::exists('theme::scope.' . $arr[1]))
                    return true;
            }
        }
        return View::exists($view);
    }
}



if ( ! function_exists('is_enum') ) {
    function is_enum(object $potentialEnum): bool
    {
        return $potentialEnum instanceof StringBackedEnum || $potentialEnum instanceof BackedEnum || $potentialEnum instanceof IntBackedEnum;
    }
}

if ( ! function_exists('pipe') ) {
    function pipe(mixed $passable = null): Pipe
    {
        return $passable ? app(Pipe::class)->send($passable) : app(Pipe::class);
    }
}