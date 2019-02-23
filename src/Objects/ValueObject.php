<?php namespace Greenf\Quasar\Objects;

class ValueObject {

    const TYPE_STRING = 'string';
    const TYPE_UUID = 'uuid';
    const TYPE_NUMERIC = 'numeric';

    protected $name;

    protected $type;

    protected $isShared;

    public function __construct(string $name, string $type, bool $isShared = false)
    {
        $this->throwIfNameIsIncorrect($type);
        $this->throwIfTypeIsIncorrect($type);

        $this->name = $name;
        $this->type = $type;
        $this->isShared = $isShared;
    }

    protected function throwIfNameIsIncorrect(string $type)
    {

    }

    protected function throwIfTypeIsIncorrect(string $type)
    {
        if (!in_array($type, (new \ReflectionClass($this))->getConstants())) {
            throw new \InvalidArgumentException(sprintf('Value object type %s is not allowed'));
        }
    }
}