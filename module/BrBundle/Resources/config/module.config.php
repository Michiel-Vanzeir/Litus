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

return array(
    'router' => array(
        'routes' => array(
            'br_install' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/br[/]',
                    'defaults' => array(
                        'controller' => 'br_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'br_admin_company' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/company[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[a-zA-Z0-9_-]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'br_admin_company',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'br_admin_company_event' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/company/event[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'br_admin_company_event',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'br_admin_company_job' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/company/job[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'br_admin_company_job',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'br_admin_company_user' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/company/user[/:action[/:id]][/page/:page][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'br_admin_company_user',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'br_admin_company_logo' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/company/logos[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'br_admin_company_logo',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'br_admin_cv_entry' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/cv/entry[/:action[/:id][/page/:page][/:academicyear]][/]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'           => '[0-9]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'page'         => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'br_admin_cv_entry',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'br_admin_section' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/section[/:action[/:id[/:confirm]]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'confirm' => '[01]',
                    ),
                    'defaults' => array(
                        'controller' => 'br_admin_section',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'br_career_index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/career[/:action][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'br_career_index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'br_career_company' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/career/company[/:action[/:company][/id/:id]][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'company'     => '[a-zA-Z0-9_-]*',
                        'language' => '[a-z]{2}',
                        'id'      => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'br_career_company',
                        'action'     => 'overview',
                    ),
                ),
            ),
            'br_career_company_search' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/career/company/search[/:string][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string'   => '[%a-zA-Z0-9:.,_-]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'br_career_company',
                        'action'     => 'search',
                    ),
                ),
            ),
            'br_career_event' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/career/event[/:action[/:id]][/page/:page][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9_-]*',
                        'language' => '[a-z]{2}',
                        'page'     => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'br_career_event',
                        'action'     => 'overview',
                    ),
                ),
            ),
            'br_career_vacancy' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/career/vacancy[/:action[/:id]][/page/:page][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9_-]*',
                        'language' => '[a-z]{2}',
                        'page'     => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'br_career_vacancy',
                        'action'     => 'overview',
                    ),
                ),
            ),
            'br_career_internship' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/career/internship[/:action[/:id]][/page/:page][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9_-]*',
                        'language' => '[a-z]{2}',
                        'page'     => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'br_career_internship',
                        'action'     => 'overview',
                    ),
                ),
            ),
            'br_career_file' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/career/company/file/:name[/]',
                    'constraints' => array(
                        'name'     => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'br_career_company',
                        'action'     => 'file',
                    ),
                ),
            ),
            'br_corporate_index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/corporate[/:action][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'language' => '[a-z]{2}',
                        'image'    => '[a-zA-Z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'br_corporate_index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'br_corporate_cv' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/corporate/cv[/:action[/type/:type][/string/:string][/min/:min][/max/:max][/image/:image][/:academicyear]][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                        'language' => '[a-z]{2}',
                        'image'    => '[a-zA-Z0-9]*',
                        'type'     => '[a-zA-Z]*',
                        'string'   => '[%a-zA-Z0-9:.,_-]*',
                        'min'      => '[0-9]*',
                        'max'      => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'br_corporate_cv',
                        'action'     => 'grouped',
                    ),
                ),
            ),
            'br_corporate_auth' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/corporate/auth[/:action][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'session'  => '[0-9]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'br_corporate_auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            'br_cv_index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/cv[/:action][/]',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'br_cv_index',
                        'action'     => 'cv',
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_files' => array(
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/corporate.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/corporate.nl.php',
                'locale'   => 'nl'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/career.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/career.nl.php',
                'locale'   => 'nl'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/cv.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/cv.nl.php',
                'locale'   => 'nl'
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'br_layout' => __DIR__ . '/../layouts',
            'br_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'BrBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'brbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'br_install'               => 'BrBundle\Controller\Admin\InstallController',

            'br_admin_company'         => 'BrBundle\Controller\Admin\CompanyController',
            'br_admin_company_event'   => 'BrBundle\Controller\Admin\Company\EventController',
            'br_admin_company_job'     => 'BrBundle\Controller\Admin\Company\JobController',
            'br_admin_company_user'    => 'BrBundle\Controller\Admin\Company\UserController',
            'br_admin_company_logo'    => 'BrBundle\Controller\Admin\Company\LogoController',
            'br_admin_cv_entry'        => 'BrBundle\Controller\Admin\CvController',
            'br_admin_section'         => 'BrBundle\Controller\Admin\SectionController',

            'br_corporate_index'       => 'BrBundle\Controller\Corporate\IndexController',
            'br_corporate_cv'          => 'BrBundle\Controller\Corporate\CvController',
            'br_corporate_auth'        => 'BrBundle\Controller\Corporate\AuthController',

            'br_career_index'          => 'BrBundle\Controller\Career\IndexController',
            'br_career_vacancy'        => 'BrBundle\Controller\Career\VacancyController',
            'br_career_internship'     => 'BrBundle\Controller\Career\InternshipController',
            'br_career_event'          => 'BrBundle\Controller\Career\EventController',
            'br_career_company'        => 'BrBundle\Controller\Career\CompanyController',

            'br_cv_index'              => 'BrBundle\Controller\CvController',
        ),
    ),
    'assetic_configuration' => array(
        'modules' => array(
            'brbundle' => array(
                'root_path' => __DIR__ . '/../assets',
                'collections' => array(
                    'corporate_css' => array(
                        'assets' => array(
                            'corporate/less/base.less',
                        ),
                        'filters' => array(
                            'corporate_less' => array(
                                'name' => 'Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'corporate_css.css',
                        ),
                    ),
                    'career_css' => array(
                        'assets' => array(
                            'career/less/career.less',
                        ),
                        'filters' => array(
                            'career_less' => array(
                                'name' => 'Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'career_css.css',
                        ),
                    ),
                    'cv_css' => array(
                        'assets' => array(
                            'cv/less/cv.less',
                        ),
                        'filters' => array(
                            'cv_less' => array(
                                'name' => 'Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'cv_css.css',
                        ),
                    ),
                ),
            ),
        ),
    ),
);