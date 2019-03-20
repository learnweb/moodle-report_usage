import records
import math
from noise import pnoise1
from datetime import datetime, timedelta

time = datetime.now()

db = records.Database('postgres://testdb:hello@localhost:32768/testdb')

db.query('TRUNCATE TABLE mdl_logstore_usage_log RESTART IDENTITY')

sql = "INSERT INTO mdl_logstore_usage_log "
sql += "(objecttable, objectid, contextid, userid, courseid, amount, daycreated, monthcreated, yearcreated) VALUES "
sql += "('resource', '1', ':contextid', ':userid', '2', ':amount', ':day', ':month', ':year')"

users = [2]
contextids = [26, 28, 29, 30]

days = 100
for i in range(days):
  for c in contextids:
    for u in users:
      amount = int((pnoise1(i * 0.123, base=u*c) + 1) * 20)
      if c == 28:
        amount /= 6
      if amount != 0:
        db.query(sql, contextid=c, userid=u, amount=amount, day=time.day, month=time.month, year=time.year)
  time -= timedelta(days=1)