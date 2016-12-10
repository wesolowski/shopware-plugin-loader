<?php

namespace RawPluginLoader\Service;

use Shopware\Components\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class ActivatePlugin {

    /**
     * @var Application
     */
    private $app;

    /**
     * @var OutputInterface;
     */
    private $output;

    /**
     * @var array
     */
    private $pluginListInfo;

    /**
     * @param Application $app
     * @param OutputInterface $output
     * @param array $pluginListInfo
     */
    public function __construct(Application $app, OutputInterface $output, array $pluginListInfo)
    {
        $this->app = $app;
        $this->output = $output;
        $this->pluginListInfo = $pluginListInfo;
    }

    /**
     * @param bool $isPluginActive
     * @param string $pluginIndent
     * @param string $pluginPath
     */
    public function checkPlugin($isPluginActive, $pluginIndent, $pluginPath) {
        if (isset($this->pluginListInfo[$pluginIndent])
            && !$this->pluginListInfo[$pluginIndent]['active']
            && $isPluginActive
        ) {
            $this->activatePlugin($pluginIndent);
        } elseif (isset($this->pluginListInfo[$pluginIndent])
            && $this->pluginListInfo[$pluginIndent]['active']
            && !$isPluginActive
        ) {
            $this->unistallPlugin($pluginIndent);
        }

        if (!$isPluginActive) {
            $this->deleteUnitTestFolder($pluginPath);
        }
    }

    /**
     * @param string $pluginIndent
     */
    private function activatePlugin($pluginIndent)
    {
        $app = $this->app;

        $input = new ArrayInput([
            'command' => 'sw:plugin:install',
            'plugin' => $pluginIndent
        ]);
        $app->doRun($input, $this->output);

        $input = new ArrayInput([
            'command' => 'sw:plugin:activate',
            'plugin' => $pluginIndent
        ]);
        $app->doRun($input, $this->output);
    }

    /**
     * @param string $pluginIndent
     */
    protected function unistallPlugin($pluginIndent)
    {
        $app = $this->app;

        $input = new ArrayInput([
            'command' => 'sw:plugin:deactivate',
            'plugin' => $pluginIndent
        ]);
        $app->doRun($input, $this->output);

        $input = new ArrayInput([
            'command' => 'sw:plugin:uninstall',
            'plugin' => $pluginIndent
        ]);
        $app->doRun($input, $this->output);
    }

    /**
     * @param string $pluginPath
     */
    private function deleteUnitTestFolder($pluginPath)
    {
        system("rm -rf " . escapeshellarg($pluginPath . '/Tests'));
        system("rm -rf " . escapeshellarg($pluginPath . '/Test'));
        system("rm -rf " . escapeshellarg($pluginPath . '/test'));
        system("rm -rf " . escapeshellarg($pluginPath . '/tests'));
        system("rm -rf " . escapeshellarg($pluginPath . '/phpunit.xml'));
        system("rm -rf " . escapeshellarg($pluginPath . '/phpunit.xml.dist'));
    }
}