<?php

use Doctrine\Common\Collections\ArrayCollection;

class Shopware_Plugins_Core_RawPluginLoader_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    /**
     * @var array
     */
    private $pluginInfo;

    /**
     * The afterInit function registers the custom plugin models.
     */
    public function afterInit()
    {
        $this->get('Loader')->registerNamespace(
            'RawPluginLoader',
            $this->Path()
        );
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return array(
            'version' => $this->getVersion(),
            'label' => $this->getLabel(),
            'link' => $this->getPluginInfo()['link'],
            'author' => $this->getPluginInfo()['author'],
        );
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->getPluginInfo()['version'];
    }

    /**
     * Der Name des Plugins
     * @return string
     */
    public function getLabel()
    {
        return $this->getPluginInfo()['label'];
    }

    /**
     * @return bool
     */
    public function install()
    {
        $this->subscribeEvent('Shopware_Console_Add_Command', 'onAddConsoleCommand');

        return true;
    }


    /**
     * @return ArrayCollection
     */
    public function onAddConsoleCommand()
    {
        $container = Shopware()->Container();
        $rootDir = $container->getParameter('kernel.root_dir');
        $cacheDir = $container->getParameter('kernel.cache_dir');
        dump($rootDir);
        $container->set(
            'raw_plugin_loader.service.prepare_shop',
            new \RawPluginLoader\Service\PrepareShop($cacheDir)
        );
        $container->set(
            'raw_plugin_loader.service.plugin_list',
            new  \RawPluginLoader\Service\PluginList($rootDir)
        );

        return new ArrayCollection([
            new \RawPluginLoader\Commands\CheckPlugin(),
        ]);
    }


    /**
     * @return array
     */
    private function getPluginInfo()
    {
        if ($this->pluginInfo === null) {
            $xml = simplexml_load_string(
                file_get_contents(__DIR__ . '/plugin.xml'),
                "SimpleXMLElement",
                LIBXML_NOCDATA
            );
            $json = json_encode($xml);
            $this->pluginInfo = json_decode($json, TRUE);
        }

        return $this->pluginInfo;
    }
}