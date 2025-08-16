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
 * Form for creating and editing help pages.
 *
 * @package     local_helppages
 * @copyright   2025 Fun Learning Company
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_helppages\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Help page edit form
 */
class page_form extends \moodleform {

    /**
     * Form definition
     */
    public function definition(): void {
        $mform = $this->_form;

        $mform->addElement('text', 'title', get_string('pagetitle', 'local_helppages'), ['size' => 60]);
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', get_string('titlerequired', 'local_helppages'), 'required', null, 'client');
        $mform->addHelpButton('title', 'pagetitle', 'local_helppages');

        $mform->addElement('text', 'name', get_string('pagename', 'local_helppages'), ['size' => 60]);
        $mform->setType('name', PARAM_ALPHANUMEXT);
        $mform->addHelpButton('name', 'pagename', 'local_helppages');

        $mform->addElement(
            'editor',
            'content_editor',
            get_string('pagecontent', 'local_helppages'),
            ['rows' => 15],
            $this->get_editor_options()
        );
        $mform->setType('content_editor', PARAM_RAW);
        $mform->addRule('content_editor', get_string('contentrequired', 'local_helppages'), 'required', null, 'client');
        $mform->addHelpButton('content_editor', 'pagecontent', 'local_helppages');

        $mform->addElement('advcheckbox', 'visible', get_string('pagevisible', 'local_helppages'));
        $mform->setDefault('visible', 1);
        $mform->addHelpButton('visible', 'pagevisible', 'local_helppages');

        $mform->addElement(
            'textarea',
            'capabilities',
            get_string('pagecapabilities', 'local_helppages'),
            ['rows' => 3, 'cols' => 60]
        );
        $mform->setType('capabilities', PARAM_TEXT);
        $mform->addHelpButton('capabilities', 'pagecapabilities', 'local_helppages');

        $mform->addElement('text', 'sortorder', get_string('sortorder', 'local_helppages'), ['size' => 10]);
        $mform->setType('sortorder', PARAM_INT);
        $mform->setDefault('sortorder', 0);
        $mform->addHelpButton('sortorder', 'sortorder', 'local_helppages');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons();
    }

    /**
     * Form validation
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        if (empty($data['title'])) {
            $errors['title'] = get_string('titlerequired', 'local_helppages');
        }

        if (empty($data['name'])) {
            $data['name'] = local_helppages_generate_name($data['title']);
        }

        if (!local_helppages_validate_name($data['name'])) {
            $errors['name'] = get_string('invalidpagename', 'local_helppages');
        }

        if (local_helppages_name_exists($data['name'], $data['id'] ?? 0)) {
            $errors['name'] = get_string('pagenameexists', 'local_helppages');
        }

        if (empty($data['content_editor']['text'])) {
            $errors['content_editor'] = get_string('contentrequired', 'local_helppages');
        }

        return $errors;
    }

    /**
     * Get editor options
     *
     * @return array Editor options
     */
    private function get_editor_options(): array {
        global $CFG;

        $context = \context_system::instance();

        return [
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'maxbytes' => $CFG->maxbytes,
            'trusttext' => false,
            'forcehttps' => false,
            'subdirs' => true,
            'context' => $context,
            'noclean' => true,
            'enable_filemanagement' => true
        ];
    }
}
