<?php

/*
 * This file is part of "musement/monolog-fluentd-bundle".
 *
 * (c) Musement S.p.A. <oss@musement.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Musement\MonologFluentdBundle\DependencyInjection;

use Monolog\Logger;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MusementMonologFluentdExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Converts PSR-3 levels to Monolog ones if necessary
        $config['level'] = Logger::toMonologLevel($config['level']);

        $container->setParameter('musement_monolog_fluentd.host', $config['host']);
        $container->setParameter('musement_monolog_fluentd.port', $config['port']);
        $container->setParameter('musement_monolog_fluentd.options', $config['options']);
        $container->setParameter('musement_monolog_fluentd.level', $config['level']);
        $container->setParameter('musement_monolog_fluentd.tag_fmt', $config['tag_fmt']);
        $container->setParameter('musement_monolog_fluentd.enable_exceptions', $config['enable_exceptions']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
