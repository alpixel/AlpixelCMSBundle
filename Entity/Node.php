<?php

namespace Alpixel\Bundle\CMSBundle\Entity;

use Alpixel\Bundle\SEOBundle\Entity\MetaTagPlaceholderInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Page.
 *
 * @ORM\Table(name="cms_node")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Alpixel\Bundle\CMSBundle\Entity\Repository\NodeRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({})
 */
abstract class Node implements MetaTagPlaceholderInterface, TranslatableInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="node_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=10, nullable=true)
     */
    protected $locale;

    /**
     * @var \Alpixel\Bundle\CMSBundle\Entity\Node
     *
     * @ORM\ManyToOne(targetEntity="Alpixel\Bundle\CMSBundle\Entity\Node")
     * @ORM\JoinColumn(name="translation_source_id",referencedColumnName="node_id", nullable=true, onDelete="SET NULL")
     */
    protected $translationSource;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    protected $content;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     */
    protected $dateCreated;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="date_updated", type="datetime", nullable=false)
     */
    protected $dateUpdated;

    /**
     * @var bool
     *
     * @ORM\Column(name="published", type="boolean", nullable=false, options={"default"= true})
     */
    protected $published;

    /**
     * @var int
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    protected $position;

    /**
     * @Gedmo\Slug(fields={"title"}, updatable=false, separator="-")
     * @ORM\Column(length=128, unique=true)
     **/
    protected $slug;

    public function __construct()
    {
    }

    abstract public function getType();

    public function __clone()
    {
        $this->id = null;
        $this->dateCreated = new \DateTime();
        $this->dateUpdated = new \DateTime();
        $this->position = null;
        $this->slug = null;
    }

    public function getUriParameters()
    {
        return [
            'id'            => $this->id,
            'slug'          => $this->slug,
        ];
    }

    public function getPlaceholders()
    {
        return [
            '[cms:title]'  => $this->title,
            '[cms:resume]' => substr(strip_tags($this->content), 0, 150),
        ];
    }

    public function __toString()
    {
        return $this->title;
    }

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param int $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param string $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the value of content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the value of content.
     *
     * @param string $content the content
     *
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Gets the value of dateCreated.
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Sets the value of dateCreated.
     *
     * @param \DateTime $dateCreated the date created
     *
     * @return self
     */
    public function setDateCreated(\DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Gets the value of dateUpdated.
     *
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * Sets the value of dateUpdated.
     *
     * @param \DateTime $dateUpdated the date updated
     *
     * @return self
     */
    public function setDateUpdated(\DateTime $dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    /**
     * Gets the value of published.
     *
     * @return bool
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Sets the value of published.
     *
     * @param bool $published the published
     *
     * @return self
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Gets the value of position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the value of position.
     *
     * @param int $position the position
     *
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Gets the value of slug.
     *
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Sets the value of slug.
     *
     * @param mixed $slug the slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Gets the value of locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the value of locale.
     *
     * @param string $locale the locale
     *
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Gets the value of translationSource.
     *
     * @return \Alpixel\Bundle\CMSBundle\Entity\Node
     */
    public function getTranslationSource()
    {
        return $this->translationSource;
    }

    /**
     * Sets the value of translationSource.
     *
     * @param \Alpixel\Bundle\CMSBundle\Entity\Node $translationSource the translation source
     *
     * @return self
     */
    public function setTranslationSource(\Alpixel\Bundle\CMSBundle\Entity\Node $translationSource)
    {
        $this->translationSource = $translationSource;

        return $this;
    }
}
