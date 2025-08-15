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
 * Language strings for local_helppages.
 *
 * @package     local_helppages
 * @copyright   2025 Fun Learning Company
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Help Pages';
$string['helppages:manage'] = 'Manage help pages';
$string['helppages:view'] = 'View help pages';

// Admin settings
$string['helppagessettings'] = 'Help Pages Settings';
$string['managehelpages'] = 'Manage Help Pages';
$string['createhelppage'] = 'Create Help Page';
$string['edithelppage'] = 'Edit Help Page';

// Form elements
$string['pagetitle'] = 'Page Title';
$string['pagetitle_help'] = 'The title displayed at the top of the help page';
$string['pagename'] = 'Page Name';
$string['pagename_help'] = 'URL-safe identifier for the page. Leave blank to auto-generate from title.';
$string['pagecontent'] = 'Page Content';
$string['pagecontent_help'] = 'The main content of the help page';
$string['pagevisible'] = 'Visible';
$string['pagevisible_help'] = 'Whether this help page is visible to users';
$string['pagenotvisible'] = 'This page is not visible to users.';
$string['pagecapabilities'] = 'Required Capabilities';
$string['pagecapabilities_help'] = 'Additional capabilities required to view this page (one per line)';
$string['sortorder'] = 'Sort Order';
$string['sortorder_help'] = 'Order in which pages appear in listings';

// Table headers
$string['title'] = 'Title';
$string['name'] = 'Name';
$string['visible'] = 'Visible';
$string['actions'] = 'Actions';
$string['created'] = 'Created';
$string['modified'] = 'Modified';

// Actions
$string['edit'] = 'Edit';
$string['delete'] = 'Delete';
$string['view'] = 'View';
$string['add'] = 'Add New Page';

// Messages
$string['pagecreated'] = 'Help page created successfully';
$string['pageupdated'] = 'Help page updated successfully';
$string['pagedeleted'] = 'Help page deleted successfully';
$string['pagenotfound'] = 'Help page not found';
$string['nopages'] = 'No help pages are available... yet.';
$string['confirmdelete'] = 'Are you sure you want to delete this help page?';

// Errors
$string['invalidpagename'] = 'Invalid page name. Use only letters, numbers, hyphens and underscores.';
$string['pagenameexists'] = 'A page with this name already exists';
$string['titlerequired'] = 'Page title is required';
$string['contentrequired'] = 'Page content is required';
$string['noaccess'] = 'You do not have permission to access this page';

// Navigation
$string['backtomanage'] = 'Back to Manage Pages';
$string['backtopage'] = 'Back to Help Page';

// Events
$string['eventpagecreated'] = 'Help page created';
$string['eventpageupdated'] = 'Help page updated';
$string['eventpagedeleted'] = 'Help page deleted';
$string['eventpageviewed'] = 'Help page viewed';

// Privacy
$string['privacy:metadata'] = 'The Help Pages plugin does not store personal data about users.';

// Template specific
$string['hidden'] = 'Hidden';
$string['nopagesdesc'] = 'Create your first help page to get started.';
$string['createfirstpage'] = 'Create First Page';

// Main View page
$string['helppages'] = 'Help Pages';
$string['availablepages'] = 'Available Help Pages';
$string['pageindex'] = 'Help Page Index';
