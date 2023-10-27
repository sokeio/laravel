<?php

namespace BytePlatform\Laravel;

use Illuminate\Support\Traits\Macroable;

class BaseCallback
{
    use Macroable;
    private $__data = [];
    private $__dataCache = [];
    public function Clear()
    {
        $this->__data = [];
        $this->__dataCache = [];
        return $this;
    }
    public function ClearCache()
    {
        $this->__dataCache = [];
        return $this;
    }
    private $manager;
    public function Manager($manager)
    {
        $this->manager = $manager;
        return $this;
    }
    public function getManager()
    {
        return $this->manager ?? $this;
    }
    protected function getValueByCallback($valueOrCallback)
    {
        if ($valueOrCallback && !is_string($valueOrCallback) && is_callable($valueOrCallback)) {
            return  $valueOrCallback($this, $this->getManager());
        }
        if ($valueOrCallback &&  is_object($valueOrCallback) && method_exists($valueOrCallback, 'Manager')) {
            $valueOrCallback->Manager($this->getManager());
        }
        return $valueOrCallback;
    }
    protected function getValue($__key, $__default = null)
    {
        if (isset($this->__dataCache[$__key])) return $this->__dataCache[$__key];
        $valueOrCallback = isset($this->__data[$__key]) ? $this->__data[$__key] : $__default;
        return ($this->__dataCache[$__key] = ($this->getValueByCallback($valueOrCallback) ?? $__default));
    }
    protected function setKeyValue($__key, $value)
    {
        $this->__data[$__key] = $value;
        return $this;
    }
}
