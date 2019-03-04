<?php namespace Greenf\Quasar\Modules\Aggregate\Domain;

class ValueObject {

    const TYPE_BOOL = 'bool';
    const TYPE_STRING = 'string';
    const TYPE_DATETIME = 'datetime';
    const TYPE_UUID = 'uuid';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';

    protected $name;

    protected $type;

    protected $nullable;

    protected $shared;

    public function __construct(string $name, string $type, bool $nullable = false, bool $shared = false)
    {
        $this->throwIfNameIsIncorrect($type);
        $this->throwIfTypeIsIncorrect($type);

        $this->name = $name;
        $this->type = $type;
        $this->nullable = $nullable;
        $this->shared = $shared;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function isShared(): bool
    {
        return $this->shared;
    }

    protected function throwIfNameIsIncorrect(string $type)
    {

    }

    protected function throwIfTypeIsIncorrect(string $type)
    {
        if (!in_array($type, (new \ReflectionClass($this))->getConstants())) {
            throw new \InvalidArgumentException(sprintf('Value object type %s is not allowed', $type));
        }
    }
}