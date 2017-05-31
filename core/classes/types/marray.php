<?php

/**
 * essa classe encapsula o comportamento de um array e serve para a implementação propriedades
 * como coleções na camada de persistência.
 */
class MArray extends MType
    implements \Iterator, \ArrayAccess, \Countable  {
    private $internal;

    public function __construct($internal) {
        $this->internal = $this->buildArray($internal);
    }

    private function buildArray($value) {
        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            return get_object_vars($value);
        }

        if (is_scalar($value) || is_null($value)) {
            return [ $value ];
        }

        throw new \InvalidArgumentException("Não pode converter o valor para um array.");
    }

    public function getValue() {
        return $this->internal;
    }

    #region ==ArrayAcess==

    public function offsetExists($offset) {
        return isset($this->internal[$offset]);
    }

    public function offsetGet($offset) {
        return $this->internal[$offset];
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->internal[] = $value;
        } else {
            $this->internal[$offset] = $value;
        }
    }

    public function offsetUnset($offset) {
        unset($this->internal[$offset]);
    }

    #endregion

    #region  == Iterator ==

    public function current() {
        return current($this->internal);
    }


    public function next() {
        return next($this->internal);
    }

    public function key() {
        return key($this->internal);
    }

    public function valid() {
        return $this->key() !== null;
    }

    public function rewind() {
        return reset($this->internal);
    }
    #endregion

    public function count() {
        return count($this->internal);
    }
}