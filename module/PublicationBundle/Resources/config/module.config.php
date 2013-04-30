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
            'publication_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/publication[/]',
                    'defaults' => array(
                        'controller' => 'publication_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'publication_admin_publication' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/publication[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'publication_admin_publication',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'publication_admin_edition_pdf' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/edition/pdf[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'publication_admin_edition_pdf',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'publication_admin_edition_html' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/edition/html[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'publication_admin_edition_html',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'publication_archive' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/archive[/:action[/:publication[/:year]]][/]',
                    'constraints' => array(
                        'action'      => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'publication' => '[0-9]*',
                        'year'        => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'publication_archive',
                        'action'     => 'overview',
                    ),
                ),
            ),
            'publication_edition_html' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/archive/html[/:action[/:id]][/]',
                    'constraints' => array(
                        'action'      => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'          => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'publication_edition_html',
                        'action'     => 'view',
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_files' => array(
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/site.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/site.nl.php',
                'locale'   => 'nl'
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'publication_view' => __DIR__ . '/../views',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'PublicationBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'publicationbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'publication_install'             => 'PublicationBundle\Controller\Admin\InstallController',

            'publication_admin_publication'   => 'PublicationBundle\Controller\Admin\PublicationController',
            'publication_admin_edition_pdf'   => 'PublicationBundle\Controller\Admin\Edition\PdfController',
            'publication_admin_edition_html'  => 'PublicationBundle\Controller\Admin\Edition\HtmlController',

            'publication_archive'             => 'PublicationBundle\Controller\Archive\ArchiveController',
            'publication_edition_html'        => 'PublicationBundle\Controller\Edition\HtmlController',
        ),
    ),
    'assetic_configuration' => array(
        'modules'      => array(
            'publicationbundle' => array(
                'root_path' => __DIR__ . '/../assets',
                'collections' => array(
                    'archive_css' => array(
                        'assets' => array(
                            'archive/less/archive.less'
                        ),
                        'filters' => array(
                            'archive_less' => array(
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
                            'output' => 'archive_css.css',
                        ),
                    ),
                ),
            ),
        ),
    ),
);