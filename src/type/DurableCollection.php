<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 22-03-18
 * Time: 7:35
 */

namespace edwrodrig\contento\type;

use ArrayIterator;

trait DurableCollection
{
    /**
     * @var Durable[]
     */
    protected $elements;


    public function getIterator() {
        return new ArrayIterator($this->elements);
    }

    public function sort() {
        usort($this->elements, function($a, $b) { $a::compare($a, $b); });
    }

    public function get_active_elements($now = null) {
        $copy = clone $this;
        $copy->elements = [];

        foreach ( $this->elements as $element ) {
            if ( $element->get_duration()->is_active($now) ) {
                $copy->elements[] = $element;
            }
        }
        return $copy;
    }

    public function get_finished_elements($now = null) {
        $copy = clone $this;
        $copy->elements = [];

        foreach ( $this->elements as $element ) {
            if ( $element->get_duration()->has_finished($now) ) {
                $copy->elements[] = $element;
            }
        }
        return $copy;
    }

    public function offsetExists($offset) : bool {
        $count = count($this->elements);
        if ( $offset < 0 )
            return isset($this->elements[$count + $offset]);
        else
            return isset($this->elements[$offset]);
    }

    public function offsetGet($offset) {
        $count = count($this->elements);
        if ( $offset < 0 )
            return $this->elements[$count + $offset];
        else
            return $this->elements[$offset];
    }

    public function offsetSet($offset, $value) {}

    public function offsetUnset($offset) {}

    public function count() : int {
        return count($this->elements);
    }
}