<?php namespace Greenf\Quasar\Console;

use Greenf\Quasar\Modules\Context\Application\Creator;
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

        //$repository = new ContextPhpApplicationRepository(
        //    $this->config['php_app']['app_path'],
        //    $this->config['php_app']['namespace']
        //);
        //
        $repository = new ContextNeo4JRepository($this->config['neo4j']['bolt']);

        $creator = new Creator($repository);

        $creator->create('Manufacture');

        $creator->makeModule('Manufacture', 'Order');


        //$domainGenerator = new Domain($this->config);
        //$domainGenerator->generate();

        //$classDefinition = json_decode();

    }

}