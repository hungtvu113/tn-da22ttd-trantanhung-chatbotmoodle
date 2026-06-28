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
 * Adhoc task: nhắc giáo viên vào khóa học mới để cập nhật/chỉnh sửa.
 *
 * Được hẹn chạy ~2 phút sau khi giáo viên được phân công vào khóa học
 * (queue trong observer::on_role_assigned với set_next_run_time(now + 120)).
 */
class teacher_course_followup extends \core\task\adhoc_task {

    public function get_name(): string {
        return get_string('task_teacher_followup', 'local_chatbot');
    }

    public function execute(): void {
        global $DB;

        $data = $this->get_custom_data();
        if (empty($data) || empty($data->teacherid) || empty($data->courseid)) {
            return;
        }

        $teacher = $DB->get_record('user', ['id' => $data->teacherid, 'deleted' => 0, 'suspended' => 0]);
        $course  = $DB->get_record('course', ['id' => $data->courseid]);
        if (!$teacher || !$course || empty($teacher->email)) {
            return;
        }

        // Them cac truong phu de tranh debugging warning cua fullname().
        foreach (['firstnamephonetic', 'lastnamephonetic', 'middlename', 'alternatename', 'username'] as $f) {
            if (!isset($teacher->$f)) {
                $teacher->$f = '';
            }
        }

        $editurl   = new \moodle_url('/course/view.php', ['id' => $course->id]);
        $subject   = "[Moodle] Nhắc nhở: Hoàn thiện khóa học {$course->fullname}";
        $body      = "Xin chào {$teacher->firstname} {$teacher->lastname},\n\n"
            . "Bạn vừa được phân công giảng dạy khóa học \"{$course->fullname}\".\n"
            . "Vui lòng nhanh chóng truy cập khóa học để cập nhật và chỉnh sửa nội dung:\n\n"
            . "  - Cập nhật mô tả, đề cương khóa học\n"
            . "  - Thêm tài liệu, bài giảng\n"
            . "  - Tạo các hoạt động (bài tập, bài kiểm tra)\n"
            . "  - Kiểm tra danh sách sinh viên ghi danh\n\n"
            . "Truy cập khóa học: {$editurl}\n\n"
            . "Trân trọng,\nHệ thống Moodle";

        $userfrom = \core_user::get_noreply_user();
        email_to_user($teacher, $userfrom, $subject, $body);

        mtrace("Teacher followup: đã gửi nhắc nhở cho GV {$teacher->email} (khóa {$course->shortname}).");
    }
}
