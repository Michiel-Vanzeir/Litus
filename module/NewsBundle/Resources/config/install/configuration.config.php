<?php

return array(
    array(
        'key'         => 'news.rss_title',
        'value'       => 'Student IT',
        'description' => 'The title of the RSS feed',
    ),
    array(
        'key'   => 'news.rss_description',
        'value' => serialize(
            array(
                'nl' => 'RSS Feed van de Student IT',
                'en' => 'RSS Feed of the Student IT',
            )
        ),
        'description' => 'The description of the RSS feed',
    ),
    array(
        'key'         => 'news.rss_image_link',
        'value'       => '/_site/img/logo.png',
        'description' => 'The image of the RSS feed',
    ),
    array(
        'key'         => 'news.max_age_site',
        'value'       => 'P3M',
        'description' => 'The maximum age of news items shown on the homepage',
    ),
);
