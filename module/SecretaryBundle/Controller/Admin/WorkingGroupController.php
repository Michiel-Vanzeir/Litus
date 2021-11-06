<?php

namespace SecretaryBundle\Controller\Admin;

use CommonBundle\Entity\User\Person\Academic;
use Laminas\View\Model\ViewModel;

class WorkingGroupController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CommonBundle\Entity\User\Person\Academic',
            $this->getParam('page'),
            array(
                'isInWorkingGroup' => true,
            ),
            array(
                'firstName' => 'ASC',
                'lastName'  => 'ASC',
            )
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('secretary_workingGroup_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $academic = $form->hydrateObject();

                $academic->setIsInWorkingGroup(true);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The person was succesfully added!'
                );

                $this->redirect()->toRoute(
                    'secretary_admin_working_group',
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

    public function deleteAction()
    {
        $this->initAjax();

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $academic->setIsInWorkingGroup(false);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $academics = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($academics as $academic) {
            $item = (object) array();
            $item->id = $academic->getId();
            $item->name = $academic->getFullName();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findAllWorkingGroupMembersByNameQuery($this->getParam('string'));
        }
    }

    /**
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        $academic = $this->getEntityById('CommonBundle\Entity\User\Person\Academic');

        if (!($academic instanceof Academic)) {
            $this->flashMessenger()->error(
                'Error',
                'No academic was found!'
            );

            $this->redirect()->toRoute(
                'common_admin_academic',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academic;
    }
}
