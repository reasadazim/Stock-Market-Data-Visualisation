# Copy 1 minute stream data CSV file into another CSV file for data processing (stream to OHLC conversion)

import os
import schedule
import shutil
import time


def job():
    # Copy the CSV file for data processing (stream to OHLC)
    print("File Copied")
    shutil.copy('realTimeMarketData.csv', 'realTimeMarketData-copy.csv')
    # Remove existing stream data CSV file so that it can store new data
    print("File Deleted")
    os.remove('realTimeMarketData.csv')

# Schedule to run the job in every 1 minutes
schedule.every(1).minutes.do(job)

while True:
    schedule.run_pending()
    time.sleep(1)