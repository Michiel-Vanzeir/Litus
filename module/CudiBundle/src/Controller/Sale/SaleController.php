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
 
namespace CudiBundle\Controller\Sale;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Sales\QueueItem,
	CudiBundle\Form\Sale\Sale\ReturnSale as ReturnSaleForm;

/**
 * SaleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SaleController extends \CudiBundle\Component\Controller\SaleController
{
    public function saleAction()
    {
        $barcodePrefix = $this->getEntityManager()
        	->getRepository('CommonBundle\Entity\General\Config')
        	->getConfigValue('cudi.queue_item_barcode_prefix');
        
    	return array(
    		'socketUrl' => $this->getSocketUrl(),
    		'barcodePrefix' => $barcodePrefix,
    	);
    }
    
    public function saveCommentAction()
    {
        if (!($queueItem = $this->_getQueueItem()))
            return;

        $queueItem->setComment($this->getRequest()->post()->get('comment'));
        $this->getEntityManager()->flush();
    
        return array(
            'result' => (object) array("status" => "success")
        );
    }
    
    public function returnAction()
    {
        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Session')
            ->findOneById($this->getParam('session'));
            
    	$form = new ReturnSaleForm($this->getEntityManager());
    	
    	if($this->getRequest()->isPost()) {
    	    $formData = $this->getRequest()->post()->toArray();
    		
    		if ($form->isValid($formData)) {
    			$person = $this->getEntityManager()
    				->getRepository('CommonBundle\Entity\Users\Person')
    				->findOneByUsername($formData['username']);
    				
    			$article = $this->getEntityManager()
    				->getRepository('CudiBundle\Entity\Sales\Article')
    				->findOneByBarcode($formData['article']);
    		
    			$booking = $this->getEntityManager()
    				->getRepository('CudiBundle\Entity\Sales\Booking')
    				->findOneSoldByPersonAndArticle($person, $article);
    			
    			if ($booking) {
    			    $saleItem = $this->getEntityManager()
			            ->getRepository('CudiBundle\Entity\Sales\SaleItem')
			            ->findOneByPersonAndArticle($person, $article);
                    
                    if ($saleItem) {
    			        if ($saleItem->getNumber() == 1) {
    			        	$this->getEntityManager()->remove($saleItem);
    			        } else {
    			        	$saleItem->setNumber($saleItem->getNumber() - 1);
    			        }
    			    }
    			    
    				if ($booking->getNumber() == 1) {
    					$this->getEntityManager()->remove($booking);
    				} else {
    					$booking->setNumber($booking->getNumber() - 1);
    				}
    				
    				$article->setStockValue($article->getStockValue() + 1);
    				
    				$this->getEntityManager()->flush();
    				
    				$this->flashMessenger()->addMessage(
    				    new FlashMessage(
    				        FlashMessage::SUCCESS,
    				        'SUCCESS',
    				        'The sale was successfully returned!'
    				    )
    				);
    			} else {
    				$this->flashMessenger()->addMessage(
    				    new FlashMessage(
    				        FlashMessage::ERROR,
    				        'ERROR',
    				        'The sale could not be returned!'
    				    )
    				);
    			}
    			
    			$this->redirect()->toRoute(
    				'sale_sale',
    				array(
    					'action' => 'return',
    					'session' => $session->getId(),
    				)
    			);
    			
    			return;
    		}
    	}
    	
    	return array(
    		'form' => $form,
    	);
    }
    
    private function _getQueueItem()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No ID was given to identify the queue item!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'sale_sale',
    			array(
    				'action' => 'sale'
    			)
    		);
    		
    		return;
    	}
    
        $queueItem = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $queueItem) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No queue item with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'sale_sale',
    			array(
    				'action' => 'sale'
    			)
    		);
    		
    		return;
    	}
    	
    	return $queueItem;
    }
}