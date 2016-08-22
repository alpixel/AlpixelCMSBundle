<?php

namespace Alpixel\Bundle\CMSBundle\Entity\Repository;

use Alpixel\Bundle\CMSBundle\Entity\Node;
use Doctrine\ORM\EntityRepository;

/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class NodeRepository extends EntityRepository
{
    /**
     * @param $locale
     * @return array
     */
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

    /**
     * @param $slug
     * @return \Alpixel\Bundle\CMSBundle\Entity\Node|null
     */
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

    /**
     * @param $slug
     * @param $locale
     * @return \Alpixel\Bundle\CMSBundle\Entity\Node|null
     */
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

    /**
     * @param $slug
     * @param $locale
     * @return \Alpixel\Bundle\CMSBundle\Entity\Node|null
     */
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

    /**
     * @param \Alpixel\Bundle\CMSBundle\Entity\Node $node
     * @param $locale
     * @return \Alpixel\Bundle\CMSBundle\Entity\Node|null
     */
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
            ->setParameters(
                [
                    'source' => $nodeSource,
                    'locale' => $locale,
                ]
            )
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param \Alpixel\Bundle\CMSBundle\Entity\Node $node
     * @return array
     */
    public function findTranslations(Node $node)
    {
        $nodeSource = null;

        // We are checking if the node is the translation provider or translated
        // from an other node
        if ($node->getTranslationSource() !== null) {
            $nodeSource = $node->getTranslationSource();
        } else {
            $nodeSource = $node;
        }

        return $this->createQueryBuilder('n')
            ->addSelect('n')
            ->orWhere('n.translationSource = :source')
            ->orWhere('n.id = :id')
            ->setParameters(
                [
                    'source' => $nodeSource,
                    'id'     => $nodeSource->getId(),
                ]
            )
            ->getQuery()
            ->getResult();
    }
}
