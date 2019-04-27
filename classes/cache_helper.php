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
 * Helper class for caching
 *
 * @package   report_usage
 * @copyright 2019 Justus Dieckmann
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_usage;

defined('MOODLE_INTERNAL') || die();

/**
 * Class helper
 * @package report_usage
 */
class cache_helper {
    public static function is_course_activated($courseid) {
        global $CFG;
        require_once($CFG->libdir . '/moodlelib.php');

        $cache = \cache::make('logstore_usage', 'courses');
        $courses = $cache->get('courses');
        if ($courses === false) {
            $courses = explode(',', get_config('logstore_usage', 'courses'));
            $cache->set('courses', $courses);
        }
        return in_array($courseid, $courses);
    }
}
