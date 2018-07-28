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

namespace FormBundle\Component\Controller;

use CommonBundle\Component\Controller\ActionController\Exception\ShibbolethUrlException,
    CommonBundle\Component\Controller\Exception\HasNoAccessException,
    Zend\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class FormController extends \CommonBundle\Component\Controller\ActionController
{
    /**
     * Execute the request.
     *
     * @param  MvcEvent             $e The MVC event
     * @return array
     * @throws HasNoAccessException The user does not have permissions to access this resource
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        $result->loginForm = $this->getForm('common_auth_login')
            ->setAttribute('class', '')
            ->setAttribute('action', $this->url()->fromRoute(
                'form_manage_auth',
                array(
                    'action' => 'login',
                )
            ));
        $result->organizationUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_url');
        $result->shibbolethUrl = $this->getShibbolethUrl();

        $e->setResult($result);

        return $result;
    }

    /**
     * We need to be able to specify all required authentication information,
     * which depends on the part of the site that is currently being used.
     *
     * @return array
     */
    public function getAuthenticationHandler()
    {
        return array(
            'action'     => 'index',
            'controller' => 'common_index',

            'auth_route'     => 'form_manage',
            'redirect_route' => 'form_manage',
        );
    }

    /**
     * Create the full Shibboleth URL.
     *
     * @return string
     * @throws ShibbolethUrlException
     */
    private function getShibbolethUrl()
    {
        $shibbolethUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_url');

        try {
            if (false !== ($shibbolethUrl = unserialize($shibbolethUrl))) {
                if (false === getenv('SERVED_BY')) {
                    throw new ShibbolethUrlException('The SERVED_BY environment variable does not exist');
                }
                if (!isset($shibbolethUrl[getenv('SERVED_BY')])) {
                    throw new ShibbolethUrlException('Array key ' . getenv('SERVED_BY') . ' does not exist');
                }

                $shibbolethUrl = $shibbolethUrl[getenv('SERVED_BY')];
            }
        } catch (\ErrorException $e) {
            // No load balancer active
        }

        $shibbolethUrl .= '?source=form';

        $server = $this->getRequest()->getServer();
        if (isset($server['HTTP_HOST']) && isset($server['REQUEST_URI'])) {
            $shibbolethUrl .= '%26redirect=' . urlencode('https://' . $server['HTTP_HOST'] . $server['REQUEST_URI']);
        }

        return $shibbolethUrl;
    }
}
