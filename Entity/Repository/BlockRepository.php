<?php

namespace Alpixel\Bundle\CMSBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Sonata\BlockBundle\Model\Block;

class BlockRepository extends EntityRepository
{
    /**
     * @param $type
     * @param $locale
     * @return \Alpixel\Bundle\CMSBundle\Entity\Block
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBlock($type, $locale)
    {
        return $this
            ->createQueryBuilder('b')
            ->andWhere('b.locale = :locale')
            ->andWhere('b.slug = :type')
            ->setParameter('locale', $locale)
            ->setParameter('type', $type)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findTranslation(Block $block, $locale)
    {
        $source = null;

        // We are checking if the node is the translation provider or translated
        // from an other node
        if ($block->getTranslationSource() !== null) {
            $source = $block->getTranslationSource();
            if ($source->getLocale() == $locale) {
                return $source;
            }
        } else {
            $source = $block;
        }

        return $this->createQueryBuilder('n')
            ->addSelect('n')
            ->andWhere('n.translationSource = :source')
            ->andWhere('n.locale = :locale')
            ->setParameters([
                'source' => $source,
                'locale' => $locale,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
