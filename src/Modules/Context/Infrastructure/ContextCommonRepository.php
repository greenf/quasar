<?php namespace Greenf\Quasar\Modules\Context\Infrastructure;

use Greenf\Quasar\Infrastructure\CommonRepository;
use Greenf\Quasar\Modules\Context\Domain\Context;
use Greenf\Quasar\Modules\Context\Domain\ContextRepository;

class ContextCommonRepository extends CommonRepository implements ContextRepository {

    public function save(Context $context): void
    {
        $this->phpApplicationRepository(ContextPhpApplicationRepository::class)->save($context);
        $this->neo4jRepository(ContextNeo4JRepository::class)->save($context);
    }

    public function get(string $name)
    {
        return $this->neo4jRepository(ContextNeo4JRepository::class)->get($name);
    }
}