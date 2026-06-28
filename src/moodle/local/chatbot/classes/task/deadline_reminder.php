<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

namespace local_chatbot\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Scheduled task: nhắc sinh viên về deadline sắp hết hạn (trong 24h tới).
 */
class deadline_reminder extends \core\task\scheduled_task {

    public function get_name(): string {
        return get_string('task_deadline_reminder', 'local_chatbot');
    }

    public function execute(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/lib/moodlelib.php');

        $now      = time();
        $window   = $now + (24 * 3600); // 24 giờ tới

        // Lấy tất cả assignment deadline sắp hết trong 24h.
        $assigns = $DB->get_records_sql("
            SELECT a.id, a.name, a.duedate, a.course,
                   c.fullname AS coursename
              FROM {assign} a
              JOIN {course} c ON c.id = a.course
             WHERE a.duedate > :now
               AND a.duedate <= :window
               AND a.duedate > 0
        ", ['now' => $now, 'window' => $window]);

        // Lấy tất cả quiz deadline sắp hết trong 24h.
        $quizzes = $DB->get_records_sql("
            SELECT q.id, q.name, q.timeclose AS duedate, q.course,
                   c.fullname AS coursename
              FROM {quiz} q
              JOIN {course} c ON c.id = q.course
             WHERE q.timeclose > :now
               AND q.timeclose <= :window
               AND q.timeclose > 0
        ", ['now' => $now, 'window' => $window]);

        $activities = array_merge(array_values($assigns), array_values($quizzes));

        if (empty($activities)) {
            mtrace('Deadline reminder: không có deadline nào trong 24h tới.');
            return;
        }

        $sent = 0;
        foreach ($activities as $act) {
            // Lấy sinh viên đang ghi danh khóa học này (role student = shortname 'student').
            $students = $DB->get_records_sql("
                SELECT DISTINCT u.id, u.firstname, u.lastname, u.email, u.mailformat
                  FROM {user} u
                  JOIN {user_enrolments} ue ON ue.userid = u.id
                  JOIN {enrol} e ON e.id = ue.enrolid
                  JOIN {role_assignments} ra ON ra.userid = u.id
                  JOIN {context} ctx ON ctx.id = ra.contextid
                         AND ctx.contextlevel = 50
                         AND ctx.instanceid = e.courseid
                  JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                 WHERE e.courseid = :courseid
                   AND ue.status = 0
                   AND e.status  = 0
                   AND u.deleted = 0
                   AND u.suspended = 0
                   AND u.email <> ''
            ", ['courseid' => $act->course]);

            // Them cac truong ten phu de email_to_user khong canh bao.
            foreach ($students as $s) {
                $s->firstnamephonetic = $s->firstnamephonetic ?? '';
                $s->lastnamephonetic  = $s->lastnamephonetic ?? '';
                $s->middlename        = $s->middlename ?? '';
                $s->alternatename     = $s->alternatename ?? '';
                $s->username          = $s->username ?? '';
            }

            $timeleft = round(($act->duedate - $now) / 3600, 1);
            $duestr   = date('d/m/Y H:i', $act->duedate);

            foreach ($students as $student) {
                $subject = "[Moodle] Nhắc nhở: Deadline sắp hết hạn — {$act->name}";
                $body    = "Xin chào {$student->firstname} {$student->lastname},\n\n"
                    . "Bạn còn khoảng {$timeleft} giờ để hoàn thành:\n\n"
                    . "  Hoạt động : {$act->name}\n"
                    . "  Khóa học  : {$act->coursename}\n"
                    . "  Hạn nộp   : {$duestr}\n\n"
                    . "Vui lòng đăng nhập Moodle để hoàn thành trước khi hết hạn.\n\n"
                    . "Trân trọng,\nHệ thống Moodle";

                $userfrom        = \core_user::get_noreply_user();
                $userfrom->customheaders = ['X-Auto-Response-Suppress: OOF, AutoReply'];

                email_to_user($student, $userfrom, $subject, $body);
                $sent++;
            }
        }

        mtrace("Deadline reminder: đã gửi {$sent} email nhắc nhở.");
    }
}
