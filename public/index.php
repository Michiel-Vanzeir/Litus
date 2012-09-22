<?php

if ('development' == getenv('APPLICATION_ENV')) {
    ini_set('display_errors', true);
    error_reporting(E_ALL);
}

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Setup autoloading
include 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(include 'config/application.config.php')->run();