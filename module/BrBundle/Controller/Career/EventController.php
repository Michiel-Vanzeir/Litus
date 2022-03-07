<?php

namespace BrBundle\Controller\Career;

use BrBundle\Entity\Event;
use BrBundle\Entity\Event\Match;
use BrBundle\Entity\Event\Subscription;
use BrBundle\Entity\Event\Visitor;
use BrBundle\Entity\User\Person\Corporate;
use CommonBundle\Entity\User\Person\Academic;
use CudiBundle\Form\Admin\Sale\Article\View;
use DateTime;
use Laminas\Mail\Message;
use Laminas\Mime\Mime;
use Laminas\Mime\Part;
use Laminas\View\Model\ViewModel;

/**
 * EventController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Belian Callaerts <belian.callaerts@vtk.be>
 */
class EventController extends \BrBundle\Component\Controller\CareerController
{
    public function overviewAction()
    {
        $events = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event')
            ->findAllActiveCareerQuery()->getResult();

        return new ViewModel(
            array(
                'events' => $events,
            )
        );
    }

    // public function fetchAction()
    // {
    //     $this->initAjax();

    //     $events = $this->getEvents();

    //     if ($events === null) {
    //         return $this->notFoundAction();
    //     }

    //     $result = array();
    //     foreach ($events as $event) {
    //         $result[] = array (
    //             'start' => $event->getStartDate()->getTimeStamp(),
    //             'end'   => $event->getEndDate()->getTimeStamp(),
    //             'title' => $event->getTitle(),
    //             'id'    => $event->getId(),
    //         );
    //     }

    //     return new ViewModel(
    //         array(
    //             'result' => (object) array(
    //                 'status' => 'success',
    //                 'events' => (object) $result,
    //             ),
    //         )
    //     ); 
    // }

    public function viewAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $guide = $event->getGuide();

        $hasGuide = !is_null($guide);

        $hasBusschema = !is_null($event->getBusschema());

