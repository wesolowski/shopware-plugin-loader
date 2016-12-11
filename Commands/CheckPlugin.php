<?php
namespace RawPluginLoader\Commands;

use RawPluginLoader\Service\ActivatePlugin;
use RawPluginLoader\Service\PluginList;
use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckPlugin extends ShopwareCommand
{

    protected function configure()
    {
        $this
            ->setName('raw:plugin-loader')
            ->setDescription('activate/deactivate shopware plugins');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->refreshPluginList($output);
        $app = $this->getApplication();

        $pluginsConfigs = $this->container->get('raw_plugin_loader.service.plugin_list')->getPluginConfigs();
        $pluginListInfo = $this->getShopwarePluginInfo();
        $activatePlugin = new ActivatePlugin(
            $app,
            $output,
            $pluginListInfo
        );

        foreach ($pluginsConfigs as $pluginPath => $pluginConfig) {
            $pluginIndent = basename($pluginPath);

            if (isset($pluginConfig['active'])) {
                $pluginStatus = $activatePlugin->checkPlugin(
                    (bool)$pluginConfig['active'],
                    $pluginIndent,
                    $pluginPath
                );
                $this->checkClearCache($pluginConfig, $pluginStatus);
            }
        }

        $this->getPrepareShop()->clearCache($output, $app);

        $output->writeln("");
        $output->writeln("CheckPlugin is completed");
    }

    /**
     * @return array
     */
    protected function getShopwarePluginInfo()
    {
        /** @var ModelManager $em */
        $em = $this->container->get('models');

        $repository = $em->getRepository('Shopware\Models\Plugin\Plugin');
        $builder = $repository->createQueryBuilder('plugin');
        $builder->andWhere('plugin.capabilityEnable = true');
        $builder->addOrderBy('plugin.name');
        $plugins = $builder->getQuery()->execute();

        $rows = [];

        /** @var Plugin $plugin */
        foreach ($plugins as $plugin) {
            $rows[$plugin->getName()] = [
                'active' => (bool)$plugin->getActive(),
                'installed' => (bool)$plugin->getInstalled()
            ];
        }
        return $rows;
    }

    /**
     * @param OutputInterface $output
     */
    private function refreshPluginList(OutputInterface $output)
    {
        $app = $this->getApplication();
        $app->doRun(new ArrayInput([
            'command' => 'sw:plugin:refresh',
        ]), $output);
    }

    /**
     * @param array $pluginConfig
     * @param int $pluginStatus
     */
    private function checkClearCache(array $pluginConfig, $pluginStatus)
    {
        if (isset($pluginConfig['clearcache'])
            && $pluginConfig['clearcache']
            && $pluginStatus !== ActivatePlugin::PluginNoChange
        ) {
            $this->getPrepareShop()->setClearCache();
        }
    }

    /**
     * @return \RawPluginLoader\Service\PrepareShop
     */
    private function getPrepareShop(){
        return $this->container->get('raw_plugin_loader.service.prepare_shop')
    }
}