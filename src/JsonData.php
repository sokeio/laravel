<?php

namespace Sokeio\Laravel;

use Illuminate\Support\Str;

class JsonData implements \ArrayAccess, \JsonSerializable
{
    protected $parent;
    private $data;
    public static function create($data = null, $parent = null): self
    {
        return new self($data, $parent);
    }
    public function CloneData()
    {
        return new self(json_decode(json_encode($this->data ?? []), true), $this->parent);
    }
    public static function getJsonFromFile($path_file)
    {
        return json_decode(file_get_contents($path_file), true);;
    }
    public function loadJsonFromFile($path_file)
    {
        if (file_exists($path_file)) {
            $this->data = self::getJsonFromFile($path_file);
        } else {
            $this->data = [];
        }
    }
    public function __construct($data = null, $parent = null)
    {
        $this->data = $data ?? [];
        $this->parent = $parent;
    }
    public function getData()
    {
        return $this->data;
    }
    public function setData($data)
    {
        return $this->data = $data ?? [];
    }
    /**
     * Get a data by key
     *
     * @param string The key data to retrieve
     * @access public
     */
    public function __get($key)
    {
        if ($this->parent && method_exists($this->parent, 'get' . Str::studly($key) . 'Data')) {
            return $this->parent->{'get' . Str::studly($key) . 'Data'}($this);
        }
        if (method_exists($this, 'get' . Str::studly($key) . 'Data')) {
            return $this->{'get' . Str::studly($key) . 'Data'}();
        }
        return  isset($this->data[$key]) ? $this->data[$key] : null;
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
        if ($this->parent && method_exists($this->parent, 'set' . Str::studly($key) . 'Data')) {
            return $this->parent->{'set' . Str::studly($key) . 'Data'}($this, $value);
        }
        if (method_exists($this, 'set' . Str::studly($key) . 'Data')) {
            return $this->{'set' . Str::studly($key) . 'Data'}($value);
        }
        $this->data[$key] = $value;
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
        if ($this->parent && method_exists($this->parent, 'get' . Str::studly($key) . 'Data')) {
            return true;
        }
        if (method_exists($this, 'get' . Str::studly($key) . 'Data')) {
            return true;
        }
        return isset($this->data[$key]);
    }

    /**
     * Unsets an data by key
     *
     * @param string The key to unset
     * @access public
     */
    public function __unset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Assigns a value to the specified offset
     *
     * @param string The offset to assign the value to
     * @param mixed  The value to set
     * @access public
     */
    public function offsetSet($offset, $value)
    {
        if (is_array($value)) {
            $value2 = new self();
            $value2->setData($value);
            $value = $value2;
        }
        if (is_null($offset)) {
            $this->data[] = $value;
            return;
        }
        if ($this->parent && method_exists($this->parent, 'set' . Str::studly($offset) . 'Data')) {
            $this->parent->{'set' . Str::studly($offset) . 'Data'}($this, $value);
            return;
        }
        if (method_exists($this, 'set' . Str::studly($offset) . 'Data')) {
            $this->{'set' . Str::studly($offset) . 'Data'}($value);
            return;
        }
        $this->data[$offset] = $value;
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
        if ($this->parent && method_exists($this->parent, 'get' . Str::studly($offset) . 'Data')) {
            return true;
        }
        if (method_exists($this, 'get' . Str::studly($offset) . 'Data')) {
            return true;
        }
        return isset($this->data[$offset]);
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
            unset($this->data[$offset]);
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
        if ($this->parent && !is_null($offset) && method_exists($this->parent, 'get' . Str::studly($offset) . 'Data'))
            return $this->parent->{'get' . Str::studly($offset) . 'Data'}($this);
        if (!is_null($offset) && method_exists($this, 'get' . Str::studly($offset) . 'Data'))
            return $this->{'get' . Str::studly($offset) . 'Data'}();
        return $this->offsetExists($offset) ? $this->data[$offset] : null;
    }
    public function __toString()
    {
        return json_encode($this->data);
    }
    public function getValue($key, $default = '')
    {
        return self::getValueByKey($this, $key, $default);
    }

    public function jsonSerialize()
    {
        return $this->data;
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
