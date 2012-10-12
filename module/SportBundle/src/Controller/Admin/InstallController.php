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

namespace SportBundle\Controller\Admin;

use CommonBundle\Entity\General\Language;

/**
 * InstallController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        $this->installConfig(
            array(
                array(
                    'key'         => 'sport.run_result_page',
                    'value'       => 'http://media.24u.ulyssis.org/live/tussenstand.xml',
                    'description' => 'The URL of the page where the XML of the official results is published',
                ),
                array(
                    'key'         => 'sport.run_team_id',
                    'value'       => '4',
                    'description' => 'The ID of the organization on the official result page',
                ),
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'sportbundle' => array(
                    'admin_run' => array(
                        'delete', 'queue', 'start', 'stop'
                    ),
                ),
            )
        );

        $this->installRoles(
            array(
                'guest' => array(
                    'system' => true,
                    'parents' => array(
                    ),
                    'actions' => array(
                    ),
                ),
            )
        );
    }
}