        return new ViewModel(
            array(
                'event' => $event,
                'hasGuide' => $hasGuide,
                'hasBusschema' => $hasBusschema,
            )
        );
    }

    // public function searchAction()
    // {
    //     $this->initAjax();

    //     $events = $this->getEntityManager()
    //         ->getRepository('BrBundle\Entity\Company\Event')
    //         ->findAllFutureBySearch(new DateTime(), $this->getParam('string'));

    //     $result = array();
    //     foreach ($events as $event) {
    //         $item = (object) array();
    //         $item->id = $event->getId();
    //         $item->poster = $event->getEvent()->getPoster();
    //         $item->title = $event->getEvent()->getTitle($this->getLanguage());
    //         $item->companyName = $event->getCompany()->getName();
    //         // TODO: Localization
    //         $item->startDate = $event->getEvent()->getStartDate()->format('d/m/Y h:i');
    //         $item->summary = $event->getEvent()->getSummary(400, $this->getLanguage());
    //         $result[] = $item;
    //     }

    //     return new ViewModel(
    //         array(
    //             'result' => $result,
    //         )
    //     );
    // }


    public function subscribeAction()
    {
        if ($this->getAuthentication()->isAuthenticated()) {
            $person = $this->getAuthentication()->getPersonObject();
        } else {
            $person = null;
        }

        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_career_event_subscription_add', array('event' => $event));

        if ($person instanceof Academic) {
            //TODO: Check for double subscriptions??

            $data = array();
            $data['first_name'] = $person->getFirstName();
            $data['last_name'] = $person->getLastName();
            $form->setData($data);
        }
        

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $subscription = $form->hydrateObject();
                $subscription->setEvent($event);
                $this->getEntityManager()->persist($subscription);
                $this->getEntityManager()->flush();

                $this->sendMail($event, $subscription);

                $this->flashMessenger()->success(
                    'Success',
                    'You have succesfully subscribed for this event!'
                );

                $this->redirect()->toRoute(
                    'br_career_event',
                    array(
                        'action' => 'view',
                        'id'     => $event->getId(),
                    )
                );

                return new ViewModel(
                    array(
                        'event' => $event,
                    )
                );
            }
        }

        

        return new ViewModel(
            array(
                'event' => $event,
                'form'  => $form,
            )
        );
    }

    public function mapAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $attendingCompaniesMaps = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\CompanyMap')
            ->findAllByEventQuery($event)
            ->getResult();

        $interestedMasters = array();
        foreach ($attendingCompaniesMaps as $companyMap) {
            $interestedMasters[$companyMap->getCompany()->getId()] = $companyMap->getMasterInterests();
        }

        $locations = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\Location')
            ->findAllByEventQuery($event)
            ->getResult();

        
        return new ViewModel(
            array(
                'event'             => $event,
                'locations'         => $locations,
                'interestedMasters' => $interestedMasters,
                'masters'           => Subscription::POSSIBLE_STUDIES
            )
        );
    }

    public function guideAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $guide = $event->getGuide();

        $publicPdfDir = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('publication.public_pdf_directory');

        return new ViewModel(
            array(
                'event' => $event,
                'guide' => $guide,
                'publicPdfDir' => $publicPdfDir,
            )
        );
    }

    public function busschemaAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $busschema = $event->getBusschema();

        $imageDir = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('publication.public_pdf_directory');

        return new ViewModel(
            array(
                'event' => $event,
                'busschema' => $busschema,
                'publicPdfDir' => $imageDir,
            )
        );
    }

    public function qrAction()
    {   
        $qr = $this->getParam('code');
        if ($qr === null) {
            return new ViewModel();
        }

        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

       
        if ($this->getAuthentication()->isAuthenticated()) {
            $person = $this->getAuthentication()->getPersonObject();
        }

        // If someone is logged in
        if ($person != null) {
            $subscription = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Event\Subscription')
                ->findOneByQREvent($event, $qr)[0];
            
            // Check whether person is affiliated to a company
            if ($person instanceof Corporate) {
                $companyMap = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Event\CompanyMap')
                    ->findByEventAndCompany($event, $person->getCompany());
                
                // If company is at event
                if ($companyMap != null) {
                    
                    // Check whether match already exists
                    $match = $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Event\Match')
                        ->findByMapAndSubscription($companyMap, $subscription);

                    if ($match == null) {
                        $match = new Match($companyMap, $subscription);
                        $this->getEntityManager()->persist(
                            $match
                        );
                        $this->getEntityManager()->flush();
                        $duplicate = false;
                    } else {
                        $match = $match[0];
                        $duplicate = true;
                    }
                    
                    return new ViewModel(
                        array(
                            'event'     => $event,
                            'match'     => $match,
                            'duplicate' => $duplicate,
                        )
                    );
                }
            } 
            
            if ($this->hasAccess()->toResourceAction('br_career_event', 'scanQr')) {
                // Check whether the person can use the scanQr for the entry scanning
                $visitor = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Event\Visitor')
                    ->findByEventAndQrAndExitNull($event, $qr);
                
                $previousVisits = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Event\Visitor')
                    ->findByEventAndQr($event, $qr);

                if ($visitor == null) {
                    // If there is no such result, then the person must be entering
                    $entry = true;

                    $visitor = new Visitor($event, $qr);
                    $this->getEntityManager()->persist(
                        $visitor
                    );
                    $this->getEntityManager()->flush();

                    $color = unserialize(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('br.study_colors')
                    )[$subscription->getStudy()];
                    
                    $textColor = unserialize(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('br.study_text_colors')
                    )[$subscription->getStudy()];
                } else {
                    // Otherwise, the person is exiting
                    $entry = false;
                    $visitor[0]->setExitTimestamp(new DateTime());

                    $this->getEntityManager()->flush();
                }

                

                return new ViewModel(
                    array(
                        'event'        => $event,
                        'subscription' => $subscription,
                        'entry'        => $entry,
                        'firstTime'    => ($previousVisits == null),
                        'color'        => $color,
                        'textColor'    => $textColor
                    )
                );
            }
        }

        // This should only be reached when there is either no person logged in or that person has no special access

        $encodedUrl = urlencode(
            $this->url()
                ->fromRoute(
                    'br_career_event',
                    array('action' => 'qr',
                        'id'    => $event->getId(),
                        'code'     => $qr
                    ),
                    array('force_canonical' => true)
                )
        );

        $encodedUrl = str_replace('leia.', '', $encodedUrl);

        $qrSource = str_replace(
            '{{encodedUrl}}',
            $encodedUrl,
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.google_qr_api')
        );

        return new ViewModel(
            array(
                'event'    => $event,
                'qrSource' => $qrSource,
            )
        );
    }

    public function overviewMatchesAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        if ($this->getAuthentication()->isAuthenticated()) {
            $person = $this->getAuthentication()->getPersonObject();
        }

        if ($person === null || !($person instanceof Corporate)) {
            $this->flashMessenger()->error(
                'Error',
                "You are not logged in and can't view any matches!"
            );

            $this->redirect()->toRoute(
                'br_career_event',
                array(
                    'action' => 'view',
                    'id'     => $event->getId(),
                )
            );
            return new ViewModel();
        }

        $companyMap = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\CompanyMap')
            ->findByEventAndCompany($event, $person->getCompany());

        $matches = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\Match')
            ->findAllByCompanyMapQuery($companyMap)
            ->getResult();

        
        return new ViewModel(
            array(
                'event'   => $event,
                'matches' => $matches,
            )
        );
    }

    public function removeMatchAction()
    {
        $this->initAjax();
        $match = $this->getMatchEntity();
        if ($match === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($match);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function sendMail(Event $event, Subscription $subscription)
    {
        $language = $this->getLanguage();
        $entityManager = $this->getEntityManager();
        if ($language === null) {
            $language = $entityManager->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('en');
        }
        

        $mailData = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.subscription_mail_data')
        );

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = str_replace('{{event}}', $event->getTitle(), $mailData[$language->getAbbrev()]['subject']);

        $mailAddress = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.subscription_mail');

        $mailName = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.subscription_mail_name');
        
        $url = $this->url()
            ->fromRoute(
                'br_career_event',
                array('action' => 'qr',
                    'id'       => $event->getId(),
                    'code'     => $subscription->getQrCode()
                ),
                array('force_canonical' => true)
            );

        $url = str_replace('leia.', '', $url);
                
        $qrSource = str_replace(
            '{{encodedUrl}}',
            urlencode($url),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.google_qr_api')
        );

        $message = str_replace('{{event}}', $event->getTitle(), $message);
        $message = str_replace('{{eventDate}}', $event->getStartDate()->format('d/m/Y'), $message);
        $message = str_replace('{{qrSource}}', $qrSource, $message);
        $message = str_replace('{{qrLink}}', $url, $message);
        $message = str_replace('{{brMail}}', $mailAddress, $message);

        $part = new Part($message);

        $part->type = Mime::TYPE_HTML;
        $part->charset = 'utf-8';
        $newMessage = new \Laminas\Mime\Message();
        $newMessage->addPart($part);

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody($newMessage)
            ->setFrom($mailAddress, $mailName)
            ->addTo($subscription->getEmail(), $subscription->getFirstName().' '.$subscription->getLastName())
            ->setSubject($subject);

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }

    /**
     * @return array
     */
    private function getEvents()
    {
        if ($this->getParam('start') === null || $this->getParam('end') === null) {
            return;
        }

        $startTime = new DateTime();
        $startTime->setTimeStamp($this->getParam('start'));

        $endTime = new DateTime();
        $endTime->setTimeStamp($this->getParam('end'));

        $events = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event')
            ->findAllByDates($startTime, $endTime);

        if (count($events) == 0) {
            $events = array();
        }

        return $events;
    }

    /**
     * @return Event|null
     */
    private function getEventEntity()
    {
        $event = $this->getEntityById('BrBundle\Entity\Event');

        if (!($event instanceof Event)) {
            $this->flashMessenger()->error(
                'Error',
                'No event was found!'
            );

            $this->redirect()->toRoute(
                'br_career_event',
                array(
                    'action' => 'overview',
                )
            );

            return;
        }

        return $event;
    }

    /**
     * @return Match|null
     */
    private function getMatchEntity()
    {
        $event = $this->getEventEntity();
        $match = $this->getEntityById('BrBundle\Entity\Event\Match', 'match');

        if (!($match instanceof Match)) {
            $this->flashMessenger()->error(
                'Error',
                'No match was found!'
            );

            $this->redirect()->toRoute(
                'br_career_event',
                array(
                    'action' => 'view',
                    'event'  => $event->getId(),
                )
            );

            return;
        }

        return $match;
    }
}
