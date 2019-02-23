<?php namespace Greenf\Quasar\Objects;

class Operation {

    protected $name;

    protected $attributes;

    protected $returnType;

    protected $recordedEvents;

    protected $throwingExceptions;

    protected $isStaic;

    public function __construct(string $name, array $attributes, ReturnType $returnType, bool $isStatic = false)
    {
        $this->throwIfNameIsIncorrect($name);
        //$this->throwIfTypeIsIncorrect($type);

        $this->name = $name;
        $this->attributes = $attributes;
        $this->returnType = $returnType;
        $this->isStatic = $isStatic;
    }

    protected function throwIfNameIsIncorrect(string $type)
    {
        # TODO: validate name
    }

    protected function throwIfTypeIsIncorrect(string $type)
    {
        if (!in_array($type, (new \ReflectionClass($this))->getConstants())) {
            throw new \InvalidArgumentException(sprintf('Value object type %s is not allowed'));
        }
    }
}