<?php

namespace Alpixel\Bundle\CMSBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CMSExtension extends Extension
{
    private $_blockDefaultClass = 'Alpixel\Bundle\CMSBundle\Entity\Block';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['content_types'] as $name => $contentType) {
            if (empty($contentType['title'])) {
                throw new InvalidConfigurationException('Content type '.$name.' shoud have a title');
            }

            if (empty($contentType['description'])) {
                throw new InvalidConfigurationException('Content type '.$name.' shoud have a description');
            }

            if (!isset($contentType['class']) || empty($contentType['class']) || !class_exists($contentType['class'])) {
                throw new InvalidConfigurationException('CMS '.$contentType['class'].' can\'t be found');
            }

            if (empty($contentType['controller'])) {
                throw new InvalidConfigurationException('CMS '.$contentType['controller'].' can\'t be found');
            }
        }
        $container->setParameter('cms.content_types', $config['content_types']);

        foreach ($config['blocks'] as $name => $contentType) {
            if (empty($contentType['title'])) {
                throw new InvalidConfigurationException('Block '.$name.' shoud have a title');
            }

            if ((!isset($contentType['class']) || empty($contentType['class'])) && class_exists($this->_blockDefaultClass)) {
                $config['blocks'][$name]['class'] = $this->_blockDefaultClass;
            }

            if (isset($contentType['class']) && !class_exists($contentType['class'])) {
                throw new InvalidConfigurationException('Block '.$contentType['class'].' can\'t be found');
            }
        }
        $container->setParameter('cms.blocks', $config['blocks']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
