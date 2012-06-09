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
 
return array(
	'controllers'  => array(
		'cudi_install' => array(
			'@common_jquery',
			'@admin_css',
			'@admin_js',
		),
		'admin_article' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
		),
		'admin_article_subject' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
			'@common_typeahead_remote',
		),
		'admin_article_comment' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
		),
		'admin_article_file' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@common_form_upload_progress',
		    '@common_download_file',
		    '@common_permanent_modal',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
		),
		'admin_sales_article' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
		),
		'admin_sales_discount' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
		),
		'admin_sales_booking' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
		),
		'admin_sales_session' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		),
		'admin_sales_financial' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		),
		'admin_supplier' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		),
		'admin_supplier_user' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
		),
		'admin_stock' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
		),
		'admin_stock_period' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
		),
		'admin_stock_order' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@common_download_file',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
		    '@supplier_nav',
		),
		'admin_stock_delivery' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
		    '@supplier_nav',
		),
		'admin_stock_retour' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
		    '@supplier_nav',
		),
		/*
		'sale_sale' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@sale_css',
			'@sale_js',
			'@queue_js',
			'@bootstrap_js_transition',
			'@bootstrap_js_modal',
			'@bootstrap_js_alert',
			'@common_permanent_modal',
			'@common_socket',
		),
		'sale_queue' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@sale_css',
			'@queue_js',
			'@bootstrap_js_alert',
			'@common_socket',
		),*/
		'supplier_index' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@bootstrap_js_dropdown',
			'@bootstrap_js_alert',
			'@supplier_css',
		),
		'supplier_article' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@bootstrap_js_dropdown',
			'@bootstrap_js_alert',
			'@supplier_css',
		),
	),
	'routes' => array(),
);
