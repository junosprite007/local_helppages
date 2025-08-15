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
 * View help page.
 *
 * @package     local_helppages
 * @copyright   2025 Fun Learning Company
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$pagename = required_param('page', PARAM_ALPHANUMEXT);

require_login();

$context = context_system::instance();
require_capability('local/helppages:view', $context);

$page = local_helppages_get_by_name($pagename);

if (!$page) {
    throw new moodle_exception('pagenotfound', 'local_helppages');
}

if (!local_helppages_can_view_page($page)) {
    throw new moodle_exception('noaccess', 'local_helppages');
}

$PAGE->set_context($context);
$PAGE->set_url('/local/helppages/view.php', ['page' => $pagename]);
$PAGE->set_title($page->title);
// $PAGE->set_heading($page->title);
$PAGE->set_pagelayout('standard');

// Log page view event
$event = \local_helppages\event\page_viewed::create([
    'objectid' => $page->id,
    'context' => $context,
    'other' => ['pagename' => $page->name]
]);
$event->trigger();

echo $OUTPUT->header();

$templatecontext = [
    'page' => [
        'id' => $page->id,
        'title' => format_string($page->title),
        'content' => format_text($page->content, $page->contentformat, ['context' => $context]),
        'name' => $page->name
    ],
    'canmanage' => has_capability('local/helppages:manage', $context),
    'editurl' => new moodle_url('/local/helppages/edit.php', ['id' => $page->id]),
    'manageurl' => new moodle_url('/local/helppages/manage.php'),
    'notvisible' => $DB->get_record('local_helppages', ['name' => $page->name, 'visible' => 1]) === false
];

echo $OUTPUT->render_from_template('local_helppages/page_view', $templatecontext);
echo $OUTPUT->footer();
