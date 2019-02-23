<?php namespace Greenf\Quasar\Objects;

class Aggregate {

    private $moduleId;

    private $name;

    private $identity;

    private $properties;

    private $operations;

    public function __construct(
        string $moduleId,
        string $name,
        string $identity,
        array $properties = [],
        array $operations = []
    )
    {
        $this->moduleId = $moduleId;
        $this->name = $name;
        $this->identity = $identity;
        $this->properties = $properties;
        $this->operations = $operations;
    }

    public static function create(string $moduleId, string $name, Identity $identity)
    {
        # TODO: Validation

        return new self($moduleId, $name, $identity, [], []);
    }

    public function addProperty(string $name, string $type)
    {
        $this->properties[] = new ValueObject($name, $type, false);
    }

    public function addOperation(string $name, array $attributes, ReturnType $returnType, bool $isStatic = false)
    {
        $this->operations[] = new Operation($name, $attributes, $returnType, $isStatic);
    }
}