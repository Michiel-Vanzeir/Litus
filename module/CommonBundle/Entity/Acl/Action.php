<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Entity\Acl;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class that represents an action that can be executed on a certain resource.
 *
 * Examples:
 * DELETE a forum post, COOK a contact form, ...
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\Acl\Action")
 * @ORM\Table(
 *      name="acl.actions"),
 *      uniqueConstraints={@ORM\UniqueConstraint(name="action_unique", columns={"name", "resource"})}
 * )
 */
class Action
{
    /**
     * @var int The ID of this action
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string $name The name of the action
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var \CommonBundle\Entity\Acl\Resource The name of the resource
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Acl\Resource")
     * @ORM\JoinColumn(name="resource", referencedColumnName="name")
     */
    private $resource;

    /**
     * @param string                            $name     The name of the action
     * @param \CommonBundle\Entity\Acl\Resource $resource The resource to which the action belongs
     */
    public function __construct($name, Resource $resource)
    {
        $this->name = $name;
        $this->resource = $resource;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \CommonBundle\Entity\Acl\Resource
     */
    public function getResource()
    {
        return $this->resource;
    }
}
