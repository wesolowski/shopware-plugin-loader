<?php
namespace RawPluginLoader\Commands;

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
        $output->writeln("Start");

        $app = $this->getApplication();
        $app->doRun(new ArrayInput([
            'command' => 'sw:plugin:refresh',
        ]), $output);

        $pluginsConfigs = $this->getPluginConfigs();
        $pluginListInfo = $this->getShopwarePluginInfo();

        $output->writeln("");
        foreach ($pluginsConfigs as $pluginPath => $pluginConfig) {
            $pluginIndent = basename($pluginPath);

            if (isset($pluginConfig['active'])) {
                $isPluginActive = (bool)$pluginConfig['active'];

                if (isset($pluginListInfo[$pluginIndent])
                    && !$pluginListInfo[$pluginIndent]['active']
                    && $isPluginActive
                ) {
                    $this->activatePlugin($output, $pluginIndent);
                } elseif (isset($pluginListInfo[$pluginIndent])
                    && $pluginListInfo[$pluginIndent]['active']
                    && !$isPluginActive
                ) {
                    $this->unistallPlugin($output, $pluginIndent);
                }

                if (!$isPluginActive) {
                    $this->deleteUnitTestFolder($pluginPath);
                }
            }
        }
        $output->writeln("");
        $output->writeln("CheckPlugin is completed");
    }

    /**
     * @return array
     */
    protected function getPluginConfigs()
    {
        $shopDir = $this->getContainer()->get('kernel')->getRootDir();
        $pluginInfo = [];

        $pluginDistConfigs = glob($shopDir . "/custom/plugins/*/pluginconfig.dist.php");
        foreach ($pluginDistConfigs as $pluginDistConfig) {
            $pluginInfo[dirname($pluginDistConfig)] = require $pluginDistConfig;
        }

        $pluginConfigs = glob($shopDir . "/custom/plugins/*/pluginconfig.php");
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
     * @param $pluginIndent
     * @return array
     */
    private function activatePlugin(OutputInterface $output, $pluginIndent)
    {
        $app = $this->getApplication();

        $input = new ArrayInput([
            'command' => 'sw:plugin:install',
            'plugin' => $pluginIndent
        ]);
        $app->doRun($input, $output);

        $input = new ArrayInput([
            'command' => 'sw:plugin:activate',
            'plugin' => $pluginIndent
        ]);
        $app->doRun($input, $output);
    }

    /**
     * @param OutputInterface $output
     * @param $pluginIndent
     */
    protected function unistallPlugin(OutputInterface $output, $pluginIndent)
    {
        $app = $this->getApplication();

        $input = new ArrayInput([
            'command' => 'sw:plugin:deactivate',
            'plugin' => $pluginIndent
        ]);
        $app->doRun($input, $output);

        $input = new ArrayInput([
            'command' => 'sw:plugin:uninstall',
            'plugin' => $pluginIndent
        ]);
        $app->doRun($input, $output);
    }

    /**
     * @param $pluginPath
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