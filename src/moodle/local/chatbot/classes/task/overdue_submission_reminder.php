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
 * Scheduled task: nhắc sinh viên CHƯA NỘP bài khi assignment vừa hết hạn.
 *
 * Chạy định kỳ. Mỗi lần chạy chỉ xử lý các assignment có hạn nộp (duedate)
 * vừa trôi qua kể từ lần chạy trước -> mỗi bài chỉ gửi nhắc đúng 1 lần.
 */
class overdue_submission_reminder extends \core\task\scheduled_task {

    public function get_name(): string {
        return get_string('task_overdue_reminder', 'local_chatbot');
    }

    public function execute(): void {
        global $DB;

        $now = time();

        // Cua so xet: tu lan chay truoc den hien tai.
        // Lan chay dau tien (lastrun = 0) chi nhin lui toi da 24h de tranh spam lich su.
        $lastrun = $this->get_last_run_time();
        $since   = ($lastrun > 0) ? $lastrun : ($now - 24 * 3600);

        // Tim assignment co duedate vua qua trong khoang (since, now].
        $assigns = $DB->get_records_sql("
            SELECT a.id, a.name, a.duedate, a.course,
                   c.fullname AS coursename
              FROM {assign} a
              JOIN {course} c ON c.id = a.course
             WHERE a.duedate > :since
               AND a.duedate <= :now
        ", ['since' => $since, 'now' => $now]);

        if (empty($assigns)) {
            mtrace('Overdue reminder: không có bài tập nào vừa hết hạn.');
            return;
        }

        $sent = 0;
        foreach ($assigns as $assign) {
            // Danh sach SV trong khoa (role student).
            $students = $DB->get_records_sql("
                SELECT DISTINCT u.id, u.firstname, u.lastname, u.email, u.username,
                       u.mailformat,
                       '' AS firstnamephonetic, '' AS lastnamephonetic,
                       '' AS middlename, '' AS alternatename
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
            ", ['courseid' => $assign->course]);

            if (empty($students)) {
                continue;
            }

            // SV da nop (submitted/reopened, ban moi nhat).
            $submitted = $DB->get_fieldset_select(
                'assign_submission',
                'userid',
                "assignment = :aid AND latest = 1 AND status IN ('submitted', 'reopened')",
                ['aid' => $assign->id]
            );
            $submitted = array_flip($submitted);

            $duestr = date('d/m/Y H:i', $assign->duedate);

            // Danh sach SV chua nop (de gui cho GV).
            $unsubmittedNames = [];

            foreach ($students as $student) {
                if (isset($submitted[$student->id])) {
                    continue; // da nop -> bo qua
                }

                $unsubmittedNames[] = "{$student->firstname} {$student->lastname}";

                // (1) Email nhac chinh sinh vien chua nop.
                $subject = "[Moodle] Đã hết hạn nộp bài: {$assign->name}";
                $body    = "Xin chào {$student->firstname} {$student->lastname},\n\n"
                    . "Bài tập sau đã HẾT HẠN nộp và hệ thống ghi nhận bạn CHƯA nộp:\n\n"
                    . "  Bài tập   : {$assign->name}\n"
                    . "  Khóa học  : {$assign->coursename}\n"
                    . "  Hạn nộp   : {$duestr}\n\n"
                    . "Vui lòng liên hệ giảng viên hoặc nộp sớm nếu khóa học còn cho phép nộp muộn.\n\n"
                    . "Trân trọng,\nHệ thống Moodle";

                $userfrom = \core_user::get_noreply_user();
                email_to_user($student, $userfrom, $subject, $body);
                $sent++;
            }

            // (2) Email tong hop cho GIANG VIEN danh sach SV chua nop.
            if (!empty($unsubmittedNames)) {
                $sent += $this->notify_teachers($assign, $unsubmittedNames, $duestr, count($students));
            }
        }

        mtrace("Overdue reminder: đã gửi {$sent} email (sinh viên + giảng viên).");
    }

    /**
     * Gửi email cho giảng viên của khóa: danh sách SV chưa nộp bài.
     *
     * @param object   $assign            bản ghi assignment (kèm coursename)
     * @param string[] $unsubmittedNames  danh sách họ tên SV chưa nộp
     * @param string   $duestr            hạn nộp đã định dạng
     * @param int      $totalStudents     tổng số SV trong khóa
     * @return int số email đã gửi cho giảng viên
     */
    private function notify_teachers(object $assign, array $unsubmittedNames, string $duestr, int $totalStudents): int {
        global $DB;

        // Lay giang vien cua khoa (editingteacher + teacher).
        $teachers = $DB->get_records_sql("
            SELECT DISTINCT u.id, u.firstname, u.lastname, u.email, u.username,
                   u.mailformat,
                   '' AS firstnamephonetic, '' AS lastnamephonetic,
                   '' AS middlename, '' AS alternatename
              FROM {user} u
              JOIN {role_assignments} ra ON ra.userid = u.id
              JOIN {context} ctx ON ctx.id = ra.contextid
                     AND ctx.contextlevel = 50
                     AND ctx.instanceid = :courseid
              JOIN {role} r ON r.id = ra.roleid
             WHERE r.shortname IN ('editingteacher', 'teacher')
               AND u.deleted = 0
               AND u.suspended = 0
               AND u.email <> ''
        ", ['courseid' => $assign->course]);

        if (empty($teachers)) {
            return 0;
        }

        $count   = count($unsubmittedNames);
        $listText = '';
        foreach ($unsubmittedNames as $i => $name) {
            $listText .= '  ' . ($i + 1) . ". {$name}\n";
        }

        $sent = 0;
        foreach ($teachers as $teacher) {
            $subject = "[Moodle] {$count} sinh viên chưa nộp bài: {$assign->name}";
            $body    = "Xin chào {$teacher->firstname} {$teacher->lastname},\n\n"
                . "Bài tập sau đã HẾT HẠN nộp. Thống kê tình hình nộp bài:\n\n"
                . "  Bài tập    : {$assign->name}\n"
                . "  Khóa học   : {$assign->coursename}\n"
                . "  Hạn nộp    : {$duestr}\n"
                . "  Tổng SV    : {$totalStudents}\n"
                . "  Chưa nộp   : {$count}\n\n"
                . "Danh sách sinh viên CHƯA nộp:\n"
                . $listText . "\n"
                . "Trân trọng,\nHệ thống Moodle";

            $userfrom = \core_user::get_noreply_user();
            email_to_user($teacher, $userfrom, $subject, $body);
            $sent++;
        }

        return $sent;
    }
}
