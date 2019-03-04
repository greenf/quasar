<?php namespace Greenf\Quasar\Modules\Aggregate\Application;

use Greenf\Quasar\Modules\Aggregate\Domain\Aggregate;
use Greenf\Quasar\Modules\Aggregate\Domain\Shared;
use Greenf\Quasar\Modules\Aggregate\Domain\ValueObject;

class AggregateBuilder {

    private $contextName;
    private $moduleName;
    private $name;
    private $identityName;
    private $identityType = 'string';
    private $isIdentityShared = false;

    public function inContext(string $contextName): AggregateBuilder
    {
        $this->contextName = $contextName;

        return $this;
    }

    public function inModule(string $moduleName): AggregateBuilder
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    public function withName(string $name): AggregateBuilder
    {
        $this->name = $name;

        return $this;
    }

    public function withCustomIdentity(string $name = null, string $type = 'string', bool $isShared = false): AggregateBuilder
    {
        $this->identityName = $name;
        $this->identityType = $type;
        $this->isIdentityShared = $isShared;

        return $this;
    }

    public function contextName(): string
    {
        return $this->contextName;
    }

    public function moduleName(): string
    {
        return $this->moduleName;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function identity(): ValueObject
    {
        return new ValueObject($this->identityName(), $this->identityType, false, $this->isIdentityShared);
    }

    private function identityName()
    {
        return $this->identityName ?: $this->name . 'Id';
    }

    public function build(): Aggregate
    {
        return Aggregate::create($this);
    }
}