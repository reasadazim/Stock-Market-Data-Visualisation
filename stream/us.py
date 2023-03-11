# Get LIVE market data (from eodhistoricaldata.com) via socket and store it in a CSV file

import websocket
import json
import csv
import datetime
import pandas as pd
import os
import time
import logging

try:
    import thread
except ImportError:
    import _thread as thread

# For logging messages
logging.basicConfig(
    level=logging.DEBUG,
    format="{asctime} {levelname: <8} {message}",
    style="{",
    filename="streaming.log",
    filemode="a"
)

# Variable to ensure calling write_data_to_csv func 1 time after each minute
fire = 0
# Variable to store 1 minute stream data
data_filtered = []
previous_minute = 0


def on_message(ws, message):
    # Including global variables
    global data_filtered, fire, previous_minute

    # Convert data into json
    data = json.loads(message)

    # Convert timestamp to date and time
    if len(data) > 2:  # avoid other messages except data stream

        convert_timestamp_to_date_time = pd.to_datetime(data["t"], unit='ms')

        format_date_time = convert_timestamp_to_date_time.strftime('%Y-%m-%d %H:%M:%S')

        current_data_date_time = convert_timestamp_to_date_time.strftime('%Y-%m-%d %H:%M:%S%f')

        current_minute = convert_timestamp_to_date_time.strftime('%Y-%m-%d %H:%M:%S')

        # If previous minute is not set
        if previous_minute == 0:
            previous_minute = int(current_minute[-5:16])

        # If minute changed store data in CSV
        if previous_minute < int(current_minute[-5:16]):
            print("Running...")
            previous_minute = int(current_minute[-5:16])
            # ensure this data write function invokes 1 time after the end of the seconds
            # since we are dealing with milliseconds the function may be called so many times
            # to reduce this phenomenon, we are using a counter variable 'fire'
            if fire == 0:
                one_min_data = data_filtered
                data_filtered = []
                # Invoke data write to csv function
                write_data_to_csv(one_min_data)
                fire = 1
        # If minute changed store data in CSV (for 59 to next hours 00 minute)
        elif (previous_minute == 59) and (int(current_minute[-5:16]) == 00):
            print("Running...")
            previous_minute = int(current_minute[-5:16])
            # ensure this data write function invokes 1 time after the end of the seconds
            # since we are dealing with milliseconds the function may be called so many times
            # to reduce this phenomenon, we are using a counter variable 'fire'
            if fire == 0:
                one_min_data = data_filtered
                data_filtered = []
                # Invoke data write to csv function
                write_data_to_csv(one_min_data)
                fire = 1
        else:
            fire = 0

        # Store data in a 1 minute que
        # if ((int(current_data_date_time[-8:])) > 00000000) and ((int(current_data_date_time[-8:])) < 59990000):
        if previous_minute == int(current_minute[-5:16]):
            # Preparing data for inserting in CSV (1 minute data queue)
            data_filtered.append([data['s'], format_date_time, data['p']])


def write_data_to_csv(one_min_data):
    # ********** Write/store 1 minute stream data in CSV file **********
    logging.info("############################### US Trade ###############################")
    for data in one_min_data:
        # Set filename e.g. BTC-USD-2022-12-27-1m.csv
        date = datetime.datetime.utcnow().date()

        file_name = (str)(data[0]) + "-" + (str)(date) + "-1m.csv"

        # Write data in csv file
        f = open('stream/' + file_name, "a", encoding='UTF8', newline='')
        writer = csv.writer(f)
        writer.writerow([data[1], data[2]])
        # f.write(data_filtered  +  "\n" )
        f.close()

    # ********** END - Write/store 1 minute stream data in CSV file **********

    # Take a break
    time.sleep(5)

    # ********* Resample data and store it in data directory for frontend chart **************
    # Get the list of all files and directories
    path = "stream/"
    dir_list = os.listdir(path)

    for file_name in dir_list:
        # Get file_name size
        size = len(file_name)
        # Slice string to remove last 3 characters from string
        dir_name = file_name[:size - 18]

        path = "../data/realtime/1m/" + dir_name
        # Check whether the specified path exists or not
        is_exist = os.path.exists(path)
        if not is_exist:
            # Create a new directory because it does not exist
            os.makedirs(path)
            logging.info("The new directory ../data/realtime/1m/" + dir_name + " is created!")

        # Convert
        df = pd.read_csv('stream/' + file_name, names=['date', 'price'], index_col=0, parse_dates=True, header=None).fillna(0)
        df = pd.DataFrame(df)
        data = df['price'].resample('1min').ohlc()

        # Create resample data CSV file in data directory and remove existing stream files from stream directory
        path = 'stream/' + file_name
        is_exist = os.path.exists(path)
        if is_exist:
            # Write data in csv file inside data directory
            data.to_csv('../data/realtime/1m/' + dir_name + '/' + file_name, mode='a', header=False)
            logging.info("File: " + file_name + " updated for frontend.")

            # Remove processed files from stream directory
            os.remove('stream/' + file_name)
            logging.info("File " + file_name + " deleted from stream directory")
    # ********* END -Resample data and store it in data directory for frontend chart **************


# Print error if any
def on_error(ws, error):
    logging.info(error)
    # If connection is closed due to error, reconnect
    while not ws.keep_running:
        connect_websocket()
        time.sleep(3)


# On close print message
def on_close(ws):
    logging.info("### Closed ###")
    # If connection is closed, reconnect
    while not ws.keep_running:
        connect_websocket()
        time.sleep(3)


# Connect to eodhistoricaldata LIVE data stream
def on_open(ws):
    def run(*args):
        ws.send('{"action": "subscribe", "symbols": "AAPL, MSFT, TSLA"}')

    thread.start_new_thread(run, ())


def connect_websocket():
    ws = websocket.WebSocketApp("ws://ws.eodhistoricaldata.com/ws/us?api_token=90j6w31n68ka2.91057371",
                                on_message=on_message,
                                on_error=on_error,
                                on_close=on_close)

    ws.on_open = on_open
    ws.on_close = on_close
    ws.on_error = on_error

    # Keep alive socket
    ws.run_forever()


if __name__ == "__main__":
    connect_websocket()