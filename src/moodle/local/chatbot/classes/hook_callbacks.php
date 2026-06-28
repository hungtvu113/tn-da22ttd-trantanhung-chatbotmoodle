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

use core\hook\output\before_footer_html_generation;

/**
 * Hook callbacks for local_chatbot.
 *
 * @package    local_chatbot
 * @copyright  2026 Khoa luan tot nghiep
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {

    /**
     * Inject the floating chat widget into every page footer.
     *
     * @param before_footer_html_generation $hook
     */
    public static function inject_chat_widget(before_footer_html_generation $hook): void {
        global $PAGE, $USER;

        if (!get_config('local_chatbot', 'enabled')) {
            return;
        }

        if (!isloggedin() || isguestuser()) {
            return;
        }

        if (!has_capability('local/chatbot:use', \context_system::instance())) {
            return;
        }

        try {
            $context = [
                'userid' => (int) $USER->id,
                'userfullname' => fullname($USER),
            ];
            $html = $PAGE->get_renderer('core')->render_from_template('local_chatbot/widget', $context);
            $PAGE->requires->js_call_amd('local_chatbot/chatbot', 'init');
            $hook->add_html($html);
        } catch (\Throwable $e) {
            debugging('local_chatbot widget injection failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }
}
