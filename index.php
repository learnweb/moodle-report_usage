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

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
$id = required_param('id', PARAM_INT); // Course ID.
$tab = optional_param('tab', 'table-tab', PARAM_ALPHANUMEXT);

$url = new moodle_url('/report/usage/index.php', array('id' => $id));

$PAGE->set_url($url);
$PAGE->set_pagelayout('report');

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_login($course);
$context = context_course::instance($course->id);

require_capability('report/usage:view', $context);

$PAGE->set_title($course->shortname . ': ' . get_string('pluginname', 'report_usage'));
$PAGE->set_heading($course->fullname);

$roles = \report_usage\db_helper::get_roles_in_course($context->id);

$roleids = array_keys($roles);
$rolenames = array_values($roles);

$customdata = array(
    'startyear' => date('Y', $course->timecreated),
    'stopyear' => date('Y'),
    'roles' => $rolenames
);
$mform = new \report_usage\filter_form(new moodle_url('/report/usage/index.php'), $customdata, 'get');
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

$default = array('startdate' => $start, 'enddate' => $end, 'id' => $id);
$selectedroles = [];

// Form processing and displaying is done here.
if ($fromform = $mform->get_data()) {
    $start = $fromform->startdate;
    $end = $fromform->enddate;
    $tab = $fromform->tab;
    $selectedroles = [];
    foreach ($fromform->roles as $r) {
        $selectedroles[] = $roleids[$r];
    }
}

// If no or all roles are selected, disable role filtering.
if (count($selectedroles) == 0 || count($selectedroles) === count($roleids)) {
    $selectedroles = null;
}

var_dump(\report_usage\db_helper::get_data_from_course($id, $context->id, $selectedroles, '20190500', '20200000'));
var_dump(\report_usage\db_helper::get_processed_data_from_course($id, $context->id, $selectedroles, $start, $end));
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

$mustacheparams = array('table' => $tableoutput);
if ($tab && $tab == 'chart-tab') {
    $mustacheparams['chart-tab'] = true;
} else {
    $mustacheparams['table-tab'] = true;
}
echo $OUTPUT->render_from_template('report_usage/tabs', $mustacheparams);

$chartdata = new \report_usage\output\report_usage_chart($start, $end, $id);
list($data, $names) = $chartdata->get_data();
// The warning is weird, we decided it doesn't make sense in this case.
// Sending data via AJAX wouldn't be more efficient, because you can't cache the data on the client.
$PAGE->requires->js_call_amd('report_usage/init', 'init', array($data, $chartdata->create_labels(), $names, $id));
$PAGE->requires->js_call_amd('report_usage/tabs', 'init');

echo $OUTPUT->footer();
