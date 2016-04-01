<?php

namespace Alpixel\Bundle\CMSBundle\Tests\Functional;

use JMS\TranslationBundle\Exception\RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class AppKernel extends Kernel
{
    private $config;

    public function __construct($config)
    {
        parent::__construct('test', true);

        $fs = new Filesystem();
        if (!$fs->isAbsolutePath($config)) {
            $config = __DIR__.'/config/'.$config;
        }

        if (!file_exists($config)) {
            throw new RuntimeException(sprintf('The config file "%s" does not exist.', $config));
        }

        $this->config = $config;
    }

    public function registerBundles()
    {
        return array(
            //Symfony
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            //Doctrine
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),

            // Admin
            new \Sonata\CoreBundle\SonataCoreBundle(),
            new \Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new \Sonata\AdminBundle\SonataAdminBundle(),
            new \Sonata\BlockBundle\SonataBlockBundle(),
            new \Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new \Pix\SortableBehaviorBundle\PixSortableBehaviorBundle(),
            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),

            //i18n
            new \Lunetics\LocaleBundle\LuneticsLocaleBundle(),

            //i18n : Translation
            new \JMS\TranslationBundle\JMSTranslationBundle(),
            new \JMS\I18nRoutingBundle\JMSI18nRoutingBundle(),
            new \Happyr\TranslationBundle\HappyrTranslationBundle(),
            new \Http\HttplugBundle\HttplugBundle(),

            //ALPIXEL User
            new \Alpixel\Bundle\UserBundle\AlpixelUserBundle(),
            new \FOS\UserBundle\FOSUserBundle(),

            //ALPIXEL CMS bundle
            new \Alpixel\Bundle\CMSBundle\AlpixelCMSBundle(),

            //ALPIXEL media bundle
            new \Alpixel\Bundle\MediaBundle\AlpixelMediaBundle(),
            new \Liip\ImagineBundle\LiipImagineBundle(),

            new \Alpixel\Bundle\CMSBundle\Tests\Functional\Fixture\TestBundle\TestBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->config);
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().'/AlpixelCMSBundle';
    }

    public function serialize()
    {
        return $this->config;
    }

    public function unserialize($config)
    {
        $this->__construct($config);
    }
}