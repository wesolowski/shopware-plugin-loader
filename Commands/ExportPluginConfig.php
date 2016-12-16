<?php
namespace RawPluginLoader\Commands;

use Shopware\Commands\ShopwareCommand;
use Shopware\Components\Model\ModelRepository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportPluginConfig extends ShopwareCommand
{

    protected function configure()
    {
        $this
            ->setName('raw:plugin-loader:export')
            ->setDescription('export shopware plugins config for raw:plugin-loader')
            ->addArgument(
                'plugin',
                InputArgument::REQUIRED,
                'Name of the plugin to export config.'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pluginName = $input->getArgument('plugin');

        $this->getShopwarePluginInfoByName($pluginName);

        $output->writeln("");
        $output->writeln("CheckPlugin is completed");
    }

    /**
     * @param string $pluginName
     */
    private function getShopwarePluginInfoByName($pluginName)
    {
        /** @var ModelManager $em */
        $em = $this->container->get('models');

        /** @var ModelRepository $repository */
        $repository = $em->getRepository('Shopware\Models\Plugin\Plugin');
        $builder = $repository->createQueryBuilder('plugin');
        $builder->andWhere('plugin.name =:pluginName');
        $builder->setParameter('pluginName', $pluginName);
        $plugin = $builder->getQuery()->getOneOrNullResult();
    }
}