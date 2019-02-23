<?php namespace Greenf\Quasar\Tests\Modules\Context\Domain;

use Greenf\Quasar\Modules\Context\Domain\Context;

class CreateTest extends \PHPUnit\Framework\TestCase {

    /**
     * @test
     */
    public function create_with_correct_data()
    {
        $contextName = 'Helpdesk';

        $context = Context::create($contextName);

        $this->assertInstanceOf(Context::class, $context);
        $this->assertEquals($contextName, $context->name());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function create_with_incorrect_name()
    {
        $contextName = 'helpdesk';

        Context::create($contextName);
    }
}