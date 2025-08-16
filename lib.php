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
 * Library functions for local_helppages.
 *
 * @package     local_helppages
 * @copyright   2025 Fun Learning Company
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Generate a URL-safe name from a title
 *
 * @param string $title The title to convert
 * @return string URL-safe name
 */
function local_helppages_generate_name(string $title): string {
    $name = strtolower($title);
    $name = preg_replace('/[^a-z0-9\-_]/', '-', $name);
    $name = preg_replace('/[-_]+/', '-', $name);
    $name = trim($name, '-_');
    return $name;
}

/**
 * Check if a page name is valid
 *
 * @param string $name The name to validate
 * @return bool True if valid
 */
function local_helppages_validate_name(string $name): bool {
    return preg_match('/^[a-z0-9\-_]+$/', $name) && !empty($name);
}

/**
 * Check if a page name exists
 *
 * @param string $name The name to check
 * @param int $excludeid Optional ID to exclude from check
 * @return bool True if name exists
 */
function local_helppages_name_exists(string $name, int $excludeid = 0): bool {
    global $DB;

    if ($excludeid > 0) {
        return $DB->record_exists_select('local_helppages', 'name = ? AND id != ?', [$name, $excludeid]);
    } else {
        return $DB->record_exists('local_helppages', ['name' => $name]);
    }
}

/**
 * Get a help page by name
 *
 * @param string $name The page name
 * @return object|false The page record or false
 */
function local_helppages_get_by_name(string $name) {
    global $DB;

    if (is_siteadmin()) {
        return $DB->get_record('local_helppages', ['name' => $name]);
    } else {
        return $DB->get_record('local_helppages', ['name' => $name, 'visible' => 1]);
    }
}

/**
 * Check if user can view a specific help page
 *
 * @param object $page The page record
 * @param object $user Optional user object
 * @return bool True if user can view
 */
function local_helppages_can_view_page($page, $user = null): bool {
    global $USER;

    if (!$page->visible) {
        if (!is_siteadmin()) {
            return false;
        }
    }

    if ($user === null) {
        $user = $USER;
    }

    $context = context_system::instance();

    if (!has_capability('local/helppages:view', $context, $user)) {
        return false;
    }

    if (!empty($page->capabilities)) {
        $capabilities = json_decode($page->capabilities, true);
        if (is_array($capabilities)) {
            foreach ($capabilities as $capability) {
                if (!has_capability(trim($capability), $context, $user)) {
                    return false;
                }
            }
        }
    }

    return true;
}

/**
 * Get all visible help pages for current user
 *
 * @return array Array of page records
 */
function local_helppages_get_visible_pages(): array {
    global $DB;

    $pages = $DB->get_records('local_helppages', ['visible' => 1], 'sortorder ASC, title ASC');
    $visiblepages = [];

    foreach ($pages as $page) {
        if (local_helppages_can_view_page($page)) {
            $visiblepages[] = $page;
        }
    }

    return $visiblepages;
}

/**
 * Serve plugin files
 *
 * @param stdClass $course Course object
 * @param stdClass $cm Course module object
 * @param stdClass $context Context object
 * @param string $filearea File area name
 * @param array $args Array of arguments
 * @param bool $forcedownload Force download
 * @param array $options Additional options
 * @return bool|void False on failure
 */
function local_helppages_pluginfile($course, $cm, $context, string $filearea, array $args, bool $forcedownload, array $options = []): void {
    global $DB;

    // Check the context level is correct.
    if ($context->contextlevel !== CONTEXT_SYSTEM) {
        send_file_not_found();
    }

    // Make sure the filearea is one of those used by the plugin.
    if ($filearea !== 'content') {
        send_file_not_found();
    }

    // Make sure the user is logged in and has permission to view files.
    require_login();
    require_capability('local/helppages:view', $context);

    // Get the page ID from the args.
    $itemid = array_shift($args);

    if (!$itemid) {
        send_file_not_found();
    }

    // Get the page record to check permissions.
    $page = $DB->get_record('local_helppages', ['id' => $itemid]);
    if (!$page) {
        send_file_not_found();
    }

    // Check if user can view this specific page.
    if (!local_helppages_can_view_page($page)) {
        send_file_not_found();
    }

    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args);
    if (!$args) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'local_helppages', $filearea, $itemid, $filepath, $filename);

    if (!$file) {
        send_file_not_found();
    }

    // We can now send the file to the browser - in this case with a cache lifetime of 1 day and no filtering.
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}

/**
 * Get file areas used by the plugin
 *
 * @return array Array of file areas
 */
function local_helppages_get_file_areas(): array {
    return [
        'content' => get_string('pagecontentfiles', 'local_helppages')
    ];
}
