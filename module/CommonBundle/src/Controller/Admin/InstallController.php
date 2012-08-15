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

namespace CommonBundle\Controller\Admin;

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
                    'key'         => 'common.profile_path',
                    'value'       => 'data/images/profile',
                    'description' => 'The path for profile photo\'s',
                ),
                array(
                    'key'         => 'search_max_results',
                    'value'       => '30',
                    'description' => 'The maximum number of search results shown',
                ),
                array(
                    'key'         => 'account_deactivated_mail',
                    'value'       => 'Dear,

Your account on Litus is deactivated.
Click here to activate it again: http://litus/account/activate/code/{{ code }}',
                    'description' => 'The email sent when an account is deactivated',
                ),
                array(
                    'key'         => 'account_deactivated_subject',
                    'value'       => 'Account deactivated',
                    'description' => 'The mail subject when an account is deactivated',
                ),
                array(
                    'key'         => 'system_mail_address',
                    'value'       => 'info@litus.cc',
                    'description' => 'The system mail address',
                ),
                array(
                    'key'         => 'system_mail_name',
                    'value'       => 'Litus Project',
                    'description' => 'The system mail name',
                ),
                array(
                    'key'         => 'account_activated_mail',
                    'value'       => 'Dear {{ name }},

An account for you is created on Litus with username {{ username }}.
Click here to activate it: http://litus/account/activate/code/{{ code }}',
                    'description' => 'The email sent when an account is deactivated',
                ),
                array(
                    'key'         => 'account_activated_subject',
                    'value'       => 'Account created',
                    'description' => 'The mail subject when an account is deactivated',
                ),
                array(
                    'key'         => 'start_organization_year',
                    'value'       => '{{ year }}-7-15 0:0:0',
                    'description' => 'The start date of the organization year',
                ),
                array(
                    'key'         => 'shibboleth_url',
                    'value'       => 'https://arianna.vtk.be/Shibboleth.sso/Login?target=https%3A%2F%2Farianna.vtk.be%2Fshibboleth%2F',
                    'description' => 'The Shibboleth authentication URL, wherein the target parameter specifies the redirect',
                ),
                array(
                    'key'         => 'shibboleth_person_key',
                    'value'       => 'Shib-Person-uid',
                    'description' => 'The key in the $_SERVER array that accesses the university identification',
                ),
                array(
                    'key'         => 'shibboleth_session_key',
                    'value'       => 'Shib-Session-ID',
                    'description' => 'The key in the $_SERVER array that accesses the shibboleth session',
                ),
                array(
                    'key'         => 'shibboleth_code_handler_url',
                    'value'       => serialize(
                        array(
                            'admin' => 'https://dev.vtk.be/admin/auth/shibboleth',
                            'prof'  => 'https://dev.vtk.be/cudi/prof/auth/shibboleth',
                            'site'  => 'https://dev.vtk.be/auth/shibboleth',
                        )
                    ),
                    'description' => 'The Shibboleth handler URL, without a trailing slash',
                ),
                array(
                    'key'         => 'shibboleth_code_expiration_time',
                    'value'       => '300',
                    'description' => 'The amount of time during which the generated Shibboleth code is valid (in seconds)',
                ),
                array(
                    'key'         => 'system_administrator_mail',
                    'value'       => 'it@vtk.be',
                    'description' => 'The mail address of system administrator',
                ),
                array(
                    'key'         => 'fallback_language',
                    'value'       => 'nl',
                    'description' => 'The abbreviation of the language that will be used of no other translation is present',
                ),
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'commonbundle' => array(
                    'admin_academic' => array(
                        'add', 'delete', 'edit', 'manage', 'search', 'typeahead'
                    ),
                    'admin_auth' => array(
                        'authenticate', 'login', 'logout'
                    ),
                    'admin_config' => array(
                        'edit', 'manage'
                    ),
                    'admin_index' => array(
                        'index'
                    ),
                    'admin_role' => array(
                        'add', 'edit', 'delete', 'manage'
                    ),
                    'index' => array(
                        'index'
                    ),
                )
            )
        );

        $this->installRoles(
            array(
                'guest' => array(
                    'system' => true,
                    'parents' => array(
                    ),
                    'actions' => array(
                        'admin_auth' => array(
                            'authenticate', 'login', 'logout'
                        ),
                        'index' => array(
                            'index'
                        ),
                    ),
                ),
            )
        );
    }
}
