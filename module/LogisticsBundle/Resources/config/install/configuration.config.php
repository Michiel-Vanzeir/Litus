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

return array(
    array(
        'key'         => 'logistics.piano_auto_confirm_immediatly',
        'value'       => '1',
        'description' => 'Automatically confirm all piano reservations',
    ),
    array(
        'key'         => 'logistics.piano_auto_confirm_deadline',
        'value'       => 'P1D',
        'description' => 'The deadline for auto confirm a piano reservation',
    ),
    array(
        'key'         => 'logistics.piano_time_slot_duration',
        'value'       => '30',
        'description' => 'Duration of one time slot for a piano reservation in minutes',
    ),
    array(
        'key'         => 'logistics.piano_time_slot_max_duration',
        'value'       => '90',
        'description' => 'Maximum duration of one time slot for a piano reservation in minutes',
    ),
    array(
        'key'         => 'logistics.piano_reservation_max_in_advance',
        'value'       => 'P30D',
        'description' => 'Maximum days a reservation is possible in advance',
    ),
    array(
        'key'   => 'logistics.piano_time_slots',
        'value' => serialize(
            array(
                '1' => array(
                    array('start' => '19:00', 'end' => '22:00'),
                ), // Monday
                '2' => array(
                    array('start' => '19:00', 'end' => '22:00'),
                ), // Tuesday
                '3' => null, // Wednesday
                '4' => array(
                    array('start' => '19:00', 'end' => '22:00'),
                ), // Thursday
                '5' => null, // Friday
                '6' => null, // Saturday
                '7' => null, // Sunday
            )
        ),
        'description' => 'Available time slots for a piano reservation',
    ),
    array(
        'key'         => 'logistics.piano_mail_to',
        'value'       => 'vice@vtk.be',
        'description' => 'The mail address piano reservation mails are send to',
    ),
    array(
        'key'   => 'logistics.piano_new_reservation',
        'value' => serialize(
            array(
                'en' => array(
                    'subject' => 'New Piano Reservation',
                    'content' => 'Dear,

A new piano reservation was made:
{{ name }} from {{ start }} until {{ end }}.

It is important you always have the accompanying letter with you if you are going to play. You can get this in Blok 6 (Studentenwijk Arenberg) from the vice. You should be able to show this letter when security asks for it.

VTK

-- This is an automatically generated email, please do not reply --',
                ),
                'nl' => array(
                    'subject' => 'Nieuwe Piano Reservatie',
                    'content' => 'Beste,

Een nieuwe piano reservatie is aangemaakt:
{{ name }} van {{ start }} tot {{ end }}.

Het is belangrijk dat je de begeleidende brief steeds bij je hebt als je gaat spelen. Deze kan je gaan afhalen op blok 6 (Studentenwijk Arenberg) bij de vice. De brief moet je steeds kunnen voorleggen wanneer security er om vraagt.

VTK

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
                ),
            )
        ),
        'description' => 'The mail sent when a new piano reservation is created',
    ),
    array(
        'key'   => 'logistics.piano_new_reservation_confirmed',
        'value' => serialize(
            array(
                'en' => array(
                    'subject' => 'New Piano Reservation',
                    'content' => 'Dear,

A new piano reservation was made and confirmed:
{{ name }} from {{ start }} until {{ end }}.

It is important you always have the accompanying letter with you if you are going to play. You can get this in Blok 6 (Studentenwijk Arenberg) from the vice. You should be able to show this letter when security asks for it.

VTK

-- This is an automatically generated email, please do not reply --',
                ),
                'nl' => array(
                    'subject' => 'Nieuwe Piano Reservatie',
                    'content' => 'Beste,

Een nieuwe piano reservatie is aangemaakt en bevestigd:
{{ name }} van {{ start }} tot {{ end }}.

Het is belangrijk dat je de begeleidende brief steeds bij je hebt als je gaat spelen. Deze kan je gaan afhalen op blok 6 (Studentenwijk Arenberg) bij de vice. De brief moet je steeds kunnen voorleggen wanneer security er om vraagt.

VTK

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
                ),
            )
        ),
        'description' => 'The mail sent when a new piano reservation is created and confirmed',
    ),
    array(
        'key'         => 'logistics.icalendar_uid_suffix',
        'value'       => 'logistics.vtk.be',
        'description' => 'The suffix of an iCalendar event uid',
    ),
);
