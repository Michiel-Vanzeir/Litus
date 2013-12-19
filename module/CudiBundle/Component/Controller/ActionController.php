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

namespace CudiBundle\Component\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ActionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    /**
     * Returns the current academic year.
     *
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    protected function getAcademicYear()
    {
        $date = null;
        if (null !== $this->getParam('academicyear'))
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        $academicYear = AcademicYear::getOrganizationYear($this->getEntityManager(), $date);

        if (null === $academicYear) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic year was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $academicYear;
    }

    /**
     * Returns the active stock period.
     *
     * @return \CudiBundle\Entity\Stock\Period
     */
    protected function getActiveStockPeriod()
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        if (null === $period) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'There is no active stock period!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_period',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $period->setEntityManager($this->getEntityManager());
        return $period;
    }
}
