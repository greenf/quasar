<?php namespace Greenf\Quasar\Tests\Modules;


use Greenf\Quasar\Modules\Context\Domain\Context;
use Greenf\Quasar\Modules\Context\Domain\Module;

class Helper {

    /**
     * @return Context
     */
    public static function helpdeskContextWithNoModules()
    {
        $class = new \ReflectionClass(Context::class);
        $constructor = $class->getConstructor();
        $constructor->setAccessible(true);
        $object = $class->newInstanceWithoutConstructor();
        $constructor->invoke($object, 'Helpdesk', []);

        return $object;
    }

    /**
     * @return Context
     */
    public static function helpdeskContextWithClientModule()
    {
        $class = new \ReflectionClass(Context::class);
        $constructor = $class->getConstructor();
        $constructor->setAccessible(true);
        $object = $class->newInstanceWithoutConstructor();
        $constructor->invoke($object, 'Helpdesk', [self::clientModule()]);

        return $object;
    }

    /**
     * @return Module
     */
    public static function clientModule()
    {
        $class = new \ReflectionClass(Module::class);
        $constructor = $class->getConstructor();
        $constructor->setAccessible(true);
        $object = $class->newInstanceWithoutConstructor();
        $constructor->invoke($object, 'Helpdesk.Client', 'Client');

        return $object;
    }

}