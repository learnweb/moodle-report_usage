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

    private $couseid;

    private $days;
    private $startdate;
    private $enddate;


    public function __construct($courseid, $start, $end) {
        parent::__construct("report_usage_" . $courseid);

        $startdate = new \DateTime("now", \core_date::get_server_timezone_object());
        $startdate->setTimestamp($start);
        $this->startdate = $startdate;

        $enddate = new \DateTime("now", \core_date::get_server_timezone_object());
        $enddate->setTimestamp($end);
        $this->enddate = $enddate;

        $days = intval($startdate->diff($enddate)->format('%a'));
        $this->days = $days;

        $this->set_attribute('class', 'generaltable generalbox');
        $this->show_download_buttons_at(array(TABLE_P_BOTTOM));

        $this->couseid = $courseid;

        $dt = new \DateTime("now", \core_date::get_server_timezone_object());
        $dt->setTimestamp($start);

        $cols = ['name'];
        $headers = ["<div style='padding: .5rem'>".get_string('file', 'report_usage')."</div>"];

        for ($i = 0; $i <= $days; $i++) {
            $cols[] = $dt->format('Y-m-d');
            $name = $dt->format('d.m');
            $headers[] = "<div style='padding: .5rem'>$name</div>";
            $dt->add(new \DateInterval("P1D"));
        }

        $this->define_columns($cols);
        $this->define_headers($headers);
        $this->is_downloadable(true);

        $this->column_style_all('padding', '0');
        $this->column_style_all('white-space', 'nowrap');
    }

    public function init_data() {
        global $DB;

        $params = array($this->couseid, $this->startdate->format("Ymd"), $this->enddate->format("Ymd"));
        $sql = "SELECT contextid AS id, MAX(amount)
                  FROM (
                      SELECT MIN(id) AS id, contextid, yearcreated, monthcreated, daycreated, SUM(amount) AS amount
                        FROM {logstore_usage_log}
                       WHERE courseid = ? AND yearcreated * 10000 + monthcreated * 100 + daycreated >= ?
                    GROUP BY contextid, yearcreated, monthcreated, daycreated
                  ) AS sub
              GROUP BY contextid";
        $maxima = $DB->get_records_sql_menu($sql, $params);

        $biggestmax = 0;
        foreach ($maxima as $m) {
            if (intval($m) > $biggestmax) {
                $biggestmax = intval($m);
            }
        }

        $sql = "SELECT MIN(id) AS id, contextid, yearcreated, monthcreated, daycreated, SUM(amount) AS amount
                  FROM {logstore_usage_log}
                 WHERE courseid = ? AND yearcreated * 10000 + monthcreated * 100 + daycreated >= ?
                       AND yearcreated * 10000 + monthcreated * 100 + daycreated <= ?
              GROUP BY contextid, yearcreated, monthcreated, daycreated
              ORDER BY contextid, yearcreated, monthcreated, daycreated";

        $records = $DB->get_records_sql($sql, $params);

        $data = [];

        foreach ($records as $v) {
            if (!isset($data[$v->contextid])) {
                $context = \context::instance_by_id($v->contextid, IGNORE_MISSING);
                $name = $context->get_context_name(false, true);
                $link = $context->get_url();
                $color = $this->get_color_by_percentage(intval($maxima[$v->contextid]) / $biggestmax);
                $html = "<div style='background-color: $color; padding: .5rem'><a href='$link'>$name</a></div>";

                $data[$v->contextid] = [$html];
            }

            $diff = new \DateTime("$v->daycreated-$v->monthcreated-$v->yearcreated");
            $datediff = intval($diff->diff($this->startdate, true)->format("%a"));
            if ($datediff > $this->days) {
                echo $datediff;
            }
            $color = $this->get_color_by_percentage($v->amount / intval($maxima[$v->contextid]));
            $data[$v->contextid][$datediff + 1] = "<div style='background-color: $color; padding: .5rem'>$v->amount</div>";
        }

        for ($i = 0; $i <= $this->days; $i++) {
            foreach ($data as $k => $v) {
                if (!isset($data[$k][$i + 1])) {
                    $color = $this->get_color_by_percentage(0);
                    $data[$k][$i + 1] = "<div style='background-color: $color; padding: .5rem'>0</div>";;
                }
            }
        }

        foreach ($data as $row) {
            ksort($row);
            $this->add_data($row);
        }
    }

    protected function get_color_by_percentage($per) {
        $r = 255;
        $g = $b = 255 - intval($per * 150);

        $str = "#";
        $str .= str_pad(dechex($r), 2, "0", STR_PAD_LEFT);
        $str .= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
        $str .= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);
        return $str;
    }
}