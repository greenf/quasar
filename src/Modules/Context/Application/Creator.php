<?php namespace Greenf\Quasar\Modules\Context\Application;

use Greenf\Quasar\Modules\Context\Domain\Context;
use Greenf\Quasar\Modules\Context\Domain\ContextRepository;

class Creator {

    private $repository;

    public function __construct(ContextRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(string $name)
    {
        $context = Context::create($name);

        $this->repository->save($context);
    }

    public function makeModule(string $contextName, string $moduleName)
    {
        $context = $this->repository->get($contextName);

        $context->makeModule($moduleName);

        $this->repository->save($context);
    }
}