<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CudiBundle\Controller\Prof;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Form\Auth\Login as LoginForm,
    Zend\View\Model\ViewModel;

/**
 * AuthController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AuthController extends \CommonBundle\Component\Controller\ActionController
{
    public function loginAction()
    {
        $form = new LoginForm();
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
            
            if ($form->isValid($formData)) {
                $this->getAuthentication()->authenticate(
                    $formData['username'], $formData['password'], $formData['remember_me']
                );
                
                if ($this->getAuthentication()->isAuthenticated()) {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCESS',
                            'You are successfully logged in!'
                        )
                    );
                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'ERROR',
                            'You cannot be logged in!'
                        )
                    );
                }
            }
        }
                    
        $this->redirect()->toRoute(
            'prof_index',
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
                'You are successfully logged out!'
            )
        );
        
        $this->redirect()->toRoute(
            'prof_index',
            array(
                'language' => $this->getLanguage()->getAbbrev(),
            )
        );

        return new ViewModel();
    }
    
    public function shibbolethAction()
    {   
        if ((null !== $this->getParam('identification')) && (null !== $this->getParam('hash'))) {
            $authentication = new Authentication(
                new ShibbolethAdapter(
                    $this->getEntityManager(),
                    'CommonBundle\Entity\Users\People\Academic',
                    'universityIdentification'
                ),
                $this->getLocator()->get('authentication_doctrineservice')
            );
            
            $code = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Users\Shibboleth\Code')
                ->findLastByUniversityIdentification($this->getParam('identification'));
                
            if (null !== $code) { 
                if ($code->validate($this->getParam('hash'))) {
                    $this->getEntityManager()->remove($code);
                    $this->getEntityManager()->flush();
                    
                    $authentication->authenticate(
                        $this->getParam('identification'), '', true
                    );
                    
                    if ($authentication->isAuthenticated()) {
                        $this->redirect()->toRoute(
                            'prof_index'
                        );
                    }
                }
            }
        }
        
        return new ViewModel();
    }
}
