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
 * Admin settings for local_chatbot.
 *
 * @package    local_chatbot
 * @copyright  2026 Khoa luan tot nghiep
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_chatbot', new lang_string('pluginname', 'local_chatbot'));

    $settings->add(new admin_setting_configcheckbox(
        'local_chatbot/enabled',
        new lang_string('settings_enabled', 'local_chatbot'),
        new lang_string('settings_enabled_desc', 'local_chatbot'),
        1
    ));

    $settings->add(new admin_setting_configtext(
        'local_chatbot/apiurl',
        new lang_string('settings_apiurl', 'local_chatbot'),
        new lang_string('settings_apiurl_desc', 'local_chatbot'),
        'http://localhost:8001/api/chat',
        PARAM_URL
    ));

    $settings->add(new admin_setting_configpasswordunmask(
        'local_chatbot/apikey',
        new lang_string('settings_apikey', 'local_chatbot'),
        new lang_string('settings_apikey_desc', 'local_chatbot'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'local_chatbot/timeout',
        new lang_string('settings_timeout', 'local_chatbot'),
        new lang_string('settings_timeout_desc', 'local_chatbot'),
        90,
        PARAM_INT
    ));

    $ADMIN->add('localplugins', $settings);
}
