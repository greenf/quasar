<?php namespace Greenf\Quasar\Modules\Context\Domain;

class Module {

    private $id;

    private $name;

    private function __construct(string $id, string $name)
    {
        $this->id = $id;

        $this->name = $name;
    }

    public function id()
    {
        return $this->id;
    }

    public function name()
    {
        return $this->name;
    }

}