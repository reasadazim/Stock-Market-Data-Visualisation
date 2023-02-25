import os
import schedule
import shutil
import time


def job():
    print("File Copied")
    shutil.copy('realTimeMarketData.csv', 'convert.csv')

schedule.every(1).minutes.do(job)

while True:
    schedule.run_pending()
    time.sleep(1)