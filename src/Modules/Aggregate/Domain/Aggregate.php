<?php namespace Greenf\Quasar\Modules\Aggregate\Domain;

use Greenf\Quasar\Modules\Aggregate\Application\AggregateBuilder;

class Aggregate {

    private $contextName;

    private $moduleName;

    private $name;

    private $identity;

    private $properties;

    private $operations;

    private function __construct(
        string $contextName,
        string $moduleName,
        string $name,
        ValueObject $identity,
        array $properties = [],
        array $operations = []
    )
    {
        $this->contextName = $contextName;
        $this->moduleName = $moduleName;
        $this->name = $name;
        $this->identity = $identity;
        $this->properties = $properties;
        $this->operations = $operations;
    }

    public static function create(AggregateBuilder $builder)
    {
        return new self($builder->contextName(), $builder->moduleName(), $builder->name(), $builder->identity());
    }

    public function addProperty(string $name, string $type, bool $nullable, bool $shared)
    {
        foreach ($this->properties as $property) {
            if ($property->name() == $name) {
                throw new \DomainException(sprintf('Property with name %s already exists', $name));
            }
        }

        $this->properties[] = new ValueObject($name, $type, $nullable, $shared);
    }

    //public function addOperation(string $name, array $attributes, ReturnType $returnType, bool $isStatic = false)
    //{
    //    $this->operations[] = new Operation($name, $attributes, $returnType, $isStatic);
    //}

    public function name(): string
    {
        return $this->name;
    }

    public function identity(): ValueObject
    {
        return $this->identity;
    }

    /**
     * @return ValueObject[]
     */
    public function properties(): array
    {
        return $this->properties;
    }

    public function contextName(): string
    {
        return $this->contextName;
    }

    public function moduleName(): string
    {
        return $this->moduleName;
    }
}