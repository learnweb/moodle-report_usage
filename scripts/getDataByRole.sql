SELECT MIN(ul.id) AS id, ul.contextid, yearcreated, monthcreated, daycreated, SUM(amount) AS amount
FROM mdl_logstore_usage_log ul
LEFT JOIN
	(
   	SELECT id AS coursecontextid, instanceid
   	FROM mdl_context
   	WHERE contextlevel = 50 AND instanceid = :courseid
	) c
	ON ul.courseid = c.instanceid
LEFT JOIN mdl_role_assignments r
	ON ul.userid = r.userid
WHERE courseid = :courseid AND yearcreated * 10000 + monthcreated * 100 + daycreated >= :mindate
  AND yearcreated * 10000 + monthcreated * 100 + daycreated <= :maxdate
  AND r.roleid = :roleid
  --AND ul.contextid IN $contextlist //Für Abschnitte?
GROUP BY ul.contextid, yearcreated, monthcreated, daycreated
ORDER BY ul.contextid, yearcreated, monthcreated, daycreated
