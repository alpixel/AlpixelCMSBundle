<?php

namespace Alpixel\Bundle\CMSBundle\Entity\Repository;

use Alpixel\Bundle\CMSBundle\Entity\Node;
use Doctrine\ORM\EntityRepository;

class NodeRepository extends EntityRepository
{
    public function findAllWithLocale($locale)
    {
        return $this
            ->createQueryBuilder('n')
            ->andWhere('n.published = true')
            ->andWhere('n.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('n.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOnePublishedBySlug($slug)
    {
        return $this
            ->createQueryBuilder('n')
            ->andWhere('n.published = true')
            ->andWhere('n.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOnePublishedBySlugAndLocale($slug, $locale)
    {
        return $this
            ->createQueryBuilder('n')
            ->andWhere('n.published = true')
            ->andWhere('n.locale = :locale')
            ->andWhere('n.slug = :slug')
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBySlugAndLocale($slug, $locale)
    {
        return $this
            ->createQueryBuilder('n')
            ->andWhere('n.locale = :locale')
            ->andWhere('n.slug = :slug')
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findTranslation(Node $node, $locale)
    {
        $nodeSource = null;

        // We are checking if the node is the translation provider or translated
        // from an other node
        if ($node->getTranslationSource() !== null) {
            $nodeSource = $node->getTranslationSource();
            if ($nodeSource->getLocale() == $locale) {
                return $nodeSource;
            }
        } else {
            $nodeSource = $node;
        }

        return $this->createQueryBuilder('n')
                    ->addSelect('n')
                    ->andWhere('n.translationSource = :source')
                    ->andWhere('n.locale = :locale')
                    ->setParameters([
                        'source' => $nodeSource,
                        'locale' => $locale,
                    ])
                    ->getQuery()
                    ->getOneOrNullResult();
    }
}
