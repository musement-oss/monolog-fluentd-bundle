[![SensioLabsInsight](https://insight.sensiolabs.com/projects/eb3b864b-e61b-46d8-aa8a-89c500d73985/small.png)](https://insight.sensiolabs.com/projects/eb3b864b-e61b-46d8-aa8a-89c500d73985)
["Monolog Fluentd handler" Symfony bundle](https://github.com/musement/monolog-fluentd-bundle)
===

[![GitHub release](https://img.shields.io/github/release/musement/monolog-fluentd-bundle.svg?style=flat&label=latest)](https://github.com/musement/monolog-fluentd-bundle/releases/latest)
[![Project Status](http://opensource.box.com/badges/active.svg?style=flat)](http://opensource.box.com/badges)
[![Percentage of issues still open](http://isitmaintained.com/badge/open/musement/monolog-fluentd-bundle.svg?style=flat)](http://isitmaintained.com/project/musement/monolog-fluentd-bundle "Percentage of issues still open")
[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/musement/monolog-fluentd-bundle.svg?style=flat)](http://isitmaintained.com/project/musement/monolog-fluentd-bundle "Average time to resolve an issue")
[![composer.lock](https://poser.pugx.org/musement/monolog-fluentd-bundle/composerlock?style=flat)](https://packagist.org/packages/musement/monolog-fluentd-bundle)
[![Dependencies Status](https://img.shields.io/librariesio/github/musement/monolog-fluentd-bundle.svg?maxAge=3600&style=flat)](https://libraries.io/github/musement/monolog-fluentd-bundle)
[![License](https://img.shields.io/packagist/l/musement/monolog-fluentd-bundle.svg?style=flat)](https://tldrlegal.com/license/mit-license)

____

# musement/monolog-fluentd-bundle

This Symfony bundle enables logging to **fluentd** via monolog.

Fluentd is an open source data collector, it decouples data sources from backend systems by providing a unified logging layer in between.

## Install

    composer require musement/monolog-fluentd-bundle

## Register the bundle in Symfony

    <?php
    // AppKernel.php

    use Symfony\Component\HttpKernel\Kernel;
    use Symfony\Component\Config\Loader\LoaderInterface;

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                ...
                new Musement\MonologFluentdBundle\MusementMonologFluentdBundle(),
            );
            ...
        }
    }

## Configuration

These are the default parameters:

    musement_monolog_fluentd:
      host: localhost
      port: 24224
      options: []
      level: debug
      tag_fmt: '{{channel}}.{{level_name}}'
      exceptions: true

You can modify them in config.yml or parameters.yml

You may load the handler as a service

    monolog:
      handlers:
        musement_monolog_fluentd:
          type: service
          id: musement_monolog_fluentd.fluentd_handler

## How to run the tests

    phpunit -c phpunit.xml.dist

## Copyright

© Musement S.p.A.

