<?php

/*
 * This file is part of "musement/monolog-fluentd-bundle".
 *
 * (c) Musement S.p.A. <oss@musement.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Musement\MonologFluentdBundle\Tests\DependencyInjection;

use Monolog\Logger;
use Musement\MonologFluentdBundle\DependencyInjection\MusementMonologFluentdExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

class MusementMonologFluentdExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var MusementMonologFluentdExtension */
    protected $loader;

    /** @var ContainerBuilder */
    protected $container;

    public function setUp()
    {
        $this->loader = new MusementMonologFluentdExtension();
        $this->container = new ContainerBuilder();
    }

    public function testParameterHost()
    {
        $this->loader->load($this->getConfig(), $this->container);
        $this->assertParameter('localhost', 'musement_monolog_fluentd.host');
    }

    public function testParameterPort()
    {
        $this->loader->load($this->getConfig(), $this->container);
        $this->assertParameter(24224, 'musement_monolog_fluentd.port');
    }

    public function testParameterOptions()
    {
        $this->loader->load($this->getConfig(), $this->container);
        $this->assertParameter([], 'musement_monolog_fluentd.options');
    }

    public function testParameterLevelAsInt()
    {
        $config = $this->getConfig();
        $config['musement_monolog_fluentd']['level'] = Logger::DEBUG;
        $this->loader->load($config, $this->container);
        $this->assertParameter(Logger::DEBUG, 'musement_monolog_fluentd.level');
    }

    public function testParameterLevelAsString()
    {
        $config = $this->getConfig();
        $config['musement_monolog_fluentd']['level'] = 'dEbUg';
        $this->loader->load($config, $this->container);
        $this->assertParameter(Logger::DEBUG, 'musement_monolog_fluentd.level');
    }

    public function testParameterTagFmt()
    {
        $config = $this->getConfig();
        $this->loader->load($config, $this->container);
        $this->assertParameter('{{channel}}.{{level_name}}', 'musement_monolog_fluentd.tag_fmt');
    }

    public function testParameterEnableExceptions()
    {
        $config = $this->getConfig();
        $this->loader->load($config, $this->container);
        $this->assertParameter(false, 'musement_monolog_fluentd.enable_exceptions');
    }

    protected function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->container->getParameter($key));
    }

    protected function getConfig()
    {
        $yaml = <<<'EOF'
musement_monolog_fluentd:
    host: localhost
    port: 24224
    options: []
    level: debug
    tag_fmt: '{{channel}}.{{level_name}}'
    enable_exceptions: false
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }
}
