<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 20-03-18
 * Time: 14:13
 */

namespace edwrodrig\contento\collection;

use Countable;
use edwrodrig\contento\type\DefaultElement;
use edwrodrig\contento\type\Element;
use IteratorAggregate;
use ArrayIterator;

/**
 * Class Collection
 * Represents a collection of elements from json elements
 * @package edwrodrig\contento\collection\json
 */
class Collection implements Countable, IteratorAggregate
{
    /**
     * @var array
     */
    private $elements;

    /**
     * @var
     */
    private $class_name;

    protected function __construct() {
        $this->elements = [];
    }

    protected function fromArray(array $data, string $class_name) {
        $this->class_name = $class_name;

        foreach ( $data as $element ) {

            $element_object = $class_name::createFromArray($element);
            $this->elements[$element_object->getId()] = $element_object;
        }
    }

    public function getIterator() {
        return new ArrayIterator($this->elements);
    }

    /**
     * Sort the elements in ascendent order.
     *
     * first < last
     * @see Collection::reverseSort() to order in reverse order
     */
    public function sort() : Collection {
        if ( method_exists($this->class_name, 'compare') )
            uasort($this->elements, function($a, $b) { return ($this->class_name)::compare($a, $b); });
        else
            ksort($this->elements);

        return $this;
    }

    /**
     * Sort the elements in descendent order.
     *
     * first > last
     * @see Collection::reverseSort() to order in normal order
     */
    public function reverseSort() : Collection {
        if ( method_exists($this->class_name, 'compare') )
            uasort($this->elements, function($a, $b) { return ($this->class_name)::compare($b, $a); });
        else
            krsort($this->elements);

        return $this;
    }

    /**
     * The number of elements
     * @return int
     */
    public function count() : int {
        return count($this->elements);
    }

    /**
     * Get an element by key
     *
     * The key coincide with the element {@see Element::getId() id}
     * @param string $offset
     * @return Element
     */
    public function getElement(string $offset) {
        return $this->elements[$offset];
    }

    /**
     * @param Element[] $elements
     * @return Collection
     */
    public static function createFromElements(array $elements) : self {
        $r = new self;
        $last_element = null;
        foreach ( $elements as $element ) {
            $r->elements[$element->getId()] = $element;
            $last_element = $element;
        }

        if ( !is_null($last_element) ) {
            $r->class_name = get_class($last_element);
        }

        return $r;
    }

    public static function createFromArray(array $elements, string $class = DefaultElement::class) : self {
        $r = new self;
        $r->fromArray($elements, $class);
        return $r;
    }

    public static function createFromJson(string $filename, string $class = DefaultElement::class) : self {
        $elements = json_decode(file_get_contents($filename), true);

        return self::createFromArray($elements, $class);
    }
}