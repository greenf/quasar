<?php namespace Greenf\Quasar\Objects;

class Identity extends ValueObject {

    public function __construct(string $name, string $type)
    {
        return parent::__construct($name, $type, true);
    }
}