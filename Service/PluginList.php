<?php

namespace RawPluginLoader\Service;

class PluginList
{

    /**
     * @var string
     */
    private $shopDir;

    /**
     * PluginList constructor.
     * @param string $shopDir
     */
    public function __construct($shopDir)
    {
        $this->shopDir = $shopDir;
    }

    /**
     * @return array
     */
    public function getPluginConfigs()
    {
        $pluginDir = '/custom/plugins/*/';
        $legacyPluginDir = '/engine/Shopware/Plugins/Local/*/*/';
        return array_merge(
            $this->getPluginInfo($pluginDir),
            $this->getPluginInfo($legacyPluginDir)
        );
    }

    /**
     * @param string $pluginDir
     * @return array
     */
    private function getPluginInfo($pluginDir)
    {
        $pluginInfo = $this->getPluginConfigDist($pluginDir);
        $pluginConfigs = glob($this->shopDir . $pluginDir . "pluginconfig.php");
        foreach ($pluginConfigs as $pluginConfig) {
            $dirPluginConfig = dirname($pluginConfig);
            $pluginConfigInfo = require $pluginConfig;
            $pluginInfo[dirname($pluginConfig)] = (isset($pluginInfo[$dirPluginConfig]))
                ? array_merge($pluginInfo[$dirPluginConfig], $pluginConfigInfo)
                : $pluginConfigInfo;
        }
        return $pluginInfo;
    }

    /**
     * @param string $path
     * @return array
     */
    private function getPluginConfigDist($path)
    {
        $pluginInfo = [];
        $pluginDistConfigs = glob($this->shopDir . $path . 'pluginconfig.dist.php');
        foreach ($pluginDistConfigs as $pluginDistConfig) {
            $pluginInfo[dirname($pluginDistConfig)] = require $pluginDistConfig;
        }
        return $pluginInfo;
    }


}