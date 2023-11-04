<?php

namespace BytePlatform\Laravel;

use Illuminate\Support\Str;

class JsonData implements \ArrayAccess, \JsonSerializable
{
    protected $__parent;
    private $__data;
    public static function Create($data = null, $parent = null): self
    {
        return new self($data, $parent);
    }
    public function CloneData()
    {
        return new self(json_decode(json_encode($this->__data ?? []), true), $this->__parent);
    }
    public static function getJsonFromFile($path_file)
    {
        return json_decode(file_get_contents($path_file), true);;
    }
    public function loadJsonFromFile($path_file)
    {
        if (file_exists($path_file))
            $this->__data = self::getJsonFromFile($path_file);
        else   $this->__data = [];
    }
    public function __construct($data = null, $parent = null)
    {
        $this->__data = $data ?? [];
        $this->__parent = $parent;
    }
    public function getData()
    {
        return $this->__data;
    }
    public function setData($data)
    {
        return $this->__data = $data ?? [];
    }
    /**
     * Get a data by key
     *
     * @param string The key data to retrieve
     * @access public
     */
    public function __get($key)
    {
        if ($this->__parent && method_exists($this->__parent, 'get' . Str::studly($key) . 'Data'))
            return $this->__parent->{'get' . Str::studly($key) . 'Data'}($this);
        if (method_exists($this, 'get' . Str::studly($key) . 'Data'))
            return $this->{'get' . Str::studly($key) . 'Data'}();
        return  isset($this->__data[$key]) ? $this->__data[$key] : null;
    }

    /**
     * Assigns a value to the specified data
     * 
     * @param string The data key to assign the value to
     * @param mixed  The value to set
     * @access public 
     */
    public function __set($key, $value)
    {
        if (is_array($value)) {
            $value2 = new self();
            $value2->setData($value);
            $value = $value2;
        }
        if ($this->__parent && method_exists($this->__parent, 'set' . Str::studly($key) . 'Data'))
            return $this->__parent->{'set' . Str::studly($key) . 'Data'}($this, $value);
        if (method_exists($this, 'set' . Str::studly($key) . 'Data'))
            return $this->{'set' . Str::studly($key) . 'Data'}($value);
        $this->__data[$key] = $value;
    }

    /**
     * Whether or not an data exists by key
     *
     * @param string An data key to check for
     * @access public
     * @return boolean
     * @abstracting ArrayAccess
     */
    public function __isset($key)
    {
        if ($this->__parent && method_exists($this->__parent, 'get' . Str::studly($key) . 'Data'))
            return true;
        if (method_exists($this, 'get' . Str::studly($key) . 'Data'))
            return true;
        return isset($this->__data[$key]);
    }

    /**
     * Unsets an data by key
     *
     * @param string The key to unset
     * @access public
     */
    public function __unset($key)
    {
        unset($this->__data[$key]);
    }

    /**
     * Assigns a value to the specified offset
     *
     * @param string The offset to assign the value to
     * @param mixed  The value to set
     * @access public
     * @abstracting ArrayAccess
     */
    public function offsetSet($offset,  $value)
    {
        if (is_array($value)) {
            $value2 = new self();
            $value2->setData($value);
            $value = $value2;
        }
        if (is_null($offset)) {
            $this->__data[] = $value;
            return;
        }
        if ($this->__parent && method_exists($this->__parent, 'set' . Str::studly($offset) . 'Data'))
            return $this->__parent->{'set' . Str::studly($offset) . 'Data'}($this, $value);
        if (method_exists($this, 'set' . Str::studly($offset) . 'Data'))
            return $this->{'set' . Str::studly($offset) . 'Data'}($value);
        $this->__data[$offset] = $value;
    }

    /**
     * Whether or not an offset exists
     *
     * @param string An offset to check for
     * @access public
     * @return boolean
     */
    public function offsetExists($offset)
    {
        if ($this->__parent && method_exists($this->__parent, 'get' . Str::studly($offset) . 'Data'))
            return true;
        if (method_exists($this, 'get' . Str::studly($offset) . 'Data'))
            return true;
        return isset($this->__data[$offset]);
    }

    /**
     * Unsets an offset
     *
     * @param string The offset to unset
     * @access public
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->__data[$offset]);
        }
    }
    /**
     * Returns the value at specified offset
     *
     * @param string The offset to retrieve
     * @access public
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if ($this->__parent && !is_null($offset) && method_exists($this->__parent, 'get' . Str::studly($offset) . 'Data'))
            return $this->__parent->{'get' . Str::studly($offset) . 'Data'}($this);
        if (!is_null($offset) && method_exists($this, 'get' . Str::studly($offset) . 'Data'))
            return $this->{'get' . Str::studly($offset) . 'Data'}();
        return $this->offsetExists($offset) ? $this->__data[$offset] : null;
    }
    public function __toString()
    {
        return json_encode($this->__data);
    }
    public function getValue($key, $default = '')
    {
        return self::getValueByKey($this, $key, $default);
    }

    public function jsonSerialize()
    {
        return $this->__data;
    }
    public static function getValueByKey($data, $key, $default = '')
    {
        if ($data && $key) {
            $arrkey = explode('.', $key);
            $dataTemp = $data;
            if (is_array($dataTemp) || is_a($dataTemp, self::class)) {
                foreach ($arrkey as $keyItem) {
                    if (isset($dataTemp[$keyItem])) {
                        $dataTemp = $dataTemp[$keyItem];
                    } else {
                        return $default;
                    }
                }
            } else {
                foreach ($arrkey as $keyItem) {
                    if (isset($dataTemp->{$keyItem})) {
                        $dataTemp = $dataTemp->{$keyItem};
                    } else {
                        return $default;
                    }
                }
            }


            return $dataTemp;
        }

        return $default;
    }
}
