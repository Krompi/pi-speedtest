#!/usr/bin/python
# -*- coding: utf-8 -*-

import MySQLdb
import subprocess
import datetime

# Daten holen
speedtest = subprocess.Popen(["/usr/bin/speedtest", "--simple"],
                               stdout=subprocess.PIPE,
                               universal_newlines=True
                            )

# Daten verarbeiten
input_lines = []
for line in speedtest.stdout:
    input_lines.append(line)
ping_key,ping_val,ping_unit = input_lines[0].split(" ")
download_key,download_val,download_unit = input_lines[1].split(" ")
upload_key,upload_val,upload_unit = input_lines[2].split(" ")
dt_log = datetime.datetime.today().strftime("%Y-%m-%d %H:%M:%S")

# Datenbank-Verbindung öffnen
db = MySQLdb.connect(host="localhost",
                     user="speedtest",
                     passwd="speedy",
                     db="speedtest")
cur = db.cursor()
# Daten einfügen
cur.execute("INSERT INTO results (time, ping, down, up) VALUES (%s, %s, %s, %s)", (dt_log, ping_val, download_val, upload_val))
# Datenbank-Verbindung schließen
cur.close()
db.commit()
db.close()
