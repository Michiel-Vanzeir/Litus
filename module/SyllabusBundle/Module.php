<?php

namespace SyllabusBundle;

class Module
{
    /**
     * @return string
     */
    public function getConfig()
    {
        return include __DIR__ . '/Resources/config/module.config.php';
    }
}
