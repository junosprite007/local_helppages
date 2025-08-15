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
 * Edit help page.
 *
 * @package     local_helppages
 * @copyright   2025 Fun Learning Company
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$id = optional_param('id', 0, PARAM_INT);

require_login();

$context = context_system::instance();
require_capability('local/helppages:manage', $context);

$page = null;
if ($id) {
    $page = $DB->get_record('local_helppages', ['id' => $id], '*', MUST_EXIST);
}

$PAGE->set_context($context);
$PAGE->set_url('/local/helppages/edit.php', $id ? ['id' => $id] : []);
$PAGE->set_title($page ? get_string('edithelppage', 'local_helppages') : get_string('createhelppage', 'local_helppages'));
$PAGE->set_heading($page ? get_string('edithelppage', 'local_helppages') : get_string('createhelppage', 'local_helppages'));
$PAGE->set_pagelayout('admin');

$form = new \local_helppages\form\page_form();

if ($page) {
    // Editing existing page - prepare form data
    $formdata = clone $page;
    $formdata->content_editor = [
        'text' => $page->content,
        'format' => $page->contentformat
    ];
    $form->set_data($formdata);
}

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/helppages/manage.php'));
} else if ($data = $form->get_data()) {

    // Generate name if empty
    if (empty($data->name)) {
        $data->name = local_helppages_generate_name($data->title);
    }

    $record = new stdClass();
    $record->title = $data->title;
    $record->name = $data->name;
    $record->content = $data->content_editor['text'];
    $record->contentformat = $data->content_editor['format'];
    $record->visible = $data->visible ?? 0;
    $record->sortorder = $data->sortorder ?? 0;
    $record->capabilities = !empty($data->capabilities) ? json_encode(array_map('trim', explode("\n", $data->capabilities))) : '';

    if ($page) {
        // Update existing page
        $record->id = $page->id;
        $record->timemodified = time();
        $record->modifiedby = $USER->id;

        $DB->update_record('local_helppages', $record);

        // Trigger event
        $event = \local_helppages\event\page_updated::create([
            'objectid' => $page->id,
            'context' => $context,
            'other' => ['pagename' => $record->name]
        ]);
        $event->trigger();

        $message = get_string('pageupdated', 'local_helppages');
    } else {
        // Create new page
        $record->timecreated = time();
        $record->timemodified = time();
        $record->createdby = $USER->id;
        $record->modifiedby = $USER->id;

        $id = $DB->insert_record('local_helppages', $record);
        $record->id = $id;

        // Trigger event
        $event = \local_helppages\event\page_created::create([
            'objectid' => $id,
            'context' => $context,
            'other' => ['pagename' => $record->name]
        ]);
        $event->trigger();

        $message = get_string('pagecreated', 'local_helppages');
    }

    redirect(new moodle_url('/local/helppages/manage.php'), $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
