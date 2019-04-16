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
 * @package    report_usage
 * @copyright  Justus Dieckmann <justusdieckmann@wwu.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("lib.php");
$id = required_param('id', PARAM_INT); // Course ID.

$url = new moodle_url('/report/usage/index.php', array('id' => $id));

$PAGE->set_url($url);
$PAGE->set_pagelayout('report');

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_login($course);
$context = context_course::instance($course->id);

require_capability('report/usage:view', $context);

$PAGE->set_title($course->shortname . ': ' . get_string('pluginname', 'report_usage'));
$PAGE->set_heading($course->fullname);

$mform = new \report_usage\filter_form(new moodle_url('/report/usage/index.php'), null, 'get');
if ($mform->is_cancelled()) {
    redirect($url);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($course->fullname . ': ' . get_string('pluginname', 'report_usage'));

$start = $course->startdate;
$end = time();
if ($course->enddate && $course->enddate < $end) {
    $end = $course->enddate;
}

$default = array('filterstartdate' => $start, 'filterenddate' => $end, 'id' => $id);

// Form processing and displaying is done here.
if ($fromform = $mform->get_data()) {
    $start = $fromform->filterstartdate;
    $end = $fromform->filterenddate;
}
// Set default data (if any).
$mform->set_data($default);
$mform->display();

$table = new \report_usage\table\report_usage_table($id, $start, $end);
$table->define_baseurl($url);

ob_start();
$table->setup();
$table->init_data();
$table->finish_html();
$tableoutput = ob_get_clean();

echo $OUTPUT->render_from_template('report_usage/tabs', array('table' => $tableoutput));

$renderable = new \report_usage\output\report_usage_chart_renderable($start, $end, $id);
list($data, $names) = $renderable->get_data();
$PAGE->requires->js_call_amd('report_usage/init', 'init', array($data, $renderable->create_labels(), $names));

echo $OUTPUT->footer();
