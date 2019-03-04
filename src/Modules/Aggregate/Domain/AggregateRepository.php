<?php namespace Greenf\Quasar\Modules\Aggregate\Domain;

interface AggregateRepository {

    public function save(Aggregate $aggregate): void;

    public function get(string $contextName, string $moduleName, string $name): Aggregate;

}