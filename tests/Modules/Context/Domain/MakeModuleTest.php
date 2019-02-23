<?php namespace Greenf\Quasar\Tests\Modules\Context\Domain;

use Greenf\Quasar\Modules\Context\Domain\Module;
use Greenf\Quasar\Tests\Modules\Helper;

class MakeModuleTest extends \PHPUnit\Framework\TestCase {

    /**
     * @test
     */
    public function create_with_correct_data()
    {
        $moduleName = 'Client';

        $context = Helper::helpdeskContextWithNoModules();

        $context->makeModule($moduleName);

        $this->assertCount(1, $context->modules());
        $this->assertInstanceOf(Module::class, $context->modules()[0]);
        $this->assertEquals($moduleName, $context->modules()[0]->name());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function create_with_incorrect_name()
    {
        $moduleName = 'client';

        $context = Helper::helpdeskContextWithNoModules();

        $context->makeModule($moduleName);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function create_duplicate()
    {
        $moduleName = 'Client';

        $context = Helper::helpdeskContextWithClientModule();

        $context->makeModule($moduleName);
    }
}