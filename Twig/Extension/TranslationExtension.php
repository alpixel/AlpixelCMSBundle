<?php

namespace Alpixel\Bundle\CMSBundle\Twig\Extension;

use Alpixel\Bundle\CMSBundle\Entity\Node;
use Alpixel\Bundle\CMSBundle\Entity\TranslatableInterface;
use Alpixel\Bundle\CMSBundle\Helper\BlockHelper;
use Alpixel\Bundle\CMSBundle\Helper\CMSHelper;
use Symfony\Component\PropertyAccess\PropertyAccess;

class TranslationExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    protected $blockHelper;
    protected $container;
    protected $cmsHelper;

    public function __construct(CMSHelper $cmsHelper, BlockHelper $blockHelper, $container)
    {
        $this->cmsHelper = $cmsHelper;
        $this->container = $container;
        $this->blockHelper = $blockHelper;
    }

    public function getName()
    {
        return 'translation';
    }

    public function getGlobals()
    {
        return [
            'alpixel_cms_languages' => ($this->container->hasParameter('lunetics_locale.allowed_locales') ? $this->container->getParameter('lunetics_locale.allowed_locales') : null),
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('alpixel_cms_get_translation', [$this, 'cmsHasTranslation']),
        ];
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('iso_to_country_name', [$this, 'isoToCountryName']),
            new \Twig_SimpleFilter('is_translatable', [$this, 'isTranslatable']),
            new \Twig_SimpleFilter('translation_source_property', [$this, 'translationSourceProperty']),
        ];
    }

    public function isoToCountryName($iso)
    {
        return \Locale::getDisplayLanguage($iso, $this->container->getParameter('default_locale'));
    }

    public function isTranslatable($object)
    {
        return $object instanceof TranslatableInterface;
    }

    public function cmsHasTranslation(TranslatableInterface $object, $locale)
    {
        if ($object instanceof Node) {
            return $this->cmsHelper->nodeGetTranslation($object, $locale);
        } else {
            return $this->blockHelper->blockGetTranslation($object, $locale);
        }
    }

    public function translationSourceProperty($object, $property)
    {

        if (!is_object($object)) {
            throw new \InvalidArgumentException('The "$object" parameter must be an object.');
        } else if (empty($property) || !is_string($property)) {
            throw new \InvalidArgumentException('The "$property" parameter must be a non empty string.');
        }

        if ($this->isTranslatable($object) && $object->getTranslationSource() !== null) {
            $object = $object->getTranslationSource();
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($object, $property);
    }
}
