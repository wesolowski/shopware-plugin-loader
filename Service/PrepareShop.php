<?php

namespace RawPluginLoader\Service;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Components\Console\Application;

class PrepareShop
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var bool
     */
    private $clearCacheAfterCheckPlugin = false;

    /**
     * @param string $cacheDir
     */
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function setClearCache()
    {
        $this->clearCacheAfterCheckPlugin = true;
    }

    /**
     * @param OutputInterface $output
     * @param Application $app
     */
    public function clearCache(OutputInterface $output, Application $app)
    {
        if ($this->clearCacheAfterCheckPlugin === true) {
            $output->writeln("Clear cache");
            system("rm -rf " . dirname($this->cacheDir) . '/production*');
            system("rm -rf " . dirname($this->cacheDir) . '/testing*');

            $input = new ArrayInput([
                'command' => 'sw:generate:attributes'
            ]);
            $app->doRun($input, $output);
        }
    }
}