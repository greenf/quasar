<?php namespace Greenf\Quasar\Infrastructure\PhpApplication;

use Greenf\Quasar\Infrastructure\Repository;

abstract class PhpApplicationRepository extends Repository {

    protected $appPath;

    protected $baseNamespace;

    public function __construct(string $appPath, string $baseNamespace)
    {
        $this->appPath = $appPath;
        $this->baseNamespace = $baseNamespace;
    }

    protected function createDirectory($name): void
    {
        @mkdir($name, '0755', true);
    }

    protected function directoryExists($name): bool
    {
        return file_exists($name) && !is_file($name);
    }

}