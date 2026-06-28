<?php
namespace local_chatbot;

defined('MOODLE_INTERNAL') || die();

/**
 * Event observer: gửi email thông báo tức thì khi có sự kiện quan trọng.
 */
class observer {

    /**
     * Gửi email cho toàn bộ sinh viên khi giáo viên tạo hoạt động mới trong khóa học.
     */
    public static function on_course_module_created(\core\event\course_module_created $event): void {
        global $DB;

        $courseId  = $event->courseid;
        $cmId      = $event->objectid;
        $creatorId = $event->userid;

        // Lay thong tin course module.
        $cm = get_coursemodule_from_id('', $cmId);
        if (!$cm) {
            return;
        }

        // Chi thong bao cac loai hoat dong hoc tap chinh (bo qua label, folder...).
        $notifyTypes = ['assign', 'quiz', 'forum', 'workshop', 'choice', 'scorm', 'lesson'];
        if (!in_array($cm->modname, $notifyTypes)) {
            return;
        }

        $course  = $DB->get_record('course', ['id' => $courseId]);
        $creator = $DB->get_record('user', ['id' => $creatorId]);
        if (!$course || !$creator) {
            return;
        }

        $modNames = [
            'assign'   => 'Bài tập',
            'quiz'     => 'Bài kiểm tra',
            'forum'    => 'Diễn đàn',
            'workshop' => 'Workshop',
            'choice'   => 'Bình chọn',
            'scorm'    => 'SCORM',
            'lesson'   => 'Bài học',
        ];
        $typeName = $modNames[$cm->modname] ?? ucfirst($cm->modname);

        // Lay danh sach sinh vien trong khoa.
        $students = self::get_course_students($courseId);
        if (empty($students)) {
            return;
        }

        $courseUrl = new \moodle_url('/course/view.php', ['id' => $courseId]);
        $subject   = "[Moodle] Hoạt động mới: {$cm->name} — {$course->fullname}";

        foreach ($students as $student) {
            $body = "Xin chào {$student->firstname} {$student->lastname},\n\n"
                . "Giảng viên vừa thêm hoạt động mới vào khóa học của bạn:\n\n"
                . "  Loại        : {$typeName}\n"
                . "  Tên         : {$cm->name}\n"
                . "  Khóa học    : {$course->fullname}\n\n"
                . "Truy cập khóa học: {$courseUrl}\n\n"
                . "Trân trọng,\nHệ thống Moodle";

            $userfrom = \core_user::get_noreply_user();
            email_to_user($student, $userfrom, $subject, $body);
        }
    }

    /**
     * Gửi email cho giáo viên khi được phân công vào khóa học.
     */
    public static function on_role_assigned(\core\event\role_assigned $event): void {
        global $DB;

        // Chi xu ly o context COURSE (contextlevel = 50).
        if ($event->contextlevel !== CONTEXT_COURSE) {
            return;
        }

        // Trong event role_assigned: role id nam o objectid (KHONG phai other['roleid']).
        $roleId   = $event->objectid;
        $userId   = $event->relateduserid;
        $courseId = $event->contextinstanceid;

        // Chi thong bao cho giao vien (editingteacher = 3, teacher = 4).
        $role = $DB->get_record('role', ['id' => $roleId]);
        if (!$role || !in_array($role->shortname, ['editingteacher', 'teacher'])) {
            return;
        }

        $teacher = $DB->get_record('user', ['id' => $userId, 'deleted' => 0]);
        $course  = $DB->get_record('course', ['id' => $courseId]);
        if (!$teacher || !$course) {
            return;
        }

        // Them cac truong phu de tranh warning.
        foreach (['firstnamephonetic','lastnamephonetic','middlename','alternatename','username'] as $f) {
            if (!isset($teacher->$f)) $teacher->$f = '';
        }

        $courseUrl = new \moodle_url('/course/view.php', ['id' => $courseId]);
        $subject   = "[Moodle] Bạn được phân công giảng dạy: {$course->fullname}";
        $body      = "Xin chào {$teacher->firstname} {$teacher->lastname},\n\n"
            . "Bạn vừa được phân công giảng dạy khóa học sau:\n\n"
            . "  Khóa học : {$course->fullname}\n"
            . "  Mã môn   : {$course->shortname}\n\n"
            . "Truy cập khóa học: {$courseUrl}\n\n"
            . "Trân trọng,\nHệ thống Moodle";

        $userfrom = \core_user::get_noreply_user();
        email_to_user($teacher, $userfrom, $subject, $body);

        // Hen gui email nhac nho lan 2 sau ~2 phut (adhoc task).
        $followup = new \local_chatbot\task\teacher_course_followup();
        $followup->set_custom_data([
            'teacherid' => (int) $teacher->id,
            'courseid'  => (int) $course->id,
        ]);
        $followup->set_next_run_time(time() + 120);
        \core\task\manager::queue_adhoc_task($followup);
    }

    /**
     * Helper: lấy danh sách sinh viên (role student) trong 1 khóa học.
     */
    private static function get_course_students(int $courseId): array {
        global $DB;

        $students = $DB->get_records_sql("
            SELECT DISTINCT u.id, u.firstname, u.lastname, u.email,
                   u.username, u.mailformat,
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
        ", ['courseid' => $courseId]);

        return array_values($students);
    }
}
