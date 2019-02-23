<?php namespace Greenf\Quasar\Modules\Context\Infrastructure;

use GraphAware\Common\Result\Result;
use GraphAware\Common\Type\Node;
use Greenf\Quasar\Infrastructure\Neo4j\Neo4jRepository;
use Greenf\Quasar\Modules\Context\Domain\Context;
use Greenf\Quasar\Modules\Context\Domain\ContextRepository;
use Greenf\Quasar\Modules\Context\Domain\Module;

class ContextNeo4JRepository extends Neo4jRepository implements ContextRepository {

    public function save(Context $context): void
    {
        $this->saveContext($context->name());

        foreach ($context->modules() as $module) {
            $this->saveModule($context->name(), $module->name());
        }
    }

    public function get(string $name)
    {
        $contextData = $this->getContextData($name);

        if (!$this->contextExists($contextData, $name)) {
            throw new \DomainException('Context does not exists.');
        }

        return $this->createProtectedObject(Context::class, $name, $this->getModules($contextData, $name));
    }

    private function saveContext(string $name)
    {
        $this->exec(
            sprintf(
                'MERGE (:Context {name:"%s"})',
                $name
            )
        );
    }

    public function saveModule(string $contextName, string $moduleName)
    {
        $this->exec(
            sprintf(
                'MATCH (c:Context {name:"%s"})
				MERGE (m:Module {name:"%s"})
				CREATE UNIQUE (m)-[:IN_CONTEXT]->(c)',
                $contextName,
                $moduleName
            )
        );
    }

    public function getContextData(string $name)
    {
        return $this->exec(
            sprintf(
                'MATCH (c:Context {name:"%s"})
                OPTIONAL MATCH (m:Module)-[:IN_CONTEXT]->(c)
                RETURN m, c',
                $name
            )
        );
    }

    private function contextExists(Result $contextData, $name): bool
    {
        foreach ($contextData->records() as $record) {
            foreach($record->values() as $value) {
                if ($value instanceof Node && $value->hasLabel('Context') ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param Result $contextData
     * @param string $contextName
     *
     * @return Module[]
     */
    private function getModules(Result $contextData, string $contextName): array
    {
        $modules = [];

        foreach ($contextData->records() as $record) {
            foreach($record->values() as $value) {
                if ($value instanceof Node && $value->hasLabel('Module')) {
                    $modules[] = $this->createProtectedObject(Module::class, md5($contextName . $value->get('name')), $value->get('name'));
                }
            }
        }

        return $modules;
    }
}