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

namespace local_chatbot\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use core_external\external_value;
use local_chatbot\api_client;

/**
 * External function: forward a chat message to the RAG backend.
 *
 * @package    local_chatbot
 * @copyright  2026 Khoa luan tot nghiep
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_message extends external_api {

    /**
     * Parameters definition.
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'message' => new external_value(PARAM_RAW, 'User message text', VALUE_REQUIRED),
            'conversationid' => new external_value(PARAM_ALPHANUMEXT, 'Optional client conversation id',
                VALUE_DEFAULT, ''),
        ]);
    }

    /**
     * Execute: validate, call backend, return reply.
     *
     * @param string $message
     * @param string $conversationid
     * @return array
     */
    public static function execute(string $message, string $conversationid = ''): array {
        global $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'message' => $message,
            'conversationid' => $conversationid,
        ]);

        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('local/chatbot:use', $context);

        $message = trim((string) $params['message']);
        if ($message === '') {
            return [
                'success' => false,
                'reply' => '',
                'sources' => [],
                'error' => 'empty_message',
                'errordetail' => '',
            ];
        }

        $client = new api_client();

        try {
            $response = $client->send_message(
                (int) $USER->id,
                $message,
                $params['conversationid'] !== '' ? $params['conversationid'] : null,
                (string) $USER->username,
                fullname($USER),
                self::detect_role($USER)
            );
        } catch (\Throwable $e) {
            debugging('local_chatbot backend call failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return [
                'success' => false,
                'reply' => '',
                'sources' => [],
                'error' => 'backend_error',
                'errordetail' => debugging('', DEBUG_DEVELOPER) ? $e->getMessage() : '',
            ];
        }

        $sources = [];
        if (!empty($response['sources']) && is_array($response['sources'])) {
            foreach ($response['sources'] as $src) {
                $sources[] = [
                    'title' => (string) ($src['title'] ?? ''),
                    'url' => (string) ($src['url'] ?? ''),
                ];
            }
        }

        return [
            'success' => true,
            'reply' => (string) ($response['answer'] ?? $response['reply'] ?? ''),
            'sources' => $sources,
            'error' => '',
            'errordetail' => '',
        ];
    }

    /**
     * Xác định vai trò tổng quát của người dùng để backend cá nhân hoá phản hồi.
     *
     * Trả về một trong: 'admin', 'teacher', 'student'.
     * - admin: là site admin.
     * - teacher: có vai trò editingteacher/teacher/manager ở bất kỳ khoá học nào.
     * - student: mặc định còn lại.
     *
     * @param \stdClass $user Đối tượng user (thường là $USER).
     * @return string
     */
    private static function detect_role(\stdClass $user): string {
        if (is_siteadmin($user)) {
            return 'admin';
        }

        $teacherroles = ['editingteacher', 'teacher', 'manager', 'coursecreator'];
        $courses = enrol_get_users_courses($user->id, true, ['id']);
        foreach ($courses as $course) {
            $context = \context_course::instance($course->id);
            foreach (get_user_roles($context, $user->id, false) as $role) {
                if (in_array($role->shortname, $teacherroles, true)) {
                    return 'teacher';
                }
            }
        }

        return 'student';
    }

    /**
     * Return structure.
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether the call succeeded'),
            'reply' => new external_value(PARAM_RAW, 'Assistant reply text'),
            'sources' => new external_multiple_structure(
                new external_single_structure([
                    'title' => new external_value(PARAM_TEXT, 'Source title'),
                    'url' => new external_value(PARAM_URL, 'Source URL', VALUE_OPTIONAL),
                ]),
                'List of retrieved source documents'
            ),
            'error' => new external_value(PARAM_ALPHANUMEXT, 'Error code if success is false'),
            'errordetail' => new external_value(PARAM_TEXT, 'Detailed error message (developer mode only)'),
        ]);
    }
}
