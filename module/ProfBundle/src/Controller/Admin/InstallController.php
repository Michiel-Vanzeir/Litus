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
 
namespace ProfBundle\Controller\Admin;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
	protected function _initConfig()
	{
	    $this->_installConfig(
	        array(
	            array(
	            	'key'         => 'union_url',
	            	'value'       => 'http://www.vtk.be',
	            	'description' => 'The URL of the union',
	            ),
	    	)
	    );
	}
	
	protected function _initAcl()
	{
	    $this->installAcl(
	        array(
	            'profbundle' => array(
	            )
	        )
	    );
	    
	    $this->installRoles(
	        array(
	            'prof' => array(
	            	'system' => true,
	                'parents' => array(
	                    'guest',
	                ),
	                'actions' => array(
	                )
	            )
	        )
	    );
	}
}