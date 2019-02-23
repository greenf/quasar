<?php namespace Greenf\Quasar\Objects;

class DomainException {

    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}