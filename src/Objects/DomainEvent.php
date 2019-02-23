<?php namespace Greenf\Quasar\Objects;

class DomainEvent {

    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}