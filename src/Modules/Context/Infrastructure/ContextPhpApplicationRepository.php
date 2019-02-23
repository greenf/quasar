<?php namespace Greenf\Quasar\Modules\Context\Infrastructure;

use Greenf\Quasar\Infrastructure\PhpApplication\PhpApplicationRepository;
use Greenf\Quasar\Modules\Context\Domain\Context;
use Greenf\Quasar\Modules\Context\Domain\ContextRepository;
use Greenf\Quasar\Modules\Context\Domain\Module;

class ContextPhpApplicationRepository extends PhpApplicationRepository implements ContextRepository {

    public function save(Context $context): void
    {
        $dir = $this->baseNamespace . DIRECTORY_SEPARATOR . $context->name();

        $this->createDirectory($dir);

        foreach ($context->modules() as $module) {
            $this->createDirectory($dir . DIRECTORY_SEPARATOR . $module->name());
        }
    }

    public function get(string $name)
    {
        $dir = $this->baseNamespace . DIRECTORY_SEPARATOR . $name;

        if (!$this->directoryExists($dir)) {
            throw new \DomainException('Context does not exists.');
        }

        return $this->createProtectedObject(Context::class, $name, $this->getModules($dir));
    }

    private function getModules(string $dir): array
    {
        $modules = [];

        $contextName = substr($dir, strrpos($dir, '/') + 1);

        foreach (scandir($dir) as $el) {
            if ($this->isModule($dir, $el)) {
                $modules[] = $this->createProtectedObject(Module::class, md5($contextName . $el), $el);
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