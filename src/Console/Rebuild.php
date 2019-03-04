<?php namespace Greenf\Quasar\Console;

use Greenf\Quasar\Modules\Aggregate\Application\AggregateBuilder;
use Greenf\Quasar\Modules\Aggregate\Domain\ValueObject;
use Greenf\Quasar\Modules\Aggregate\Infrastructure\AggregatePhpApplicationRepository;
use Greenf\Quasar\Modules\Context\Application\Creator as ContextCreator;
use Greenf\Quasar\Modules\Aggregate\Application\Manager as AggregateManager;
use Greenf\Quasar\Modules\Context\Infrastructure\ContextCommonRepository;
use Greenf\Quasar\Modules\Context\Infrastructure\ContextNeo4JRepository;
use Greenf\Quasar\Modules\Context\Infrastructure\ContextPhpApplicationRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Rebuild extends Command {

    protected $input;

    protected $output;

    protected $config;

    protected function configure()
    {
        # php vendor/greenf/quasar/bin/console quasar:rebuild --config=/home/www/Fotocore/Config/quasar.php
        $this
            ->setName('quasar:rebuild')
            ->setDescription('Rebuild domain models.')
            ->setHelp('This command rebuilds domain models.')
            ->addOption('config', 'c',InputOption::VALUE_REQUIRED, 'Config file');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $filePath = substr($input->getOption('config'), 0, 1) == '/'
            ? $input->getOption('config')
            : __DIR__ . '/../../../../../' . $input->getOption('config');

        if (!file_exists($filePath)) {
            $output->writeln(sprintf('<error>Config file [%s] does not exist.</error>', $filePath));
            die;
        }

        $this->config = require $filePath;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        //dump('asd', $this->config);

        $contextRepository = new ContextPhpApplicationRepository($this->config['php_app']);

        //$contextRepository = new ContextNeo4JRepository($this->config['neo4j']['bolt']);
        //
        //$contextRepository = new ContextCommonRepository(
        //    $this->config['php_app']['app_path'],
        //    $this->config['php_app']['namespace'],
        //    $this->config['neo4j']['bolt']
        //);

        $contextCreator = new ContextCreator($contextRepository);

        //$contextCreator->create('Manufacture');
        //$contextCreator->makeModule('Manufacture', 'Order');
        //$contextCreator->makeModule('Manufacture', 'Process');
        //
        //$contextCreator->create('Ecommerce');
        //$contextCreator->makeModule('Ecommerce', 'Order');

        $aggregateRepository = new AggregatePhpApplicationRepository($this->config['php_app']);

        $aggregateManager = new AggregateManager($aggregateRepository);

        //$aggregateManager->create((new AggregateBuilder())
        //    ->inContext('Ecommerce')
        //    ->inModule('Order')
        //    ->withName('NewOrder')
        //    ->withCustomIdentity('OrderNumber', ValueObject::TYPE_STRING, true)
        //    ->build());
        //
        //$aggregateManager->create((new AggregateBuilder())
        //    ->inContext('Manufacture')
        //    ->inModule('Order')
        //    ->withName('Order')
        //    ->withCustomIdentity('OrderNumber', ValueObject::TYPE_STRING, true)
        //    ->build());
        //
        //$aggregateManager->create($agg = (new AggregateBuilder())
        //    ->inContext('Manufacture')
        //    ->inModule('Process')
        //    ->withName('Process')
        //    ->build());
        //
        //$aggregateManager->addProperty('Manufacture', 'Process', 'Process',
        //    'OrderNumber', ValueObject::TYPE_STRING, false, true);

        //$aggregateManager->addProperty('Manufacture', 'Process', 'Process',
        //    'CreatedAt', ValueObject::TYPE_DATETIME, false, false);

        $aggregateManager->addProperty('Manufacture', 'Process', 'Process',
            'StartedAt', ValueObject::TYPE_DATETIME, false, false);

        //$aggregateManager->addProperty('Ecommerce', 'Order', 'NewOrder',
        //    'CreatedAt', ValueObject::TYPE_DATETIME, false, false);
    }

}