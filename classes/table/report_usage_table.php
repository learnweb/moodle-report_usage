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
 * A table to display the usage of activites
 *
 * @package    report_usage
 * @copyright  Justus Dieckmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_usage\table;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/tablelib.php');

class report_usage_table extends \flexible_table {

    public function __construct($courseid, $days) {
        parent::__construct("report_usage_" . $courseid);

        $dt = new \DateTime($days . " days ago");

        $cols = [];
        $headers = [];

        for ($i = 0; $i < $days; $i++) {
            $cols[] = $dt->format('Y-m-d');
            $headers[] = $dt->format('d.m');
            $dt->add(new \DateInterval("P1D"));
        }

        $this->define_columns($cols);
        $this->define_headers($headers);
    }

    public function addFunkyData() {
        $this->add_data($this->headers);
    }
}