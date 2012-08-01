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

/**
 * A little PHP script to enable Shibboleth authentication, when
 * the server's hostname is registered as the IP.
 */
 
chdir(dirname(__DIR__));

// @NOTE: you can either use the git submodule or create an environment variable
// ZF2_PATH that contains the path to your zf2 library (no trailing slash). 
require_once (getenv('ZF2_PATH') ?: 'vendor/ZendFramework/library') . '/Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory(
 array('Zend\Loader\StandardAutoloader' => array())
);

$appConfig = include 'config/application.config.php';

$listenerOptions  = new Zend\Module\Listener\ListenerOptions($appConfig['module_listener_options']);
$defaultListeners = new Zend\Module\Listener\DefaultListenerAggregate($listenerOptions);
$defaultListeners->getConfigListener()->addConfigGlobPath('config/autoload/*.config.php');

$moduleManager = new Zend\Module\Manager($appConfig['modules']);
$moduleManager->events()->attachAggregate($defaultListeners);
$moduleManager->loadModules();

$config = $defaultListeners->getConfigListener()->getMergedConfig();

$diContainer = new \Zend\Di\Di();
$diContainer->instanceManager()->addTypePreference('Zend\Di\Locator', $diContainer);
$diConfig = new \Zend\Di\Configuration($config->di);
$diConfig->configure($diContainer);

$em = $diContainer->get('doctrine_em');

if (isset($_SERVER['Shib-Person-uid'], $_SERVER['Shib-Session-ID'])) {
    $checkCode = $em->getRepository('CommonBundle\Entity\Users\Shibboleth\Code')
        ->findOneByCode(substr($_SERVER['Shib-Session-ID'], 1));
        
    if (null !== $checkCode)
        break 2;

    $newCode = new CommonBundle\Entity\Users\Shibboleth\Code(
        $_SERVER['Shib-Person-uid'],
        substr($_SERVER['Shib-Session-ID'], 1),
        $em->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_code_expiration_time')
    );
    
    $em->persist($newCode);
    $em->flush();
}

$shibbolethHandler = $em->getRepository('CommonBundle\Entity\General\Config')
    ->getConfigValue('shibboleth_code_handler_url');
    
if ('/' == substr($shibbolethHandler, -1))
    $shibbolethHandler = substr($shibbolethHandler, 0, -1);

http_response_code(307);
header(
    'Location: ' . $shibbolethHandler . (isset($newCode) ? '/code/' . $newCode->hash() : '')
);
