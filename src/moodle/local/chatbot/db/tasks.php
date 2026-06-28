<?php
defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => '\local_chatbot\task\deadline_reminder',
        'blocking'  => 0,
        'minute'    => '0',   // chạy lúc xx:00
        'hour'      => '7',   // 7 giờ sáng mỗi ngày
        'day'       => '*',
        'month'     => '*',
        'dayofweek' => '*',
    ],
    [
        // Nhắc SV chưa nộp khi bài tập vừa hết hạn — chạy mỗi 5 phút.
        'classname' => '\local_chatbot\task\overdue_submission_reminder',
        'blocking'  => 0,
        'minute'    => '*/5',
        'hour'      => '*',
        'day'       => '*',
        'month'     => '*',
        'dayofweek' => '*',
    ],
];
