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

if (getenv('APPLICATION_ENV') != 'development') {
    if (!extension_loaded('redis')) {
        throw new RuntimeException('Litus requires the Redis extension to be loaded');
    }

    if (!file_exists(__DIR__ . '/../redis.config.php')) {
        throw new RuntimeException(
            'The Redis configuration file (' . (__DIR__ . '/../redis.config.php') . ') was not found'
        );
    }

    $redisConfig = include __DIR__ . '/../redis.config.php';

    return array(
        'cache' => array(
            'storage' => array(
                'adapter' => array(
                    'name'    => 'redis',
                    'options' => array(
                        'ttl'       => 0,
                        'namespace' => 'cache:litus',

                        'database'      => $redisConfig['database'],
                        'lib_options'   => $redisConfig['lib_options'],
                        'password'      => $redisConfig['password'],
                        'persistent_id' => $redisConfig['persistent_id'],
                        'server'        => array(
                            'host'    => $redisConfig['host'],
                            'port'    => $redisConfig['port'],
                            'timeout' => $redisConfig['timeout'],
                        ),
                    ),
                ),
            ),
        ),
    );
}

return array(
    'cache' => array(
        'storage' => array(
            'adapter' => array(
                'name'    => 'memory',
                'options' => array(
                    'ttl'       => 0,
                    'namespace' => 'Litus',
                ),
            ),
        ),
    ),
);
