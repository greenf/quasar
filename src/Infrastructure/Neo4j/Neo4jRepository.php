<?php namespace Greenf\Quasar\Infrastructure\Neo4j;

use GraphAware\Neo4j\Client\ClientBuilder;
use Greenf\Quasar\Infrastructure\Repository;

abstract class Neo4jRepository extends Repository {

    private $neoClient;

    public function __construct(string $bolt)
    {
        $this->neoClient = ClientBuilder::create()
        ->addConnection('bolt', $bolt)
        ->build();
    }

    protected function exec(string $query)
    {
        return $this->neoClient->run($query);
    }

}
