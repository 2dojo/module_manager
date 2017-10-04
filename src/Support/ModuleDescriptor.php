<?php

namespace TwoDojo\ModuleManager\Support;

class ModuleDescriptor implements \ArrayAccess
{
    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    public function hasAttribute($key)
    {
        return array_has($this->attributes, $key);
    }

    public function getAttribute($key, $default = null)
    {
        return array_get($this->attributes, $key, $default);
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttribute($key, $value)
    {
        $this->attributes = array_set($this->attributes, $key, $value);
    }

    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    public function __get($name)
    {
        return $this->getAttribute($name);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return !is_null($this->getAttribute($offset));
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }
}
