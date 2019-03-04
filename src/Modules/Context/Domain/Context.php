<?php namespace Greenf\Quasar\Modules\Context\Domain;

use Greenf\Quasar\Validators\NameValidator;

class Context {

    private $name;

    private $modules;

    private function __construct(string $name, array $modules)
    {
        if (!NameValidator::isPascalCase($name)) {
            throw new \InvalidArgumentException('Context name has to be declared in PascalCase');
        }

        $this->name = $name;
        $this->modules = $modules;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return Module[]
     */
    public function modules(): array
    {
        return $this->modules;
    }

    public static function create(string $name): Context
    {
        return new self($name, []);
    }

    public function makeModule(string $name)
    {
        if (!NameValidator::isPascalCase($name)) {
            throw new \InvalidArgumentException('Module name has to be declared in PascalCase');
        }

        $this->throwIfModuleAlreadyExists($name);

        $this->modules[] = $this->moduleCreator($name);
    }

    private function throwIfModuleAlreadyExists(string $name): void
    {
        foreach ($this->modules as $module) {
            if ($module->name() == $name) {
                throw new \InvalidArgumentException('Module with name %s already exists');
            }
        }
    }

    /**
     * @param string $name
     *
     * @return Module
     */
    private function moduleCreator(string $name)
    {
        $class = new \ReflectionClass(Module::class);
        $constructor = $class->getConstructor();
        $constructor->setAccessible(true);
        $object = $class->newInstanceWithoutConstructor();
        $constructor->invoke($object, $this->name . '.' . $name, $name);

        return $object;
    }
}