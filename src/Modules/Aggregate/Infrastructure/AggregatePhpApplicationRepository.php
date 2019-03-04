<?php namespace Greenf\Quasar\Modules\Aggregate\Infrastructure;

use Greenf\Quasar\Infrastructure\PhpApplication\PhpApplicationRepository;
use Greenf\Quasar\Modules\Aggregate\Domain\Aggregate;
use Greenf\Quasar\Modules\Aggregate\Domain\AggregateRepository;
use Greenf\Quasar\Modules\Aggregate\Domain\ValueObject;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use PhpValueObjects\AbstractValueObject;
use PhpValueObjects\NullableInterface;
use ReflectionClass;

class AggregatePhpApplicationRepository extends PhpApplicationRepository implements AggregateRepository {

    public function save(Aggregate $aggregate): void
    {
        $this->createAggregate($aggregate);
    }

    public function get(string $contextName, string $moduleName, string $name): Aggregate
    {
        $dir = $this->aggregateDir($contextName, $moduleName, $name);

        if (!$this->classExists($dir, $name)) {
            throw new \DomainException('Aggregate does not exists.');
        }

        $aggregateReflect = new ReflectionClass($this->namespaceFromDir($dir) . '\\Base\\' . $name);
        $identity = $this->getIdentity($aggregateReflect);

        $properties = $this->getProperties($aggregateReflect);

        # TODO
        $operations = [];

        return $this->createProtectedObject(
            Aggregate::class,
            $contextName,
            $moduleName,
            $name,
            $identity,
            $properties,
            $operations
        );
    }

    private function getIdentity(ReflectionClass $aggregate) {
        foreach ($aggregate->getMethods() as $method) {
            if (preg_match('/@identity/', $method->getDocComment())) {
                $name = end(explode('\\', $method->getReturnType()));

                $valueObjectReflect = new ReflectionClass($method->getReturnType()->getName());

                $type = $this->getValueObjectType($valueObjectReflect->getParentClass()->getName());

                $nullable = $valueObjectReflect->implementsInterface(NullableInterface::class);

                $shared = preg_match(
                    sprintf(
                        '/^%s/',
                        str_replace('\\', '\\\\', $this->namespaceFromDir($this->sharedDir()))
                    ),
                    $method->getReturnType()->getName()
                );

                return new ValueObject($name, $type, $nullable, $shared);
            }
        }

        throw new \Exception('Identity not found');
    }

    private function getProperties(ReflectionClass $aggregate): array
    {
        $properties = [];

        foreach ($aggregate->getProperties() as $property) {
            if (!preg_match('/@var\ ([A-Za-z0-9\\\\\_]+)/', $property->getDocComment(), $data)) {
                continue;
            }

            if (!class_exists($data[1])) {
                continue;
            }

            $valueObjectReflect = new ReflectionClass($data[1]);

            if (!$valueObjectReflect->isSubclassOf(AbstractValueObject::class)) {
                continue;
            }

            $name = end(explode('\\', $data[1]));

            $type = $this->getValueObjectType($valueObjectReflect->getParentClass()->getName());

            $nullable = $valueObjectReflect->implementsInterface(NullableInterface::class);

            $shared = preg_match(
                sprintf(
                    '/^\\\\%s/',
                    str_replace('\\', '\\\\', $this->namespaceFromDir($this->sharedDir()))
                ),
                $data[1]
            );

            $properties[] = new ValueObject($name, $type, $nullable, $shared);
        }

        return $properties;
    }

    private function createAggregate($aggregate)
    {
        $this->createBaseAggregateRoot($aggregate);
        $this->createAggregateRoot($aggregate);
        $this->createValueObjects($aggregate);
    }

    private function createValueObjects(Aggregate $aggregate)
    {
        $this->createIdentity($aggregate, $aggregate->identity());

        foreach ($aggregate->properties() as $property) {
            $this->createValueObject($aggregate, $property);
        }
    }

    private function createIdentity(Aggregate $aggregate, ValueObject $identity)
    {
        if ($identity->isShared()) {
            $dir = $this->sharedDir();
        } else {
            $dir = $this->aggregateDir($aggregate->contextName(), $aggregate->moduleName(), $aggregate->name());
        }

        if ($this->classExists($dir, $identity->name())) {
            return;
        }

        if (!$this->directoryExists($dir)) {
            $this->createDirectory($dir);
        }

        $namespace = new PhpNamespace($this->namespaceFromDir($dir));
        $class = $namespace->addClass($identity->name());
        $class->addExtend($this->getValueObjectExtend($identity->type()));
        $class->addImplement($this->identityInterface());
        $class->setFinal();

        $this->saveFile($dir, $identity->name(), (string) $namespace);
    }

