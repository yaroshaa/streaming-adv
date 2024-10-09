<?php


namespace App\ClickHouse;


use ArrayAccess;
use Countable;
use Iterator;

class ModelCollection implements ArrayAccess, Iterator, Countable
{
    private array $collection;
    private array $keys = [];
    private int $position = 0;

    public function __construct(array $collection)
    {
        $this->collection = $collection;
    }

    public function add($value, ?string $key = null): void
    {
        $this->collection[] = $value;

        if ($key) {
            $keys = array_keys($this->collection);
            $this->keys[$key][call_user_func([$value, 'get' . ucfirst($key)])] = array_pop($keys);
        }
    }

    public function findBy(string $key, $value)
    {
        if (!array_key_exists($key, $this->keys)) {
            $this->initKeys($key);
        }

        if (!array_key_exists($value, $this->keys[$key])) {
            return null;
        }

        $index = $this->keys[$key][$value];
        return array_key_exists($index, $this->collection) ? $this->collection[$index] : null;
    }

    public function first()
    {
        return count($this->collection) > 0 ? $this->collection[0] : null;
    }

    public function toArray(): array
    {
        return $this->collection;
    }

    private function initKeys(string $key)
    {
        $method = 'get' . ucfirst($key);
        $this->keys[$key] = [];
        foreach ($this->collection as $index => $value) {
            $this->keys[$key][call_user_func([$value, $method])] = $index;
        }
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->collection);
    }

    public function offsetGet($offset)
    {
        return $this->collection[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->collection[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->collection[$offset]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->collection[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        $this->position++;
    }

    public function valid()
    {
        return isset($this->collection[$this->position]);
    }

    public function count()
    {
        return count($this->collection);
    }
}