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
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Language strings for local_chatbot.
 *
 * @package    local_chatbot
 * @copyright  2026 Khoa luan tot nghiep
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Trợ lý học tập (Chatbot RAG)';

// Settings.
$string['settings_apiurl'] = 'Địa chỉ Backend RAG';
$string['settings_apiurl_desc'] = 'URL đầy đủ tới endpoint /chat của backend Python (ví dụ: http://localhost:8001/api/chat).';
$string['settings_apikey'] = 'API Key';
$string['settings_apikey_desc'] = 'Khoá bí mật để xác thực giữa Moodle plugin và backend RAG.';
$string['settings_enabled'] = 'Bật chatbot';
$string['settings_enabled_desc'] = 'Hiển thị nút chat nổi trên tất cả các trang Moodle.';
$string['settings_timeout'] = 'Thời gian chờ phản hồi (giây)';
$string['settings_timeout_desc'] = 'Số giây tối đa chờ backend trả lời trước khi báo lỗi.';

// Scheduled tasks.
$string['task_deadline_reminder'] = 'Nhắc nhở deadline sắp hết hạn';
$string['task_overdue_reminder'] = 'Nhắc sinh viên chưa nộp bài khi hết hạn';
$string['task_teacher_followup'] = 'Nhắc giáo viên hoàn thiện khóa học mới';

// Capability.
$string['chatbot:use'] = 'Sử dụng chatbot hỗ trợ học tập';

// UI strings.
$string['widget_title'] = 'Trợ lý học tập';
$string['widget_subtitle'] = 'Hỗ trợ bởi AI · RAG';
$string['widget_placeholder'] = 'Hỏi tôi về khoá học, tài liệu, deadline...';
$string['widget_send'] = 'Gửi';
$string['widget_open'] = 'Mở trợ lý học tập';
$string['widget_close'] = 'Đóng';
$string['widget_welcome'] = 'Xin chào! Tôi có thể giúp bạn tra cứu khoá học, tài liệu, deadline và điểm số. Bạn cần hỗ trợ gì?';
$string['widget_thinking'] = 'Đang suy nghĩ...';
$string['widget_error'] = 'Xin lỗi, đã có lỗi xảy ra. Vui lòng thử lại.';
$string['widget_sources'] = 'Nguồn tham khảo';

// Privacy.
$string['privacy:metadata:chatbot_backend'] = 'Plugin chatbot gửi câu hỏi của người dùng tới backend RAG bên ngoài để xử lý và sinh câu trả lời.';
$string['privacy:metadata:chatbot_backend:userid'] = 'ID người dùng Moodle được gửi tới backend để cá nhân hoá câu trả lời.';
$string['privacy:metadata:chatbot_backend:message'] = 'Nội dung câu hỏi của người dùng được gửi tới backend.';
