<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    // Khi giao vien tao hoat dong moi (assign, quiz, page...) trong khoa hoc.
    [
        'eventname'   => '\core\event\course_module_created',
        'callback'    => '\local_chatbot\observer::on_course_module_created',
        'includefile' => null,
        'internal'    => false,
        'priority'    => 0,
    ],
    // Khi admin/giao vien duoc phan cong vao khoa hoc (role_assigned).
    [
        'eventname'   => '\core\event\role_assigned',
        'callback'    => '\local_chatbot\observer::on_role_assigned',
        'includefile' => null,
        'internal'    => false,
        'priority'    => 0,
    ],
];
