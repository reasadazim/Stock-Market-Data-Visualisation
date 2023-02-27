# Convert stream data to OHLC data and save it for frontend render
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
        # Get file_name size
        size = len(file_name)
        # Slice string to remove last 3 characters from string
        mod_string = file_name[:size - 18]

        path = "../data/" + mod_string
        # Check whether the specified path exists or not
        isExist = os.path.exists(path)
        if not isExist:
            # Create a new directory because it does not exist
            os.makedirs(path)
            print("The new directory is created!")

        # Convert
        df = pd.read_csv('copy/' + file_name, names=['price', 'date'], index_col=1, parse_dates=True, header=None)
        df = pd.DataFrame(df)
        data = df['price'].resample('1min').ohlc()
        print(data)

        # Remove existing copy data CSV file so that it can store new data
        path = 'copy/' + file_name
        is_exist = os.path.exists(path)
        if is_exist:
            print("File Deleted")
            os.remove('copy/' + file_name)

        # Write data in csv file
        data.to_csv('../data/' + mod_string + '/' + dir_list[0], mode='a', header=False)

# Schedule to run the job in every 1 minutes
schedule.every().minute.at(":15").do(job)

while True:
    schedule.run_pending()
    time.sleep(1)
