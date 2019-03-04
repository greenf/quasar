<?php namespace Greenf\Quasar\Modules\Aggregate\Domain;

class Shared {

    private $name;

    private $identity;

    private $properties;

    private function __construct(
        string $name,
        ValueObject $identity,
        array $properties = []
    )
    {
        $this->name = $name;
        $this->identity = $identity;
        $this->properties = $properties;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function identity(): ValueObject
    {
        return $this->identity;
    }

    public function properties(): array
    {
        return $this->properties;
    }


}