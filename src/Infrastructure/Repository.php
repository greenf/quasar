<?php namespace Greenf\Quasar\Infrastructure;

abstract class Repository {

    protected function createProtectedObject(string $className, ...$attributes)
    {
        $class = new \ReflectionClass($className);
        $constructor = $class->getConstructor();
        $constructor->setAccessible(true);
        $object = $class->newInstanceWithoutConstructor();
        $constructor->invoke($object, ...$attributes);

        return $object;
    }
}