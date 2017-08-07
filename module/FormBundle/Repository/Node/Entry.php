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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Repository\Node;

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    CommonBundle\Entity\User\Person,
    FormBundle\Entity\Node\Form as FormEntity,
    FormBundle\Entity\Node\Group as GroupEntity,
    FormBundle\Entity\Node\GuestInfo as GuestInfoEntity;

/**
 * Entry
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Entry extends EntityRepository
{
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\Node\Entry', 'n')
            ->orderBy('n.creationTime', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByFormQuery(FormEntity $form)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('f')
            ->from('FormBundle\Entity\Node\Entry', 'f')
            ->orderBy('f.creationTime', 'DESC')
            ->where(
                $query->expr()->eq('f.form', ':form')
            )
            ->setParameter('form', $form)
            ->getQuery();

        return $resultSet;
    }

    public function findAllByFormAndPersonQuery(FormEntity $form, Person $person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('f')
            ->from('FormBundle\Entity\Node\Entry', 'f')
            ->orderBy('f.creationTime', 'DESC')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('f.form', ':form'),
                    $query->expr()->eq('f.creationPerson', ':person')
                )
            )
            ->setParameter('form', $form)
            ->setParameter('person', $person)
            ->orderBy('f.creationTime', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    public function findDraftVersionByFormAndPerson(FormEntity $form, Person $person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('f')
            ->from('FormBundle\Entity\Node\Entry', 'f')
            ->orderBy('f.creationTime', 'DESC')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('f.form', ':form'),
                    $query->expr()->eq('f.creationPerson', ':person'),
                    $query->expr()->eq('f.draft', 'true')
                )
            )
            ->setParameter('form', $form)
            ->setParameter('person', $person)
            ->orderBy('f.creationTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findOneByFormAndPerson(FormEntity $form, Person $person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('f')
            ->from('FormBundle\Entity\Node\Entry', 'f')
            ->orderBy('f.creationTime', 'DESC')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('f.form', ':form'),
                    $query->expr()->eq('f.creationPerson', ':person')
                )
            )
            ->setParameter('form', $form)
            ->setParameter('person', $person)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findAllByFormAndGuestInfoQuery(FormEntity $form, GuestInfoEntity $guestInfo)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('f')
            ->from('FormBundle\Entity\Node\Entry', 'f')
            ->orderBy('f.creationTime', 'DESC')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('f.form', ':form'),
                    $query->expr()->eq('f.guestInfo', ':guestInfo')
                )
            )
            ->setParameter('form', $form)
            ->setParameter('guestInfo', $guestInfo)
            ->orderBy('f.creationTime', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    public function findDraftVersionByFormAndGuestInfo(FormEntity $form, GuestInfoEntity $guestInfo)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('f')
            ->from('FormBundle\Entity\Node\Entry', 'f')
            ->orderBy('f.creationTime', 'DESC')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('f.form', ':form'),
                    $query->expr()->eq('f.guestInfo', ':guestInfo'),
                    $query->expr()->eq('f.draft', 'true')
                )
            )
            ->setParameter('form', $form)
            ->setParameter('guestInfo', $guestInfo)
            ->orderBy('f.creationTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findOneByFormAndGuestInfo(FormEntity $form, GuestInfoEntity $guestInfo)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('f')
            ->from('FormBundle\Entity\Node\Entry', 'f')
            ->orderBy('f.creationTime', 'DESC')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('f.form', ':form'),
                    $query->expr()->eq('f.guestInfo', ':guestInfo')
                )
            )
            ->setParameter('form', $form)
            ->setParameter('guestInfo', $guestInfo)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findCompletedByGroup(GroupEntity $group)
    {
        if (sizeof($group->getForms()) == 0) {
            return array();
        }

        $startEntries = $this->findAllByFormQuery($group->getForms()[0]->getForm())->getResult();

        $tmpEntries = array();
        foreach ($startEntries as $entry) {
            $tmpEntries[($entry->isGuestEntry() ? 'guest_' : 'person_') . $entry->getPersonInfo()->getId()] = $entry;
        }

        $endEntries = $this->findAllByFormQuery($group->getForms()[sizeof($group->getForms()) - 1]->getForm())->getResult();
        $entries = array();
        foreach ($endEntries as $entry) {
            if ($entry->isDraft()) {
                continue;
            }
            if (isset($tmpEntries[($entry->isGuestEntry() ? 'guest_' : 'person_') . $entry->getPersonInfo()->getId()])) {
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    public function findNotCompletedByGroup(GroupEntity $group)
    {
        if (sizeof($group->getForms()) == 0) {
            return array();
        }

        $endEntries = $this->findAllByFormQuery($group->getForms()[sizeof($group->getForms()) - 1]->getForm())->getResult();
        $tmpEntries = array();
        foreach ($endEntries as $entry) {
            $tmpEntries[($entry->isGuestEntry() ? 'guest_' : 'person_') . $entry->getPersonInfo()->getId()] = $entry;
        }

        $startEntries = $this->findAllByFormQuery($group->getForms()[0]->getForm())->getResult();

        $entries = array();
        foreach ($startEntries as $entry) {
            if (!isset($tmpEntries[($entry->isGuestEntry() ? 'guest_' : 'person_') . $entry->getPersonInfo()->getId()]) ||
                    ($tmpEntries[($entry->isGuestEntry() ? 'guest_' : 'person_') . $entry->getPersonInfo()->getId()]->isDraft())) {
                $entries[] = $entry;
            }
        }

        return $entries;
    }
}
