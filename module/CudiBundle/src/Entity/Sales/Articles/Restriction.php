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

namespace CudiBundle\Entity\Sales\Articles;

use CudiBundle\Entity\Sales\Article as Article,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sales\Articles\Restriction")
 * @ORM\Table(name="cudi.sales_articles_restrictions")
 */
class Restriction
{
    /**
     * @var integer The ID of the restriction
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CudiBundle\Entity\Sales\Article The article of the restriction
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sales\Article", inversedBy="barcodes")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var string The type of restriction
     *
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var string The value of the restriction
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $value;

    /**
     * @var array The possible types of a discount
     */
    public static $POSSIBLE_TYPES = array(
        'member' => 'Member',
        'amount' => 'Amount',
    );

    /**
     * @var array The possible types of a discount
     */
    public static $VALUE_TYPES = array(
        'member' => 'boolean',
        'amount' => 'integer',
    );

    /**
     * @param \CudiBundle\Entity\Sales\Article The article of the restriction
     * @param string $type The type of the restriction
     * @param string|null $value The value of the restriction
     */
    public function __construct(Article $article, $type, $value = null)
    {
        if (!self::isValidRestrictionType($type))
            throw new \InvalidArgumentException('The restriction type is not valid.');

        $this->article = $article;
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return boolean
     */
    public static function isValidRestrictionType($type)
    {
        return array_key_exists($type, self::$POSSIBLE_TYPES);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CudiBundle\Entity\Sales\Articles\Barcode
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::$POSSIBLE_TYPES[$this->type];
    }

    /**
     * @return string
     */
    public function getRawType()
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }
}
