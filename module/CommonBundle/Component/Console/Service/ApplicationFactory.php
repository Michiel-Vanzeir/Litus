<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Console\Service;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface,
    CommonBundle\Component\Version\Version,
    Interop\Container\ContainerInterface,
    Raven_ErrorHandler,
    Symfony\Component\Console\Application,
    Symfony\Component\Console\ConsoleEvents,
    Symfony\Component\Console\Event\ConsoleErrorEvent,
    Symfony\Component\EventDispatcher\EventDispatcher,
    Zend\ServiceManager\Factory\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to instantiate a console application.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ApplicationFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Application
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $application = new Application('Litus', Version::getCommitHash());
        $application->setCatchExceptions(true);
        $application->setAutoExit(false);

        if ('production' == getenv('APPLICATION_ENV')) {
            $events = new EventDispatcher();
            $events->addListener(ConsoleEvents::ERROR, array($services->get('sentry'), 'logConsoleErrorEvent'));

            $application->setDispatcher($events);

            $errorHandler = new Raven_ErrorHandler($services->get('raven_client'));
            $errorHandler->registerErrorHandler()
                ->registerExceptionHandler()
                ->registerShutdownFunction();
        }

        $this->addCommands($application, $container);

        return $application;
    }

    /**
     * @param  ServiceLocatorInterface $locator
     * @return Application
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, 'CommonBundle\Component\Console');
    }

    /**
     * @param  Application        $application
     * @param  ContainerInterface $container
     * @return void
     */
    private function addCommands(Application $application, ContainerInterface $container)
    {
        $config = $container->get('Config');
        $config = $config['litus']['console'];

        $commands = array();

        foreach ($config as $name => $invokable) {
            $container->setInvokableClass('console_' . $name, $invokable);

            $command = $container->get('console_' . $name);
            if ($command instanceof ServiceLocatorAwareInterface) {
                $command->setServiceLocator($container);
            }

            $commands[$name] = $command;
        }

        $application->addCommands(array_values($commands));
    }
}
