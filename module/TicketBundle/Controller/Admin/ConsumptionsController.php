<?php

namespace TicketBundle\Controller\Admin;

use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use DateTime;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;
use TicketBundle\Entity\Consumptions;
use TicketBundle\Entity\Transactions;

/**
 * ConsumptionsController
 */
class ConsumptionsController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Consumptions')
                ->findAll(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function consumeAction()
    {
        $form = $this->getForm('ticket_consumptions_consume');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $data = $form->getData()['consume'];

                $entity = $this->getConsumptionsEntity();

                if ($entity instanceof Consumptions) {
                    $person = $this->getPersonEntity();

                    $transaction = new Transactions(-$data, $entity->getPerson(), $person);
                    $this->getEntityManager()->persist($transaction);
                }

                if ($entity->getConsumptions() - $data < 0) {
                    $this->flashMessenger()->error(
                        'Error',
                        'Not enough consumptions left!'
                    );

                    $this->redirect()->toRoute(
                        'ticket_admin_consumptions',
                        array(
                            'action' => 'manage',
                        )
                    );
                    return new ViewModel();
                }

                if ($entity->getConsumptions() - $data === 0) {
                    $entity->removeConsumptions($data);

                    $this->getEntityManager()->remove($entity);
                    $this->getEntityManager()->flush();

                    $this->redirect()->toRoute(
                        'ticket_admin_consumptions',
                        array(
                            'action' => 'manage',
                        )
                    );
                    return new ViewModel();
                }

                $entity->removeConsumptions($data);

                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'ticket_admin_consumptions',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('ticket_consumptions_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $consumption = $form->hydrateObject();
                $this->getEntityManager()->persist(
                    $consumption
                );
//                $this->getEntityManager()->persist($form);

                if ($consumption instanceof Consumptions) {
                    $person = $this->getPersonEntity();

                    $transaction = new Transactions($form->getData()['number_of_consumptions'], $consumption->getPerson(), $person);
                    $this->getEntityManager()->persist($transaction);
                }
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The consumptions were succesfully created!'
                );

                $this->redirect()->toRoute(
                    'ticket_admin_consumptions',
                    array(
                        'action' => 'add',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        $consumptions = $this->getConsumptionsEntity();
        if ($consumptions === null) {
            return new ViewModel();
        }
        $old = $consumptions->getConsumptions();
        $form = $this->getForm('ticket_consumptions_edit', array('consumptions' => $consumptions));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                if ($consumptions instanceof Consumptions) {
                    $person = $this->getPersonEntity();
                    $transaction = new Transactions($form->getData()['number_of_consumptions'] - $old, $consumptions->getPerson(), $person);
                    $this->getEntityManager()->persist($transaction);
                }
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The consumptions were succesfully updated!'
                );

                $this->redirect()->toRoute(
                    'ticket_admin_consumptions',
                    array(
                        'action' => 'manage',
                    )
                );

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

        $consumptions = $this->getConsumptionsEntity();

        if ($consumptions === null) {
            return new ViewModel();
        }

        if ($consumptions instanceof Consumptions) {
            $person = $this->getPersonEntity();
            $transaction = new Transactions(-$consumptions->getConsumptions(), $consumptions->getPerson(), $person);
            $this->getEntityManager()->persist($transaction);
        }

        $this->getEntityManager()->remove($consumptions);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'succes'),
            )
        );
    }

    public function deleteAllAction()
    {
        $allConsumptions = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Consumptions')
            ->findAll();

        foreach ($allConsumptions as $consumption) {
            $this->getEntityManager()->remove($consumption);
        }
        $this->getEntityManager()->flush();

        $this->redirect()->toRoute(
            'ticket_admin_consumptions',
            array(
                'action' => 'manage',
            )
        );
        return new ViewModel();
    }

    public function transactionsAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Transactions')
                ->findAll(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function totalTransactionsAction()
    {
        $date = new DateTime('now');

        $newDate = str_replace(
            '{{ currentDate }}',
            $date->format('d-m-Y'),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('ticket.transactions_refresh_date')
        );
        $dateToCheck = new DateTime($newDate);
        $period = new \DateInterval('P1D');
        if ($date->format('h-i-s') < $dateToCheck->format('h-i-s')) {
            $dateToCheck->sub($period);
        }

        $transactions = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Transactions')
            ->findAllSinceDate($dateToCheck);

        $total = 0;
        foreach ($transactions as $transaction) {
            $total += $transaction->getAmount();
        }

        return new ViewModel(
            array(
                'total' => $total,
            )
        );
    }

    private function getConsumptionsEntity()
    {
        $consumptions = $this->getEntityById('TicketBundle\Entity\Consumptions');

        if (!($consumptions instanceof Consumptions)) {
            $this->flashMessenger()->error(
                'Error',
                'No consumptions were found!'
            );

            $this->redirect()->toRoute(
                'ticket_admin_consumptions',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $consumptions;
    }

    public function searchAction()
    {
        $this->initAjax();
        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $consumptions = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($consumptions as $consumption) {
            $item = (object) array();
            $item->id = $consumption->getId();
            $item->name = $consumption->getFullName();
            $item->username = $consumption->getUserName();
            $item->consumptions = $consumption->getConsumptions();

            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function csvAction()
    {
        $form = $this->getForm('ticket_consumptions_csv');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $fileData = $this->getRequest()->getFiles();

            $fileName = $fileData['file']['tmp_name'];

            $consumptionsArray = array();

            $open = fopen($fileName, 'r');
            if ($open != false) {
                $data = fgetcsv($open, 1000, ',');
                while ($data !== false) {
                    $consumptionsArray[] = $data;
                    $data = fgetcsv($open, 1000, ',');
                }
                fclose($open);
            }

            $form->setData($formData);

            if ($form->isValid()) {
                $count = 0;
                foreach ($consumptionsArray as $key => $data) {
                    if (in_array(null, $data)) {
                        continue;
                    }
                    if ($key == '0') {
                        continue;
                    }

                    $consumption = new Consumptions();
                    $person = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person')
                        ->findOneByUsername($data[0]);
                    $consumption->setPerson($person);
                    $consumption->setConsumptions($data[1]);
                    $consumption->setUserName($person->getUserName());
                    $consumption->setFullName($person->getFullName());

                    $this->getEntityManager()->persist($consumption);
                    $count += 1;
                }
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    $count . ' consumptions were successfully added!'
                );

                $this->redirect()->toRoute(
                    'ticket_admin_consumptions',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form
            ),
        );
    }

    public function templateAction()
    {
        $file = new CsvFile();
        $heading = array(
            'r-number',
            'amount',
        );

        $results = array();
        $results[] = array(
            'r0000000',
            0,
        );

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="consumptions_template.csv"',
                'Content-Type'        => 'text/csv',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    private function search()
    {
        switch ($this->getParam('field')) {
            case 'username':
                return $this->getEntityManager()
                    ->getRepository('TicketBundle\Entity\Consumptions')
                    ->findAllByUserNameQuery($this->getParam('string'));
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('TicketBundle\Entity\Consumptions')
                    ->findAllByNameQuery($this->getParam('string'));
        }
    }

    /**
     * @return \CommonBundle\Entity\User\Person|null
     */
    private function getPersonEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return;
        }

        return $this->getAuthentication()->getPersonObject();
    }
}
