<?php namespace Greenf\Quasar\Infrastructure\PhpApplication;

use Greenf\Core\Domain\ValueObject\Datetime;
use Greenf\Quasar\Infrastructure\Repository;
use Greenf\Quasar\Modules\Aggregate\Domain\ValueObject;
use PhpValueObjects\Scalar\StringLiteral;

abstract class PhpApplicationRepository extends Repository {

    protected static $defaultConfig = [
        'app_path'             => '/home/www',
        'shared_dir'           => 'Greenfield/Shared/Domain',
        'context_dir'          => 'Greenfield/Context/{context_name}',
        'module_dir'           => '{context_dir}/{module_name}',
        'aggregate_dir'        => '{module_dir}/Domain',
        'identity_interface'   => \Greenf\Core\Domain\IdentityInterface::class,
        'aggregate_root_class' => \Greenf\Core\Domain\AggregateRoot::class,
        'value_objects'        => [
            ValueObject::TYPE_STRING   => StringLiteral::class,
            ValueObject::TYPE_DATETIME => Datetime::class,
        ],
    ];

    protected $config;

    public function __construct(array $config = [])
    {
        # TODO: config validation

        $this->config = $config;
    }

    public function moduleDir(string $contextName, string $moduleName)
    {
        $moduleDir = $this->config['module_dir'] ?? self::$defaultConfig['module_dir'];

        return str_replace(
            ['{context_dir}', '{module_name}'],
            [$this->contextDir($contextName), ucfirst($moduleName)],
            $moduleDir
        );
    }

    public function aggregateDir(string $contextName, string $moduleName)
    {
        $aggregateDir = $this->config['aggregate_dir'] ?? self::$defaultConfig['aggregate_dir'];

        return str_replace(
            '{module_dir}',
            $this->moduleDir($contextName, $moduleName),
            $aggregateDir
        );
    }

    protected function saveFile(string $dir, string $fileName, string $content)
    {
        file_put_contents(
            $this->filePath($dir, $fileName),
            '<?php ' . $content
        );
    }

    protected function filePath(string $dir, string $fileName): string
    {
        return $dir . DIRECTORY_SEPARATOR . $fileName . '.php';
    }

    protected function namespaceFromDir(string $dir, string $class = null): string
    {
        return implode('\\', explode(DIRECTORY_SEPARATOR, $dir)) . ($class ? '\\' . $class : '');
    }

    protected function sharedDir()
    {
        return $this->config['shared_dir'] ?? self::$defaultConfig['shared_dir'];
    }

    protected function identityInterface()
    {
        return $this->config['identity_interface'] ?? self::$defaultConfig['identity_interface'];
    }

    protected function aggregateAbstract()
    {
        return $this->config['aggregate_root_class'] ?? self::$defaultConfig['aggregate_root_class'];
    }

    protected function contextDir(string $contextName)
    {
        $contextDir = $this->config['context_dir'] ?? self::$defaultConfig['context_dir'];

        return str_replace(
            '{context_name}',
            ucfirst($contextName),
            $contextDir
        );
    }

    protected function getValueObjectExtend(string $type): string
    {
        return $this->config['value_objects'][$type] ?? self::$defaultConfig['value_objects'][$type];
    }

    protected function getValueObjectType(string $className): string
    {
        return array_flip(array_merge((array) $this->config['value_objects'], self::$defaultConfig['value_objects']))[$className];
    }

    protected function createDirectory($name): void
    {
        @mkdir($name, '0755', true);
    }

    protected function directoryExists($name): bool
    {
        return file_exists($name) && !is_file($name);
    }

    protected function classExists(string $dir, string $fileName): bool
    {
        $filePath = $this->filePath($dir, $fileName);

        return file_exists($filePath) && is_file($filePath);
    }

    protected function camelCase(string $name)
    {
        return lcfirst($name);
    }

    protected function pascalCase(string $name)
    {
        return ucfirst($name);
    }
}