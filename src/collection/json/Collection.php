<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 20-03-18
 * Time: 14:13
 */

namespace edwrodrig\contento\collection\json;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use ArrayIterator;

class Collection implements ArrayAccess, Countable, IteratorAggregate
{
    private $elements;
    private $class;

    protected function __construct(array $data, string $class) {
        $this->class = $class;
        $this->elements = [];

        foreach ( $data as $element ) {
            $element_object = $class::create_from_array($element);
            $this->elements[$element_object->get_id()] = $element_object;
        }
    }

    public function getIterator() {
        return new ArrayIterator($this->elements);
    }

    public function sort() {
        uasort($this->elements, function($a, $b) { return ($this->class)::compare($a, $b); });
    }

    public function reverse_sort() {
        uasort($this->elements, function($a, $b) { return ($this->class)::compare($b, $a); });
    }

    public function count() : int {
        return count($this->elements);
    }

    public function offsetExists( $offset ) : bool {
        return isset($this->elements[$offset]);
    }

    public function offsetGet ( $offset ) {
        return $this->elements[$offset];
    }

    /**
     * @param $offset
     * @param $value
     * @throws \ErrorException
     */
    public function offsetSet ($offset , $value ) {
        throw new \ErrorException('You can\'t set a value');
    }


    /**
     * @param $offset
     * @throws \ErrorException
     */
    public function offsetUnset($offset ) {
        throw new \ErrorException('You can\'t unset a value');
    }

    public static function create_from_array(array $elements, string $class) : self {
        return new self($elements, $class);
    }

    public static function create_from_json(string $filename, string $class) : self {
        $elements = json_decode(file_get_contents($filename), true);

        return self::create_from_array($elements, $class);
    }
}