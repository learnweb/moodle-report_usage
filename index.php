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
 * A page to display an analysis of activity by users
 *
 * @package    report
 * @subpackage activity_analysis
 * @copyright  Justus Dieckmann <justusdieckmann@wwu.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("lib.php");
$id = required_param('id', PARAM_INT); // Course ID.
$PAGE->set_url('/report/activity_analysis/index.php', array('id' => $id));
$PAGE->set_pagelayout('report');

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_login($course);
$context = context_course::instance($course->id);

require_capability('report/activity_analysis:view', $context);

$PAGE->set_title($course->shortname .': ' . get_string('pluginname', 'report_activity_analysis'));
$PAGE->set_heading($course->fullname);

$records = $DB->get_records_sql_menu('SELECT (objectid, objecttable), COUNT(*) FROM mdl_report_activity_ana_events WHERE courseid = ? GROUP BY objectid, objecttable', array($id));

$output = $PAGE->get_renderer('report_activity_analysis');
echo $output->header();
echo $output->heading($course->fullname .': ' . get_string('pluginname', 'report_activity_analysis'));
$renderable = new \report_activity_analysis\output\report_activity_analysis_renderable($records);

echo $output->render($renderable);

echo $output->footer();
