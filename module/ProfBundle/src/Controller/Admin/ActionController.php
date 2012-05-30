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
 
namespace ProfBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Articles\History,
    ProfBundle\Form\Admin\Article\Confirm as ArticleForm,
    ProfBundle\Form\Admin\File\Confirm as FileForm;

/**
 * ActionController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ActionController extends \CommonBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
        	$this->getEntityManager()
        	    ->getRepository('ProfBundle\Entity\Action')
        	    ->findAllUncompleted(),
            $this->getParam('page')
        );
        return array(
            'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true),
        );
    }
    
    public function completedAction()
    {
        $paginator = $this->paginator()->createFromArray(
        	$this->getEntityManager()
        	    ->getRepository('ProfBundle\Entity\Action')
        	    ->findAllCompleted(),
            $this->getParam('page')
        );
        return array(
            'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true),
        );
    }
    
    public function refusedAction()
    {
        $paginator = $this->paginator()->createFromArray(
        	$this->getEntityManager()
        	    ->getRepository('ProfBundle\Entity\Action')
        	    ->findAllRefused(),
            $this->getParam('page')
        );
        return array(
            'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true),
        );
    }
    
    public function viewAction()
    {
        if (!($action = $this->_getAction()))
            return;
        
        $action->setEntityManager($this->getEntityManager());
        
        return array(
            'action' => $action,
        );
    }
    
    public function refuseAction()
    {
        if (!($action = $this->_getAction()))
            return;
        
        $action->setRefused($this->getAuthentication()->getPersonObject());
        
        $this->getEntityManager()->flush();
        
        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Success',
                'The action is successfully refused!'
            )
        );
        
        
        $this->redirect()->toRoute(
        	'admin_action',
        	array(
        		'action' => 'refused'
        	)
        );
    }
    
    public function confirmAction()
    {
        if (!($action = $this->_getAction()))
            return;
        
        $action->setEntityManager($this->getEntityManager());
        
        if ($action->getEntityName() == 'article') {
            if ($action->getAction() == 'add') {
                $this->redirect()->toRoute(
                	'admin_action',
                	array(
                		'action' => 'confirmArticle',
                		'id' => $action->getId(),
                	)
                );
                return;
            } else {
                $edited = $action->getEntity();
                $current = $action->getPreviousEntity();
                $duplicate = $current->duplicate();
                
                $current->setTitle($edited->getTitle())
                    ->setAuthors($edited->getAuthors())
                    ->setPublishers($edited->getPublishers())
                    ->setYearPublished($edited->getYearPublished())
                    ->setISBN($edited->getISBN())
                    ->setURL($edited->getURL());
                    
                $edited->setTitle($duplicate->getTitle())
                    ->setAuthors($duplicate->getAuthors())
                    ->setPublishers($duplicate->getPublishers())
                    ->setYearPublished($duplicate->getYearPublished())
                    ->setISBN($duplicate->getISBN())
                    ->setURL($duplicate->getURL())
                    ->setIsProf(false);
                
                if ($current->isStock()) {
                    if ($current->isInternal()) {
                        $current->setBinding($edited->getBinding())
                            ->setIsRectoVerso($edited->isRectoVerso())
                            ->setIsPerforated($edited->isPerforated());
                            
                        $edited->setBinding($duplicate->getBinding())
                            ->setIsRectoVerso($duplicate->isRectoVerso())
                            ->setIsPerforated($duplicate->isPerforated());
                	}
                }
                
                $history = new History($this->getEntityManager(), $current, $edited);
                $this->getEntityManager()->persist($history);
                
                $action->setEntityId($current->getId())
                    ->setPreviousId($edited->getId());
            }
        } elseif ($action->getEntityName() == 'mapping') {
            if ($action->getAction() == 'add') {
                $action->getEntity()->setIsProf(false);
            } else {
                $action->getEntity()->setRemoved();
            }
        } elseif ($action->getEntityName() == 'file') {
            if ($action->getAction() == 'add') {
                $this->redirect()->toRoute(
                	'admin_action',
                	array(
                		'action' => 'confirmFile',
                		'id' => $action->getId(),
                	)
                );
                return;
            } else {
                $action->getEntity()->setRemoved();
            }
        }
        
        $action->setCompleted($this->getAuthentication()->getPersonObject());
        
        $this->getEntityManager()->flush();
        
        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Success',
                'The action is successfully confirmed!'
            )
        );
        
        $this->redirect()->toRoute(
        	'admin_action',
        	array(
        		'action' => 'completed'
        	)
        );
    }
    
    public function confirmArticleAction()
    {
        if (!($action = $this->_getAction()))
            return;
        
        $action->setEntityManager($this->getEntityManager());
            
        $form = new ArticleForm($this->getEntityManager(), $action->getEntity());
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
        	    $action->getEntity()->setTitle($formData['title'])
        	        ->setAuthors($formData['author'])
        	        ->setPublishers($formData['publisher'])
        	        ->setYearPublished($formData['year_published'])
        	        ->setISBN($formData['isbn'])
        	        ->setURL($formData['url']);
        	    
        	    if ($formData['stock']) {
					if ($formData['internal']) {
						$binding = $this->getEntityManager()
							->getRepository('CudiBundle\Entity\Articles\Options\Binding')
							->findOneById($formData['binding']);

						$frontPageColor = $this->getEntityManager()
							->getRepository('CudiBundle\Entity\Articles\Options\Color')
							->findOneById($formData['front_color']);
                        
                        $action->getEntity()->setNbBlackAndWhite($formData['nb_black_and_white'])
                        	->setNbColored($formData['nb_colored'])
                        	->setBinding($binding)
                        	->setIsOfficial($formData['official'])
                        	->setIsRectoVerso($formData['rectoverso'])
                        	->setFrontColor($frontPageColor)
                        	->setFrontPageTextColored($formData['front_text_colored'])
                        	->setIsPerforated($formData['perforated']);
					}
				}
				
				$action->getEntity()->setIsProf(false);
        	    
        	    $action->setCompleted($this->getAuthentication()->getPersonObject());
        	    
        	    $this->getEntityManager()->flush();
        	    
        	    $this->redirect()->toRoute(
        	    	'admin_action',
        	    	array(
        	    		'action' => 'completed'
        	    	)
        	    );
        	    return;
        	}
        }
        
        return array(
            'form' => $form,
        );
    }
    
    public function confirmFileAction()
    {
        if (!($action = $this->_getAction()))
            return;
        
        $action->setEntityManager($this->getEntityManager());
            
        $form = new FileForm($action->getEntity());
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
        	    $action->getEntity()
        	        ->setPrintable($formData['printable'])
        	        ->getFile()->setDescription($formData['description']);
				
				$action->getEntity()->setIsProf(false);
        	    
        	    $action->setCompleted($this->getAuthentication()->getPersonObject());
        	    
        	    $this->getEntityManager()->flush();
        	    
        	    $this->redirect()->toRoute(
        	    	'admin_action',
        	    	array(
        	    		'action' => 'completed'
        	    	)
        	    );
        	    return;
        	}
        }
        
        return array(
            'form' => $form,
        );
    }
    
    public function _getAction()
    {
        if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the action!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_action',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $action = $this->getEntityManager()
            ->getRepository('ProfBundle\Entity\Action')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $action) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No action with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_action',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $action;
    }
}