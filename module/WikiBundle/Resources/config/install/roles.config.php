<?php

return array(
    'guest' => array(
        'system' => true,
        'parents' => array(
        ),
        'actions' => array(
            'wiki_auth' => array(
                'login', 'logout', 'shibboleth'
            ),
        ),
    ),
);
