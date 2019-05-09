SELECT * FROM mdl_context c
    LEFT JOIN mdl_course_modules cm ON c.instanceid = cm.id
WHERE c.contextlevel = 70
    AND  cm.course = :courseid
    AND cm.section = :section