    private function createValueObject(Aggregate $aggregate, ValueObject $valueObject)
    {
        if ($valueObject->isShared()) {
            $dir = $this->sharedDir();
        } else {
            $dir = $this->aggregateDir($aggregate->contextName(), $aggregate->moduleName(), $aggregate->name());
        }

        if ($this->classExists($dir, $valueObject->name())) {
            dump(sprintf('Class %s already exists', $valueObject->name()));

            return;
        }

        if (!$this->directoryExists($dir)) {
            $this->createDirectory($dir);
        }

        $namespace = new PhpNamespace($this->namespaceFromDir($dir));
        $class = $namespace->addClass($valueObject->name());

        $class->addExtend($this->getValueObjectExtend($valueObject->type()));

        if ($valueObject->isNullable()) {
            $class->addImplement(NullableInterface::class);
        }

        $this->saveFile($dir, $valueObject->name(), (string) $namespace);
    }

    private function getAggregateDirectory(Aggregate $aggregate)
    {
        return $this->aggregateDir($aggregate->contextName(), $aggregate->moduleName());
    }

    private function getSharedKernelAggregateDirectory()
    {
        return $this->sharedDir();
    }

    private function getValueObjectDirectory(Aggregate $aggregate, ValueObject $valueObject)
    {
        return ($valueObject->isShared())
            ? $this->getSharedKernelAggregateDirectory()
            : $this->getAggregateDirectory($aggregate);
    }

    private function getValueObjectNamespace(Aggregate $aggregate, ValueObject $valueObject)
    {
        return implode('\\', explode(DIRECTORY_SEPARATOR, $this->getValueObjectDirectory($aggregate, $valueObject)));
    }

    private function createBaseAggregateRoot(Aggregate $aggregate)
    {
        $dir = $this->getAggregateDirectory($aggregate) . '/Base';

        if (!$this->directoryExists($dir)) {
            $this->createDirectory($dir);
        }

        $namespace = new PhpNamespace($this->namespaceFromDir($dir));

        $class = $namespace->addClass($aggregate->name());
        $class->addExtend($this->aggregateAbstract());
        $class->setAbstract();

        $this->addProperties($class, $aggregate);
        $this->addGetters($class, $aggregate);

        $this->saveFile($dir, $aggregate->name(), (string) $namespace);
    }

    private function createAggregateRoot(Aggregate $aggregate)
    {
        $dir = $this->getAggregateDirectory($aggregate);

        $baseClass = $this->namespaceFromDir($dir . '/Base', $aggregate->name());

        $namespace = new PhpNamespace($this->namespaceFromDir($dir));

        if ($this->classExists($dir, $aggregate->name())) {
            dump('Aggregate already exists.');

            return;
        }

        $class = $namespace->addClass($aggregate->name());
        $class->addExtend($baseClass);

        $this->saveFile($dir, $aggregate->name(), (string) $namespace);
    }

    /**
     * @param ClassType     $class
     * @param ValueObject[] $properties
     *
     */
    private function addProperties(ClassType $class, Aggregate $aggregate): void
    {
        foreach ($aggregate->properties() as $property) {
            $class->addProperty($this->camelCase($property->name()))
                ->setVisibility('protected')
                ->addComment('@var \\' . $this->getValueObjectNamespace($aggregate, $property) . '\\' . $property->name());
        }
    }

    /**
     * @param ClassType     $class
     * @param Aggregate $aggregate
     *
     */
    private function addGetters(ClassType $class, Aggregate $aggregate): void
    {
        $class->addMethod($this->camelCase($aggregate->identity()->name()))
            ->setFinal()
            ->setVisibility('public')
            ->addComment('@identity')
            ->addComment('@return \\' . $this->getValueObjectNamespace($aggregate, $aggregate->identity()) . '\\' . $aggregate->identity()->name())
            ->setReturnType($this->getValueObjectNamespace($aggregate, $aggregate->identity()) . '\\' . $aggregate->identity()->name())
            ->setBody('return $this->identity;');

        foreach ($aggregate->properties() as $property) {
            $class->addMethod($this->camelCase($property->name()))
                ->setFinal()
                ->setVisibility('public')
                ->addComment('@return \\' . $this->getValueObjectNamespace($aggregate, $property) . '\\' . $property->name())
                ->setReturnType($this->getValueObjectNamespace($aggregate, $property) . '\\' . $property->name())
                ->setBody(sprintf('return $this->%s;', $this->camelCase($property->name())));
        }
    }

}