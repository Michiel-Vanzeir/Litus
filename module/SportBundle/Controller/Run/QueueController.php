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

namespace SportBundle\Controller\Run;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    SportBundle\Form\Queue\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * QueueController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class QueueController extends \SportBundle\Component\Controller\RunController
{
    public function indexAction()
    {
        $form = new AddForm();

        return new ViewModel(
            array(
                'form' => $form,
                'socketUrl' => $this->getSocketUrl(),
                'authSession' => $this->getAuthentication()
                    ->getSessionObject(),
                'key' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('sport.queue_socket_key'),
            )
        );
    }

    public function getNameAction()
    {
        $this->initAjax();

        if (8 == strlen($this->getParam('university_identification'))) {
            $runner = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Users\People\Academic')
                ->findOneByUniversityIdentification($this->getParam('university_identification'));

            if (null === $runner) {
                $runner = $this->getEntityManager()
                    ->getRepository('SportBundle\Entity\Runner')
                    ->findOneByRunnerIdentification($this->getParam('university_identification'));
            }

            if (null !== $runner) {
                return new ViewModel(
                    array(
                        'result' => (object) array(
                            'status' => 'success',
                            'firstName' => $runner->getFirstName(),
                            'lastName' => $runner->getLastName()
                        )
                    )
                );
            }
        }

        return new ViewModel();
    }

    /**
     * Returns the WebSocket URL.
     *
     * @return string
     */
    protected function getSocketUrl()
    {
        $address = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.queue_socket_remote_host');
        $port = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.queue_socket_port');

        return 'ws://' . $address . ':' . $port;
    }
}