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
 * @subpackage usage
 * @copyright  Justus Dieckmann <justusdieckmann@wwu.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("lib.php");
$id = required_param('id', PARAM_INT); // Course ID.
$PAGE->set_url('/report/usage/index.php', array('id' => $id));
$PAGE->set_pagelayout('report');

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_login($course);
$context = context_course::instance($course->id);

require_capability('report/usage:view', $context);

$PAGE->set_title($course->shortname .': ' . get_string('pluginname', 'report_usage'));
$PAGE->set_heading($course->fullname);

$records = $DB->get_records_sql("SELECT contextid AS id, COUNT(*) AS amount FROM {report_usage_events} WHERE courseid = ? GROUP BY contextid", array($id));
//$records = $DB->get_records_sql_menu('SELECT (objectid, objecttable), COUNT(*) FROM mdl_report_usage_events WHERE courseid = ? GROUP BY objectid, objecttable', array($id));

$output = $PAGE->get_renderer('report_usage');
echo $output->header();
echo $output->heading($course->fullname .': ' . get_string('pluginname', 'report_usage'));
$renderable = new \report_usage\output\report_usage_renderable($records);

echo $output->render($renderable);

echo $output->footer();
