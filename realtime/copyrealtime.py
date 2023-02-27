# Copy 1 minute stream data CSV file into another CSV file for data processing (stream to OHLC conversion)

import os
import schedule
import shutil
import time


def job():

    # Get the list of all files and directories
    path = "stream/"
    dir_list = os.listdir(path)
    for file_name in dir_list:
        # Copy the CSV file for data processing (stream to OHLC)
        print("File Copied")
        shutil.copy('stream/' + file_name, 'copy/' + file_name)

        # Remove existing stream data CSV file so that it can store new data
        print("File Deleted")
        os.remove('stream/' + file_name)


# Schedule to run the job in every 1 minutes
schedule.every().minute.at(":59").do(job)

while True:
    schedule.run_pending()
    time.sleep(1)