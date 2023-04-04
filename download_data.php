<?php

use report_usage\util;

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->libdir.'/csvlib.class.php');

$id = required_param('id', PARAM_INT); // Course ID.
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_login($course);

if (!has_capability('moodle/site:config', context_system::instance())) {
    die("no access");
}

$context = context_course::instance($course->id);

// Get activities in course.
$activities =  course_modinfo::get_array_of_activities($course);

// Get enrolled Students in course.
$users = get_role_users(5, $context);

$csvexportwriter = new csv_export_writer();
$csvexportwriter->set_filename('course'.$id.'-gradestatistics');

foreach ($users as $u) {

    foreach ($activities as $a){

        $cm = get_coursemodule_from_instance($a->mod, $a->id, $id);
        $actcontext = context_module::instance($cm->id);

        $sql = "SELECT id, userid, amount, daycreated, monthcreated, yearcreated 
              FROM mdl_logstore_usage_log
              WHERE contextid=$actcontext->id AND userid=$u->id AND courseid=$id";

        foreach ($DB->get_records_sql($sql) as $rec) {
                
            $date = $rec->daycreated.'-'.$rec->monthcreated.'-'.$rec->yearcreated;
            $row = array(
                'username' => $u->username,
                'userid' => $rec->userid,
                'activityname' => $a->name,
                'amount' => $rec->amount,
                'date' => $date
            );
            $csvexportwriter->add_data($row);
        }
    }
}

$csvexportwriter->download_file();