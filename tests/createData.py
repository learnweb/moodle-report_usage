import records
import math
import noise
from datetime import datetime, timedelta

time = datetime.now()

db = records.Database('postgres://testdb:hello@localhost:32768/testdb')

db.query('TRUNCATE TABLE mdl_logstore_usage_log RESTART IDENTITY')
db.query('DELETE FROM mdl_logstore_usage_log');

sql = "INSERT INTO mdl_logstore_usage_log "
sql += "(objecttable, objectid, contextid, userid, courseid, amount, daycreated, monthcreated, yearcreated) VALUES "
sql += "('resource', '1', ':contextid', ':userid', ':courseid', ':amount', ':day', ':month', ':year')"

courseid = 2
contextid = [26, 27, 28, 29]
userid = [2,3]


for i in range(100):
  time -= timedelta(days=1)
  print(time.year, time.month, time.day)
  for c in contextid:
    for u in userid:
      #amount = int((-math.cos(i*0.03) + 1) * 50)
      amount = int((noise.pnoise1(i * 0.133, base=u*c) + 1) * 20)
      if c == 27:
        amount /= 6
      db.query(sql, contextid=c, userid=u, courseid=courseid, amount=amount, day=time.day, month=time.month, year=time.year)
