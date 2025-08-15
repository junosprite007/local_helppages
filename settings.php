<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Admin settings for local_helppages.
 *
 * @package     local_helppages
 * @copyright   2025 Fun Learning Company
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// use \core\output\html_writer;
// use \core\output\moodle_url;

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category(
        'local_helppages',
        new lang_string('pluginname', 'local_helppages')
    ));

    $ADMIN->add('local_helppages', new admin_externalpage(
        'local_helppages_manage',
        new lang_string('managehelpages', 'local_helppages'),
        new moodle_url('/local/helppages/manage.php'),
        'local/helppages:manage'
    ));

    $settingspage = new admin_settingpage(
        'local_helppages_settings',
        new lang_string('helppagessettings', 'local_helppages')
    );

    if ($ADMIN->fulltree) {
        $settingspage->add(new admin_setting_heading(
            'local_helppages/general',
            new lang_string('pluginname', 'local_helppages'),
            get_string('pluginname', 'local_helppages') . ' plugin configuration'
        ));
    }

    $ADMIN->add('local_helppages', $settingspage);
}
