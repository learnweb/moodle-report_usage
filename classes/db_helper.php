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
 * Helper class with methods to query the database
 *
 * @package   report_usage
 * @copyright 2019 Justus Dieckmann
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_usage;

defined('MOODLE_INTERNAL') || die();

/**
 * Class filter_form form to filter the results by date
 * @package report_outline
 */
class db_helper {

    public static function get_roles_in_course($coursecontextid) {
        global $DB;

        $sql = "SELECT DISTINCT r.id, r.shortname
                FROM {role_assignments} ra
                INNER JOIN {role} r ON r.id = ra.roleid
                WHERE contextid = ?";

        return $DB->get_records_sql_menu($sql, array($coursecontextid));
    }

    public static function get_data_from_course($courseid, $coursecontextid, $roles, $mindate, $maxdate) {
        global $DB;
        $sql = "SELECT MIN(ul.id) AS id, ul.contextid, yearcreated, monthcreated, daycreated, SUM(amount) AS amount
                FROM {logstore_usage_log} ul ";

        $params = [];

        if ($roles != null && count($roles) != 0) {
            $sql .= "INNER JOIN {context} con
                    ON ul.courseid = con.instanceid
                  INNER JOIN (
                    SELECT userid, MIN(roleid) as roleid
                    FROM  mdl_role_assignments
                    GROUP BY userid, contextid
                  ) r
                    ON ul.userid = r.userid ";
        }
        $sql .= "WHERE courseid = :courseid
                  AND yearcreated * 10000 + monthcreated * 100 + daycreated >= :mindate
                  AND yearcreated * 10000 + monthcreated * 100 + daycreated <= :maxdate ";

        if ($roles != null && count($roles) != 0) {
            list($insql, $params) = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED);

            $sql .= "AND r.roleid $insql
                     AND con.contextlevel = 50 ";
        }

        $sql .= "GROUP BY ul.contextid, yearcreated, monthcreated, daycreated
                ORDER BY ul.contextid, yearcreated, monthcreated, daycreated ";

        $params = array_merge($params, array(
            'courseid' => $courseid,
            'coursecontextid' => $coursecontextid,
            'mindate' => $mindate,
            'maxdate' => $maxdate
        ));
        return $DB->get_records_sql($sql, $params);
    }

    public static function get_processed_data_from_course($courseid, $coursecontextid, $roles, $mindatestamp, $maxdatestamp) {
        $startdate = new \DateTime("now", \core_date::get_server_timezone_object());
        $startdate->setTimestamp($mindatestamp);

        $enddate = new \DateTime("now", \core_date::get_server_timezone_object());
        $enddate->setTimestamp($maxdatestamp);

        $days = intval($startdate->diff($enddate)->format('%a'));

        $records = self::get_data_from_course($courseid, $coursecontextid, $roles, $startdate->format("Ymd"), $enddate->format("Ymd"));

        $data = [];
        $deletedids = [];

        // Create table from records.
        foreach ($records as $v) {
            if (!in_array($v->contextid, $deletedids)) {
                if (!isset($data[$v->contextid])) {
                    $context = \context::instance_by_id($v->contextid, IGNORE_MISSING);
                    if (!$context) {
                        $deletedids[] = $v->contextid;
                        continue;
                    }
                    $data[$v->contextid] = [];
                }

                $diff = new \DateTime("$v->daycreated-$v->monthcreated-$v->yearcreated");
                $datediff = intval($diff->diff($startdate, true)->format("%a"));
                $data[$v->contextid][$datediff] = $v->amount;
            }
        }

        // Fill empty cells with 0.
        for ($i = 0; $i <= $days; $i++) {
            foreach ($data as $k => $v) {
                if (!isset($data[$k][$i])) {
                    $data[$k][$i] = 0;
                }
            }
        }

        foreach ($data as &$row) {
            ksort($row);
        }
        return $data;
    }

}
