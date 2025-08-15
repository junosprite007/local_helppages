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
 * Manage help pages.
 *
 * @package     local_helppages
 * @copyright   2025 Fun Learning Company
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

require_login();

$context = context_system::instance();
require_capability('local/helppages:manage', $context);

$PAGE->set_context($context);
$PAGE->set_url('/local/helppages/manage.php');
$PAGE->set_title(get_string('managehelpages', 'local_helppages'));
$PAGE->set_heading(get_string('managehelpages', 'local_helppages'));
$PAGE->set_pagelayout('admin');

// Load JavaScript for form handling
$PAGE->requires->js_call_amd('local_helppages/formhandling', 'init');

// Handle delete action
if ($action === 'delete' && $id && confirm_sesskey()) {
    $page = $DB->get_record('local_helppages', ['id' => $id], '*', MUST_EXIST);

    $DB->delete_records('local_helppages', ['id' => $id]);

    // Trigger event
    $event = \local_helppages\event\page_deleted::create([
        'objectid' => $id,
        'context' => $context,
        'other' => ['pagename' => $page->name, 'title' => $page->title]
    ]);
    $event->trigger();

    redirect($PAGE->url, get_string('pagedeleted', 'local_helppages'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

// Get all pages
$pages = $DB->get_records('local_helppages', null, 'sortorder ASC, title ASC');

$templatecontext = [
    'pages' => [],
    'haspages' => !empty($pages),
    'addpageurl' => new moodle_url('/local/helppages/edit.php'),
    'addpagelabel' => get_string('add', 'local_helppages')
];

foreach ($pages as $page) {
    $templatecontext['pages'][] = [
        'id' => $page->id,
        'title' => format_string($page->title),
        'name' => $page->name,
        'visible' => $page->visible,
        'visibletext' => $page->visible ? get_string('yes') : get_string('no'),
        'created' => userdate($page->timecreated),
        'modified' => userdate($page->timemodified),
        'editurl' => new moodle_url('/local/helppages/edit.php', ['id' => $page->id]),
        'viewurl' => new moodle_url('/local/helppages/view.php', ['page' => $page->name]),
        'deleteurl' => new moodle_url('/local/helppages/manage.php', [
            'action' => 'delete',
            'id' => $page->id,
            'sesskey' => sesskey()
        ])
    ];
}

echo $OUTPUT->render_from_template('local_helppages/manage_pages', $templatecontext);

echo $OUTPUT->footer();
