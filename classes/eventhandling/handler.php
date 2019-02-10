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
 *
 *
 * @package    report_activity_analysis
 * @copyright  2019 Justus Dieckmann <justusdieckmann@wwu.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_activity_analysis;

class handler
{

    public static function handle($event)
    {
        global $DB;
        $data = $event->get_data();
        //if($DB->record_exists('report_activity_ana_courses', array('courseid' => $data['courseid'])))
        $record = new \stdClass();
        $record->userid = $data['userid'];
        $record->courseid = $data['courseid'];
        $record->objecttable = $data['objecttable'];
        $record->time = $data['timecreated'];
        $record->objectid = $data['objectid'];
        $DB->insert_record('report_activity_ana_events', $record);
    }

}