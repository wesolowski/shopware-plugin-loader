<?php
namespace RawPluginLoader\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckPlugin extends ShopwareCommand
{

    protected function configure()
    {
        $this
            ->setName('raw:plugin-loader:check')
            ->setDescription('activate/deactivate shopware plugins');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Start");

        $output->writeln("CheckPlugin is completed");
    }
}