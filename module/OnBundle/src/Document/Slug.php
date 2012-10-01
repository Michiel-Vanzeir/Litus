<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace OnBundle\Document;

use CommonBundle\Entity\Users\Person,
    Doctrine\ODM\MongoDB\Mapping\Annotations as ODM,
    Doctrine\ORM\EntityManager;

/**
 * This entity stores a slug, and the URL it should redirect to.
 *
 * @ODM\Document(
 *     collection="slugs",
 *     repositoryClass="OnBundle\Repository\Slug"
 * )
 */
class Slug
{
    /**
     * @var integer The ID of this slug
     *
     * @ODM\Id
     */
    private $id;

    /**
     * @var string The ID of the person that created this slug
     *
     * @ODM\Field(type="int")
     */
    private $creationPerson;

    /**
     * @var string The name of the slug
     *
     * @ODM\Field(type="string")
     * @ODM\UniqueIndex
     */
    private $name;

    /**
     * @var string The URL this logs redirects to
     *
     * @ODM\Field(type="string")
     */
    private $url;

    /**
     * @var string How many times this slug was hit
     *
     * @ODM\Field(type="increment")
     */
    private $hits;

    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param string $name
     * @param string $url
     */
    public function __construct(Person $person, $name, $url)
    {
        $this->person = $person->getId();

        $this->name = $name;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getCreationPerson(EntityManager $entityManager)
    {
        return $entityManager->getRepository('CommonBundle\Entity\Users\Person')
            ->findOneById($this->creationPerson);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \OnBundle\Document\Slug
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return \OnBundle\Document\Slug
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return int
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * @return \OnBundle\Document\Slug
     */
    public function incrementHits()
    {
        $this->hits++;
        return $this;
    }
}