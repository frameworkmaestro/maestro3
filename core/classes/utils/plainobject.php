<?php

class PlainObject
{
    public function __construct($attributes)
    {
        if (is_array($attributes)) {
            foreach($attributes as $a => $v) {
                $this->$a = $v;
            }
        }
    }

    static function fromData($data) {
        return new \PlainObject((array) $data);
    }

}
