<?php

namespace FormBundle\Entity;

use CommonBundle\Component\Util\Url,
    CommonBundle\Entity\General\Language,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Translation")
 * @ORM\Table(name="forms.field_translation")
 */
class Translation
{
    /**
     * @var int The ID of this tanslation
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \FormBundle\Entity\Field The field of this translation
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Field", inversedBy="translations")
     * @ORM\JoinColumn(name="field", referencedColumnName="id")
     */
    private $field;

    /**
     * @var \CommonBundle\Entity\General\Language The language of this tanslation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The label of this tanslation
     *
     * @ORM\Column(type="string")
     */
    private $label;

    /**
     * @param \FormBundle\Entity\Field field
     * @param \CommonBundle\Entity\General\Language $language
     * @param string $content
     * @param string $label
     */
    public function __construct(Field $field, Language $language, $label)
    {
        $this->field = $field;
        $this->language = $language;
        $this->label = $label;
    }

    /**
     * @return \FormBundle\Entity\Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return \CommonBundle\Entity\General\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return \FormBundle\Entity\Translation
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }
}
