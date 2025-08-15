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
 * Page viewed event.
 *
 * @package     local_helppages
 * @copyright   2025 Fun Learning Company
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_helppages\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Page viewed event class
 */
class page_viewed extends \core\event\base {

    /**
     * Initialize the event
     */
    protected function init(): void {
        $this->data['objecttable'] = 'local_helppages';
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Get event name
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('eventpageviewed', 'local_helppages');
    }

    /**
     * Get event description
     *
     * @return string
     */
    public function get_description(): string {
        return "The user with id '$this->userid' viewed the help page with id '$this->objectid'.";
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url(): \moodle_url {
        return new \moodle_url('/local/helppages/view.php', ['page' => $this->other['pagename']]);
    }
}
