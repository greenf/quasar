<?php namespace Greenf\Quasar\Modules\Aggregate\Application;

use Greenf\Quasar\Modules\Aggregate\Domain\Aggregate;
use Greenf\Quasar\Modules\Aggregate\Domain\AggregateRepository;
use Greenf\Quasar\Modules\Aggregate\Domain\ValueObject;

class Manager {

    private $repository;

    public function __construct(AggregateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(Aggregate $aggregate)
    {
        $this->repository->save($aggregate);
    }

    public function addProperty(
        string $contextName,
        string $moduleName,
        string $aggregateName,
        string $name,
        string $type,
        bool $nullable = false,
        bool $shared = false
    )
    {
        //string $name, string $type, bool $nullable, bool $shared)
        $aggregate = $this->repository->get($contextName, $moduleName, $aggregateName);

        $aggregate->addProperty($name, $type, $nullable, $shared);

        $this->repository->save($aggregate);
    }

}