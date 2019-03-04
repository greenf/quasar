<?php namespace Greenf\Quasar\Infrastructure;

class CommonRepository {

    protected $appPath;

    protected $baseNamespace;

    protected $bolt;

    public function __construct(string $appPath, string $baseNamespace, string $bolt)
    {
        $this->appPath = $appPath;
        $this->baseNamespace = $baseNamespace;
        $this->bolt = $bolt;
    }
    
    protected function phpApplicationRepository(string $class)
    {
        return new $class($this->appPath, $this->baseNamespace);
    }
    
    protected function neo4jRepository(string $class)
    {
        return new $class($this->bolt);
    }
    
}