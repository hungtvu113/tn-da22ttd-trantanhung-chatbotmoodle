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

namespace local_chatbot;

defined('MOODLE_INTERNAL') || die();

/**
 * HTTP client to talk to the external RAG backend.
 *
 * @package    local_chatbot
 * @copyright  2026 Khoa luan tot nghiep
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api_client {

    /** @var string */
    private string $apiurl;
    /** @var string */
    private string $apikey;
    /** @var int */
    private int $timeout;

    public function __construct() {
        $this->apiurl  = (string) get_config('local_chatbot', 'apiurl');
        $this->apikey  = (string) get_config('local_chatbot', 'apikey');
        $this->timeout = (int) (get_config('local_chatbot', 'timeout') ?: 90);
    }

    /**
     * Send a chat message to the backend and return the parsed response.
     *
     * @param int $userid Moodle user id (for backend personalisation).
     * @param string $message Raw user message.
     * @param string|null $conversationid Optional client-side conversation id.
     * @param string $username Moodle username (backend dùng để truy vấn dữ liệu cá nhân).
     * @param string $fullname Họ tên hiển thị của người dùng.
     * @param string $role Vai trò tổng quát: 'admin' | 'teacher' | 'student'.
     * @return array Backend JSON decoded as associative array.
     * @throws \moodle_exception when configuration is missing or transport fails.
     */
    public function send_message(int $userid, string $message, ?string $conversationid = null,
            string $username = '', string $fullname = '', string $role = 'student'): array {
        global $CFG;

        if (empty($this->apiurl)) {
            throw new \moodle_exception('errorbackendnotconfigured', 'local_chatbot', '',
                null, 'Backend URL is not configured.');
        }

        // Moodle's \curl class lives in lib/filelib.php and is not autoloaded
        // in AJAX/web-service contexts, so include it explicitly.
        require_once($CFG->libdir . '/filelib.php');

        $payload = [
            'user_id' => $userid,
            'message' => $message,
            'conversation_id' => $conversationid,
            'username' => $username,
            'fullname' => $fullname,
            'role' => $role,
        ];

        // The backend URL is configured by the site admin (not user input),
        // so we bypass Moodle's SSRF blocklist that would otherwise reject
        // localhost / private network addresses used during development.
        $curl = new \curl(['ignoresecurity' => true]);
        $curl->setHeader([
            'Content-Type: application/json',
            'Accept: application/json',
            'X-API-Key: ' . $this->apikey,
        ]);
        $curl->setopt([
            'CURLOPT_TIMEOUT' => $this->timeout,
            'CURLOPT_CONNECTTIMEOUT' => 10,
            'CURLOPT_RETURNTRANSFER' => true,
        ]);

        $raw = $curl->post($this->apiurl, json_encode($payload));
        $info = $curl->get_info();
        $httpcode = (int) ($info['http_code'] ?? 0);

        if ($curl->get_errno()) {
            throw new \moodle_exception('errorbackendunreachable', 'local_chatbot', '',
                null, 'cURL error: ' . $curl->error);
        }

        if ($httpcode < 200 || $httpcode >= 300) {
            throw new \moodle_exception('errorbackendhttp', 'local_chatbot', '',
                null, "HTTP {$httpcode}: " . substr((string) $raw, 0, 500));
        }

        $decoded = json_decode((string) $raw, true);
        if (!is_array($decoded)) {
            throw new \moodle_exception('errorbackendinvalidjson', 'local_chatbot', '',
                null, 'Invalid JSON from backend.');
        }

        return $decoded;
    }
}
