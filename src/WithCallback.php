<?php

namespace Sokeio\Laravel;

use Illuminate\Support\Traits\Macroable;
trait WithCallback
{
    use Macroable;
    private $__data = [];
    private $__dataCache = [];
    private $__disableCache = false;
    public function DisableCache()
    {
        $this->__disableCache = true;
        return $this;
    }
    public function EnableCache()
    {
        $this->__disableCache = false;
        return $this;
    }
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
    protected function checkKey($__key)
    {
        return  isset($this->__data[$__key]);
    }
    protected function getValue($__key, $__default = null, $withoutCache = false)
    {
        if (!$this->__disableCache && !$withoutCache && isset($this->__dataCache[$__key])) return $this->__dataCache[$__key];
        $valueOrCallback = $this->checkKey($__key) ? $this->__data[$__key] : $__default;
        return ($this->__dataCache[$__key] = ($this->getValueByCallback($valueOrCallback) ?? $__default));
    }
    protected function setKeyValue($__key, $value, $safeKey = false)
    {
        if (!isset($this->__data[$__key]) ||  !$safeKey) {
            $this->__data[$__key] = $value;
            if (isset($this->__dataCache[$__key])) unset($this->__dataCache[$__key]);
        }
        return $this;
    }
}
