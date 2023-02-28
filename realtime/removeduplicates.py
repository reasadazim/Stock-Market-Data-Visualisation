# Convert stream data to OHLC data and save it in resample directory
import pandas as pd
import os
import schedule
import shutil
import time


def job():
    # Get the list of all files and directories
    path = "resample/"
    dir_list = os.listdir(path)

    for file_name in dir_list:
        # Get file_name size
        size = len(file_name)
        # Slice string to remove last 3 characters from string
        dir_name = file_name[:size - 18]

        path = "../data/" + dir_name
        # Check whether the specified path exists or not
        is_exist = os.path.exists(path)
        if not is_exist:
            # Create a new directory because it does not exist
            os.makedirs(path)
            print("The new directory is created!")

        # Convert
        df = pd.read_csv('resample/' + file_name, names=['date', 'open', 'high', 'low', 'close'], index_col=0, parse_dates=True, header=None)
        df = pd.DataFrame(df)
        data = df.resample('1min').agg({'open': 'first', 'high': 'max', 'low': 'min', 'close': 'last'})
        print(df)




        # Remove existing resample data CSV file so that it can store new data
        path = 'resample/' + file_name
        is_exist = os.path.exists(path)
        if is_exist:
            print("File Deleted: "+file_name)
            os.remove('resample/' + file_name)

            # Write data in csv file
            data.to_csv('../data/' + dir_name + '/' + file_name, mode='a', header=False)

# Schedule to run the job in every 1 minutes
schedule.every().minute.at(":30").do(job)
# schedule.every(3).seconds.do(job)

while True:
    schedule.run_pending()
    time.sleep(1)
