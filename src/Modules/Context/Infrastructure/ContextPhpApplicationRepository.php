<?php namespace Greenf\Quasar\Modules\Context\Infrastructure;

use Greenf\Quasar\Infrastructure\PhpApplication\PhpApplicationRepository;
use Greenf\Quasar\Modules\Context\Domain\Context;
use Greenf\Quasar\Modules\Context\Domain\ContextRepository;
use Greenf\Quasar\Modules\Context\Domain\Module;

class ContextPhpApplicationRepository extends PhpApplicationRepository implements ContextRepository {

    public function save(Context $context): void
    {
        $this->createDirectory($this->contextDir($context->name()));

        foreach ($context->modules() as $module) {
            $this->createDirectory($this->moduleDir($context->name(), $module->name()));

            //$this->createDirectory($dir . DIRECTORY_SEPARATOR . $module->name() . DIRECTORY_SEPARATOR . 'Application');
            //$this->createDirectory($dir . DIRECTORY_SEPARATOR . $module->name() . DIRECTORY_SEPARATOR . 'Domain');
            //$this->createDirectory($dir . DIRECTORY_SEPARATOR . $module->name() . DIRECTORY_SEPARATOR . 'Infrastructure');
        }
    }

    public function get(string $name)
    {
        if (!$this->directoryExists($this->contextDir($name))) {
            throw new \DomainException('Context does not exists.');
        }

        return $this->createProtectedObject(Context::class, $name, $this->getModules($name));
    }

    private function getModules(string $contextName): array
    {
        $modules = [];

        $dir = $this->contextDir($contextName);

        foreach (scandir($dir) as $el) {
            if ($this->isModule($dir, $el)) {
                $modules[] = $this->createProtectedObject(Module::class, $contextName . '.' . $el, $el);
            }
        }

        return $modules;
    }

    private function isModule(string $dir, string $name)
    {
        if (in_array($name, ['.', '..'])) {
            return false;
        }

        if (is_file($dir . DIRECTORY_SEPARATOR . $name)) {
            return false;
        }

        return true;
    }
}