<?php
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

list($insql, $params) = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED);

$sql = "SELECT MIN(ul.id) AS id, ul.contextid, yearcreated, monthcreated, daycreated, SUM(amount) AS amount
FROM {logstore_usage_log} ul
    INNER JOIN {context} con
ON ul.courseid = con.instanceid
    INNER JOIN (
        SELECT userid, MIN (roleid) as roleid
    FROM mdl_role_assignments
    GROUP BY userid, contextid
    ) r
    ON ul.userid = r.userid
WHERE courseid = :courseid
AND yearcreated * 10000 + monthcreated * 100 + daycreated >= :mindate
AND yearcreated * 10000 + monthcreated * 100 + daycreated <= :maxdate
AND r.roleid $insql
AND con.contextlevel = 50
    }
GROUP BY ul.contextid, yearcreated, monthcreated, daycreated
ORDER BY ul.contextid, yearcreated, monthcreated, daycreated";

var_dump(\report_usage\db_helper::get_data_from_course(2, 25, array(1, 5), '20190500', '20200000'));