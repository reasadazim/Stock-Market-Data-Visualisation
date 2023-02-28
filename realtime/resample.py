# Convert stream data to OHLC data and save it in resample directory
import pandas as pd
import os
import schedule
import shutil
import time


def job():
    # Get the list of all files and directories
    path = "copy/"
    dir_list = os.listdir(path)

    for file_name in dir_list:
        print(dir_list)
        # Convert
        df = pd.read_csv('copy/' + file_name, names=['price', 'date'], index_col=1, parse_dates=True, header=None)
        df = pd.DataFrame(df)
        data = df['price'].resample('1min').agg({'open': 'first', 'high': 'max', 'low': 'min', 'close': 'last'})
        # print(data)

        # Remove existing copy data CSV file so that it can store new data
        path = 'copy/' + file_name
        is_exist = os.path.exists(path)
        if is_exist:
            print("File Deleted: "+path)
            os.remove('copy/' + file_name)

            # Write data in csv file
            data.to_csv('resample/' + file_name, mode='a', header=False)

# Schedule to run the job in every 1 minutes
schedule.every().minute.at(":15").do(job)

while True:
    schedule.run_pending()
    time.sleep(1)
