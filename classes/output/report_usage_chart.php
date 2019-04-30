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
 * Activity analysis rendererable
 *
 * @package    report_usage
 * @copyright  2019 Justus Dieckmann <justusdieckmann@wwu.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_usage\output;

defined('MOODLE_INTERNAL') || die();

class report_usage_chart {

    public $start;
    public $end;
    public $cid;

    public function __construct($start, $end, $cid) {
        $this->start = $start;
        $this->end = $end;
        $this->cid = $cid;
    }

    public function get_data($onlyamount = true) {
        global $DB;

        $startdate = new \DateTime("now", \core_date::get_server_timezone_object());
        $startdate->setTimestamp($this->start);

        $enddate = new \DateTime("now", \core_date::get_server_timezone_object());
        $enddate->setTimestamp($this->end);

        $days = intval($startdate->diff($enddate)->format('%a'));

        $params = array($this->cid, $startdate->format("Ymd"), $enddate->format("Ymd"));
        $sql = "SELECT MIN(id) AS id, contextid, yearcreated, monthcreated, daycreated, SUM(amount) AS amount
                  FROM {logstore_usage_log}
                 WHERE courseid = ? AND yearcreated * 10000 + monthcreated * 100 + daycreated >= ?
                       AND yearcreated * 10000 + monthcreated * 100 + daycreated <= ?
              GROUP BY contextid, yearcreated, monthcreated, daycreated
              ORDER BY contextid, yearcreated, monthcreated, daycreated";

        $records = $DB->get_records_sql($sql, $params);

        // Output[25][6] will give you the amount of views for the activity with the id 25, 6 days after the startdate.
        $output = [];
        // Put every record in its place in $output.
        foreach ($records as $v) {
            if (!isset($output[$v->contextid])) {
                $output[$v->contextid] = [];
            }
            $diffdate = new \DateTime("$v->daycreated-$v->monthcreated-$v->yearcreated");
            $diff = intval($diffdate->diff($startdate, true)->format("%a"));
            $output[$v->contextid][$diff] = $onlyamount ? intval($v->amount) : $v;
        }

        $names = [];

        // Fill empty cells with 0.
        foreach ($output as $k => $v) {
            for ($i = 0; $i <= $days; $i++) {
                if (!isset($output[$k][$i])) {
                    $output[$k][$i] = 0;
                }
            }
            $context = \context::instance_by_id($k, IGNORE_MISSING);
            $names[$k] = $context->get_context_name(false, true);
            ksort($output[$k]);
        }

        return array($output, $names);
    }

    public function create_labels() {
        $startdate = new \DateTime("now", \core_date::get_server_timezone_object());
        $startdate->setTimestamp($this->start);

        $enddate = new \DateTime("now", \core_date::get_server_timezone_object());
        $enddate->setTimestamp($this->end);

        $days = intval($startdate->diff($enddate)->format('%a'));
        $labels = [];
        for ($i = 0; $i <= $days; $i++) {
            $labels[] = $startdate->format("d.m");
            $startdate->add(new \DateInterval("P1D"));
        }
        return $labels;
    }
}