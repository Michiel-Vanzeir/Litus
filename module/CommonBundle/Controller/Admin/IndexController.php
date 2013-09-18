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

namespace CommonBundle\Controller\Admin;

use CommonBundle\Component\Piwik\Analytics,
    DateInterval,
    DateTime,
    Zend\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class IndexController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function indexAction()
    {
        $piwikEnabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.piwik_enabled');

        $piwik = null;
        if ($piwikEnabled) {
            $analytics = new Analytics(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.piwik_api_url'),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.piwik_token_auth'),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.piwik_id_site')
            );

            $piwik = array(
                'uniqueVisitors' => $analytics->getUniqueVisitors(),
                'liveCounters' => $analytics->getLiveCounters(),
                'visitsGraph' => $this->_getVisitsGraph($analytics)
            );
        }

        $registrationEnabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.registration_enabled');

        $registrationsGraph = null;
        if ($registrationEnabled)
            $registrationsGraph = $this->_getRegistrationsGraph();

        $profActions = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Prof\Action')
            ->findAllUncompleted(10);

        $subjectComments = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Comment')
            ->findLast(10);

        $subjectReplies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Reply')
            ->findLast(10);

        $activeSessions = array();
        if ($this->getAuthentication()->isAuthenticated()) {
            $activeSessions = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Session')
                ->findAllActiveByPerson($this->getAuthentication()->getPersonObject());
        }

        $currentSession = $this->getAuthentication()->getSessionObject();

        return new ViewModel(
            array(
                'profActions' => $profActions,
                'subjectComments' => $subjectComments,
                'subjectReplies' => $subjectReplies,
                'activeSessions' => $activeSessions,
                'currentSession' => $currentSession,
                'piwik' => $piwik,
                'registrationsGraph' => $registrationsGraph,
                'versions' => array(
                    'php' => phpversion(),
                    'zf' => \Zend\Version\Version::VERSION,
                    'doctrine' => \Doctrine\Common\Version::VERSION
                ),
            )
        );
    }

    private function _getVisitsGraph(Analytics $analytics)
    {
        if (null !== $this->getCache()) {
            if($this->getCache()->hasItem('CommonBundle_Controller_IndexController_VisitsGraph')) {
                $now = new DateTime();
                if ($this->getCache()->getItem('CommonBundle_Controller_IndexController_VisitsGraph')['expirationTime'] > $now)
                    return $this->getCache()->getItem('CommonBundle_Controller_IndexController_VisitsGraph');
            }

            $this->getCache()->setItem(
                'CommonBundle_Controller_IndexController_VisitsGraph',
                $this->_getVisitsGraphData($analytics)
            );

            return $this->getCache()->getItem('CommonBundle_Controller_IndexController_VisitsGraph');
        }

        return $this->_getVisitsGraphData($analytics);
    }

    private function _getVisitsGraphData(Analytics $analytics)
    {
        $now = new DateTime();

        $visitsGraphData = array(
            'expirationTime' => $now->add(new DateInterval('P1D')),

            'labels' => array(),
            'dataset' => array()
        );

        foreach ((array) $analytics->getUniqueVisitors('previous7') as $dateString => $count) {
            $date = new DateTime($dateString);

            $visitsGraphData['labels'][] = $date->format('d/m/Y');
            $visitsGraphData['dataset'][] = $count;
        }

        return $visitsGraphData;
    }

    private function _getRegistrationsGraph()
    {
        if (null !== $this->getCache()) {
            if($this->getCache()->hasItem('CommonBundle_Controller_IndexController_RegistrationGraph')) {
                $now = new DateTime();
                if ($this->getCache()->getItem('CommonBundle_Controller_IndexController_RegistrationGraph')['expirationTime'] > $now)
                    return $this->getCache()->getItem('CommonBundle_Controller_IndexController_RegistrationGraph');
            }

            $this->getCache()->setItem(
                'CommonBundle_Controller_IndexController_RegistrationGraph',
                $this->_getRegistrationsGraphData()
            );

            return $this->getCache()->getItem('CommonBundle_Controller_IndexController_RegistrationGraph');
        }

        return $this->_getRegistrationsGraphData();
    }

    private function _getRegistrationsGraphData()
    {
        $now = new DateTime();

        $registationGraphData = array(
            'expirationTime' => $now->add(new DateInterval('PT1H')),

            'labels' => array(),
            'dataset' => array()
        );

        $registrations = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Registration')
            ->findBy(
                array(
                    'academicYear' => $this->getCurrentAcademicYear()
                ),
                array(
                    'timestamp' => 'ASC'
                )
            );

        $data = array();
        foreach ($registrations as $registration) {
            if (!isset($data[$registration->getTimestamp()->format('d/m/Y')]) && count($data) <= 7) {
                $data[$registration->getTimestamp()->format('d/m/Y')] = 1;
            } else {
                $data[$registration->getTimestamp()->format('d/m/Y')]++;
            }
        }

        foreach($data as $label => $value) {
            $registationGraphData['labels'][] = $label;
            $registationGraphData['dataset'][] = $value;
        }

        return $registationGraphData;
    }
}
