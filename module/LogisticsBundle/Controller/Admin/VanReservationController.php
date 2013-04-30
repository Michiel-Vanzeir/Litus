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

namespace LogisticsBundle\Controller\Admin;

use LogisticsBundle\Form\Admin\VanReservation\Add as AddForm,
    DateTime,
    LogisticsBundle\Entity\Driver,
    LogisticsBundle\Form\Admin\VanReservation\Edit as EditForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    LogisticsBundle\Entity\Reservation\ReservableResource,
    LogisticsBundle\Entity\Reservation\VanReservation,
    Zend\View\Model\ViewModel;

class VanReservationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Reservation\VanReservation')
            ->findAllActive(),
            $this->getParam('page')
        );

        $current = $this->getAuthentication()->getPersonObject();
        if ($current != null) {
            $driver = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Driver')
                ->findOneById($current->getId());
            $isDriverLoggedIn = ($driver !== null);
        } else {
            $isDriverLoggedIn = false;
        }

        return new ViewModel(
            array(
                'currentUser' => $current,
                'isDriverLoggedIn' => $isDriverLoggedIn,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Reservation\VanReservation')
            ->findAllOld(),
            $this->getParam('page')
        );

        $current = $this->getAuthentication()->getPersonObject();
        if ($current != null) {
            $driver = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Driver')
                ->findOneById($current->getId());
            $isDriverLoggedIn = ($driver !== null);
        } else {
            $isDriverLoggedIn = false;
        }

        return new ViewModel(
            array(
                'currentUser' => $current,
                'isDriverLoggedIn' => $isDriverLoggedIn,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager(), $this->getCurrentAcademicYear());

        if($this->_handleAdd($form)) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::SUCCESS,
                    'SUCCES',
                    'The reservation was succesfully created!'
                )
            );

            $this->_doRedirect($reservation);

            return new ViewModel();
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function addasyncAction()
    {
        $form = new AddForm($this->getEntityManager(), $this->getCurrentAcademicYear());
        $this->initAjax();

        if ($this->_handleAdd($form)) {

            return new ViewModel(
                array(
                    'result' => (object) array("status" => "success"),
                )
            );
        }

        return new ViewModel();
    }

    private function _handleAdd(AddForm $form)
    {
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Users\People\Academic');

                $passenger = ('' == $formData['passenger_id'])
                    ? $repository->findOneByUsername($formData['passenger']) : $repository->findOneById($formData['passenger_id']);

                $repository = $this->getEntityManager()
                   ->getRepository('LogisticsBundle\Entity\Driver');

                $driver = $repository->findOneById($formData['driver']);

                $van = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Reservation\ReservableResource')
                    ->findOneByName(VanReservation::VAN_RESOURCE_NAME);

                if (null === $van) {
                    $van = new ReservableResource(VanReservation::VAN_RESOURCE_NAME);
                    $this->getEntityManager()->persist($van);
                }

                $reservation = new VanReservation(
                    DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']),
                    $formData['reason'],
                    $formData['load'],
                    $van,
                    $formData['additional_info'],
                    $this->getAuthentication()->getPersonObject()
                );

                $reservation->setDriver($driver);
                $reservation->setPassenger($passenger);

                $this->getEntityManager()->persist($reservation);
                $this->getEntityManager()->flush();

                return true;
            }
        }

        return false;
    }

    public function editAction()
    {
        if (!($reservation = $this->_getReservation()))
            return new ViewModel();

        $form = new EditForm(
            $this->getEntityManager(), $this->getCurrentAcademicYear(), $reservation
        );

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Users\People\Academic');

                $passenger = ('' == $formData['passenger_id'])
                    ? $repository->findOneByUsername($formData['passenger']) : $repository->findOneById($formData['passenger_id']);

                $repository = $this->getEntityManager()
                   ->getRepository('LogisticsBundle\Entity\Driver');

                $driver = $repository->findOneById($formData['driver']);

                $reservation->setStartDate(DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']))
                    ->setEndDate(DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']))
                    ->setReason($formData['reason'])
                    ->setLoad($formData['load'])
                    ->setAdditionalInfo($formData['additional_info'])
                    ->setDriver($driver)
                    ->setPassenger($passenger);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The reservation was successfully updated!'
                    )
                );

                $this->_doRedirect($reservation);

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($reservation = $this->_getReservation()))
            return new ViewModel();

        $this->getEntityManager()->remove($reservation);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    public function assignmeAction()
    {
        $this->initAjax();

        if (!($reservation = $this->_getReservation()))
            return new ViewModel();

        $person = $this->getAuthentication()->getPersonObject();
        $driver = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Driver')
            ->findOneById($person->getId());

        $reservation->setDriver($driver);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    "status" => "success",
                    "driver" => $person->getFullName(),
                ),
            )
        );
    }

    public function unassignmeAction()
    {
        $this->initAjax();

        if (!($reservation = $this->_getReservation()))
            return new ViewModel();

        $reservation->setDriver(null);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    "status" => "success",
                    "driver" => "",
                ),
            )
        );
    }

    private function _getReservation()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the reservation!'
                )
            );

            $this->_doRedirect();

            return;
        }

        $reservation = $this->getEntityManager()
        ->getRepository('LogisticsBundle\Entity\Reservation\VanReservation')
        ->findOneById($this->getParam('id'));

        if (null === $reservation) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No article with the given ID was found!'
                )
            );

            $this->_doRedirect();

            return;
        }

        return $reservation;
    }

    private function _doRedirect($reservation = null)
    {

        $controller = $this->getParam('return');

        if (null === $controller) {

            $controller = 'logistics_admin_van_reservation';
        }

        $params = array();
        if ($reservation) {
            $date = $reservation->getStartDate();
            $params['date'] = $date->format('Y') . '-' . ($date->format('n') - 1) . '-' . $date->format('j');
        }

        $this->redirect()->toRoute(
            $controller,
            $params
        );

        return;
    }
}