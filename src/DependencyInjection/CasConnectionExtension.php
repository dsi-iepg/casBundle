<?php
namespace Iepg\Bundle\Cas\DependencyInjection;

use BadMethodCallException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;


class CasConnectionExtension extends Extension implements PrependExtensionInterface
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader =new YamlFileLoader($container,new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('cas_connection.cas_host', $config['cas_host']);
        $container->setParameter('cas_connection.cas_path', $config['cas_path']);
        $container->setParameter('cas_connection.cas_port', $config['cas_port']);
        $container->setParameter('cas_connection.cas_ca', $config['cas_ca']);
        $container->setParameter('cas_connection.cas_ca_path', $config['cas_ca_path']);
        $container->setParameter('cas_connection.cas_dispatcher_name', $config['cas_dispatcher_name']);
        $container->setParameter('cas_connection.cas_user_unknow', $config['cas_user_unknow']);

    }  

    public function prepend(ContainerBuilder $container)
    {
        $twigConfig = [];
        $container->prependExtensionConfig('twig', $twigConfig);
    }   
    
    /**
     * @return string
     *
     * @throws BadMethodCallException When the extension name does not follow conventions
     */
    public function getAlias(): string
    {
        return 'cas_connection';
    }
}