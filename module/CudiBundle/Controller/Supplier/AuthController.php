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

namespace CudiBundle\Controller\Supplier;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Form\Auth\Login as LoginForm,
    Zend\View\Model\ViewModel;

/**
 * AuthController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AuthController extends \CudiBundle\Component\Controller\SupplierController
{
    public function loginAction()
    {
        $form = new LoginForm();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $this->getAuthentication()->authenticate(
                    $formData['username'], $formData['password'], $formData['remember_me']
                );

                if ($this->getAuthentication()->isAuthenticated()) {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCESS',
                            'You have been successfully logged in!'
                        )
                    );
                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'ERROR',
                            'You could not be logged in!'
                        )
                    );
                }
            }
        }

        $this->redirect()->toRoute(
            'cudi_supplier_index',
            array(
                'language' => $this->getLanguage()->getAbbrev(),
            )
        );

        return new ViewModel();
    }

    public function logoutAction()
    {
        $this->getAuthentication()->forget();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'You have been successfully logged out!'
            )
        );

        $this->redirect()->toRoute(
            'cudi_supplier_index',
            array(
                'language' => $this->getLanguage()->getAbbrev(),
            )
        );

        return new ViewModel();
    }
}