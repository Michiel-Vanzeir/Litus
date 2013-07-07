<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace FormBundle\Entity\Node;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    CommonBundle\Component\Util\Url,
    FormBundle\Entity\Node\Entry,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Form")
 * @ORM\Table(name="nodes.forms")
 */
class Form extends \CommonBundle\Entity\Node
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
     * @var int The maximum number of entries of this form.
     *
     * @ORM\Column(name="max", type="integer")
     */
    private $max;

    /**
     * @var boolean Indicates whether submitters can submit more than different one answer.
     *
     * @ORM\Column(name="multiple", type="boolean")
     */
    private $multiple;

    /**
     * @var boolean Indicates whether submitters can be non members.
     *
     * @ORM\Column(name="non_member", type="boolean")
     */
    private $nonMember;

    /**
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Field", mappedBy="form")
     *
     * @ORM\OrderBy({"order" = "ASC"})
     */
    private $fields;

    /**
     * @var DateTime The start date and time of this form.
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date and time of this form.
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var boolean The flag whether the banner is active or not.
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var boolean The flag whether a mail will be sent upon completion.
     *
     * @ORM\Column(type="boolean")
     */
    private $mail;

    /**
     * @var string The subject of the mail sent upon completion.
     *
     * @ORM\Column(name="mail_subject", type="text")
     */
    private $mailSubject;

    /**
     * @var string The body of the mail sent upon completion.
     *
     * @ORM\Column(name="mail_body", type="text")
     */
    private $mailBody;

    /**
     * @var string The email address from which the mail is sent.
     *
     * @ORM\Column(name="mail_from", type="text")
     */
    private $mailFrom;

    /**
     * @var boolean Whether to send a copy to the sender or not.
     *
     * @ORM\Column(name="mail_bcc", type="boolean")
     */
    private $mailBcc;

    /**
     * @var array The translations of this form
     *
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Node\Translation", mappedBy="form", cascade={"remove"})
     */
    private $translations;

    /**
     * @param \CommonBundle\Entity\User\Person $person
     * @param string $title
     * @param boolean $redoable
     * @param boolean $multiple
     * @param boolean $mail Whether to send a mail upon completion.
     * @param string $mailSubject The subject of the mail.
     * @param string $mailBody The body of the mail.
     */
    public function __construct($person, $startDate, $endDate, $active, $max, $multiple, $nonMember, $mail, $mailSubject, $mailBody, $mailFrom, $mailBcc)
    {
        parent::__construct($person);

        $this->max = $max;
        $this->multiple = $multiple;
        $this->nonMember = $nonMember;
        $this->fields = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->active = $active;
        $this->mail = $mail;
        $this->mailSubject = $mailSubject;
        $this->mailBody = $mailBody;
        $this->mailFrom = $mailFrom;
        $this->mailBcc = $mailBcc;
    }

    /**
     * @param int $max
     *
     * @return \FormBundle\Entity\Node\Form
     */
    public function setMax($max) {
        $this->max = $max;
        return $this;
    }

    /**
     * @return int
     */
    public function getMax() {
        return $this->max;
    }

    /**
     * @param boolean $multiple
     *
     * @return \FormBundle\Entity\Node\Form
     */
    public function setMultiple($multiple) {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isMultiple() {
        return $this->multiple;
    }

    /**
     * @param boolean $nonMember
     *
     * @return \FormBundle\Entity\Node\Form
     */
    public function setNonMember($nonMember) {
        $this->nonMember = $nonMember;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isNonMember() {
        return $this->nonMember;
    }

    /**
     * @param FormBundle\Entity\Field The field to add to this form.
     */
    public function addField($field) {
        $this->fields->add($field);
        return $this;
    }

    public function getFields() {
        return $this->fields->toArray();
    }

    /**
     * @param DateTime $startDate
     *
     * @return \BannerBundle\Entity\Nodes\Form
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @param DateTime $endDate
     *
     * @return \BannerBundle\Entity\Nodes\Form
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * @param boolean $active
     *
     * @return \BannerBundle\Entity\Nodes\Form
     */
    public function setActive($active) {
        $this->active = $active;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive() {
        return $this->active;
    }

    /**
     * @param boolean $mail
     *
     * @return \BannerBundle\Entity\Nodes\Form
     */
    public function setMail($mail) {
        $this->mail = $mail;
        return $this;
    }

    /**
     * @return boolean
     */
    public function hasMail() {
        return $this->mail;
    }

    /**
     * @param boolean $mailSubject
     *
     * @return \BannerBundle\Entity\Nodes\Form
     */
    public function setMailSubject($mailSubject) {
        $this->mailSubject = $mailSubject;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getMailSubject() {
        return $this->mailSubject;
    }

    /**
     * @param boolean $mailBody
     *
     * @return \BannerBundle\Entity\Nodes\Form
     */
    public function setMailBody($mailBody) {
        $this->mailBody = $mailBody;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getMailBody() {
        return $this->mailBody;
    }

    public function getCompletedMailBody(EntityManager $entityManager, Entry $entry, Language $language) {
        $body = $this->getMailBody();
        $body = str_replace('%id%', $entry->getId(), $body);
        $body = str_replace('%first_name%', $entry->getPersonInfo()->getFirstName(), $body);
        $body = str_replace('%last_name%', $entry->getPersonInfo()->getLastName(), $body);

        $body = str_replace('%entry_summary%', $this->_getSummary($entityManager, $entry, $language), $body);

        return $body;
    }

    private function _getSummary(EntityManager $entityManager, Entry $entry, Language $language) {
        $fieldEntries = $entityManager->getRepository('FormBundle\Entity\Entry')
            ->findAllByFormEntry($entry);

        $result = '';
        foreach ($fieldEntries as $fieldEntry) {
            $result = $result . $fieldEntry->getField()->getLabel($language) . ': ' . $fieldEntry->getValueString($language) . '
';
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getMailFrom() {
        return $this->mailFrom;
    }

    /**
     * @param string $mailFrom
     *
     * @return \FromBundle\Entity\Nodes\Form
     */
    public function setMailFrom($mailFrom) {
        $this->mailFrom = $mailFrom;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getMailBcc() {
        return $this->mailBcc;
    }

    /**
     * @param boolean $mailBcc
     *
     * @return \FromBundle\Entity\Nodes\Form
     */
    public function setMailBcc($mailBcc) {
        $this->mailBcc = $mailBcc;
        return $this;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return string
     */
    public function getTitle(Language $language = null, $allowFallback = true)
    {

        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getTitle();

        return '';
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return string
     */
    public function getIntroduction(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getIntroduction();

        return '';
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return string
     */
    public function getSubmitText(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getSubmitText();

        return '';
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return \PageBundle\Entity\Nodes\Translation
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {

        foreach($this->translations as $translation) {
            if (null !== $language && $translation->getLanguage() == $language)
                return $translation;

            if ($translation->getLanguage()->getAbbrev() == \Locale::getDefault())
                $fallbackTranslation = $translation;
        }

        if ($allowFallback && isset($fallbackTranslation))
            return $fallbackTranslation;

        return null;
    }

    /**
     * Indicates whether the given person can view this form.
     *
     * @param \CommonBundle\Entity\User\Persons $person The person to check.
     * @param \Doctrine\ORM\EntityManager $entityManager The entity manager to use.
     * @return boolean
     */
    public function canBeViewedBy(Person $person = null, EntityManager $entityManager)
    {
        if (null === $person)
            return false;

        $result = $entityManager->getRepository('FormBundle\Entity\ViewerMap')
            ->findOneByPersonAndForm($person, $this);

        return $result !== null;
    }

    /**
     * Indicates whether the given person can edit this form.
     *
     * @param \CommonBundle\Entity\User\Person $person The person to check.
     * @return boolean
     */
    public function canBeEditedBy(Person $person = null)
    {
        if (null === $person)
            return false;

        if ($this->getCreationPerson()->getId() === $person->getId())
            return true;

        foreach ($person->getFlattenedRoles() as $role) {
            if ($role->getName() == 'editor')
                return true;
        }

        return false;
    }

    /**
     * Returns the value for the given entry and field.
     *
     * @param entry The entry to find the value for.
     * @param field The field to find the value for.
     * @param language The language to get the value in.
     *
     * @return The value.
     */
    public function getValueFor($entry, $field, $language)
    {
        foreach ($entry->getFieldEntries() as $fieldEntry) {
            if ($fieldEntry->getField()->getId() === $field->getId()) {
                return $fieldEntry->getValueString($language);
            }
        }
        return '';
    }

}