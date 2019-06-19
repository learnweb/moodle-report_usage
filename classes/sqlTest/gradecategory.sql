SELECT con.id
FROM mdl_grade_items gi
         JOIN mdl_modules m
              ON gi.itemmodule = m.name
         JOIN mdl_course_modules cm
              ON cm.module = m.id AND cm.instance = gi.iteminstance
         JOIN mdl_context con
              ON con.instanceid = cm.id
WHERE gi.categoryid IN (2)
  AND con.contextlevel = 70