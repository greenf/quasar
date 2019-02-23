<?php namespace Greenf\Quasar\Objects;

use Nette\PhpGenerator\PhpNamespace;

abstract class AbstractObject {

    protected $name;

    protected $config;

    protected $namespace;

    protected $class;

    static public function create(array $namespace, array $config) {
        $new = new static();
        $new->name = $config['name'];
        $new->config = $config;
        $new->namespace = new PhpNamespace(implode('\\', $namespace));

        $new->createClass();

        return $new;
    }

    abstract public function build();

    public function save() {
        $filePath =
            $this->config['appPath'] . DIRECTORY_SEPARATOR .
            str_replace('\\', DIRECTORY_SEPARATOR, $this->namespace->getName())
        ;

        //dump('$filePath', $filePath, (string) $this->namespace);

        if (!file_exists($filePath)) {
            mkdir($filePath, 655, true);
        }

        file_put_contents(
            $filePath . DIRECTORY_SEPARATOR . $this->name . '.php',
            '<?php ' . (string) $this->namespace
        );
    }

    protected function createClass()
    {
        $this->class = $this->namespace->addClass($this->name);
    }

    public function fullClassName(string $name, $base = false)
    {
        if (substr($name, 0, 1) != '\\') {
            $name = '\\' . $this->namespace->getName() . '\\' . $name;
        }

        if (!preg_match('/(?:(.+)\\\)?([^\\\]+)$/', $name, $data)) {
            throw new \Exception('Incorrect class name');
        }

        $namespace = $data[1];
        $class = $data[2];

        if ($base && !preg_match('/Base$/', $namespace)) {
            $namespace += '\\Base';
        }

        if (!$base && preg_match('/Base$/', $namespace)) {
            $namespace = preg_replace('/\\\Base$/', '', $namespace);
        }

        return $namespace . '\\' . $class;
    }
}