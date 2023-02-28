# Get LIVE market data (from eodhistoricaldata.com) via socket and store it in a CSV file

import websocket
import json
import csv
import datetime
import pandas as pd
import os
import shutil
import time

try:
    import thread
except ImportError:
    import _thread as thread



def on_message(ws, message):
    # Convert data into json
    data = json.loads(message)

    # Convert timestamp to date and time
    if len(data) == 6:  # avoid other messages except data stream
        convert_timestamp_to_date_time = pd.to_datetime(data["t"], unit='ms')
        format_date_time = convert_timestamp_to_date_time.strftime('%Y-%m-%d %H:%M:%S')

        # Preparing data for inserting in CSV
        data_filtered = [data['p'], format_date_time]

        # Delay
        print(data_filtered)

        # Set filename e.g. BTC-USD-2022-12-27-1m.csv
        date = datetime.date.today()
        # print(date)
        file_name = (str)(data['s']) + "-" + (str)(date) + "-1m.csv"

        # Write data in csv file
        f = open('stream/' + file_name, "a", encoding='UTF8', newline='')
        writer = csv.writer(f)
        writer.writerow(data_filtered)
        # f.write(data_filtered  +  "\n" )
        f.close()

        y = convert_timestamp_to_date_time.strftime('%Y-%m-%d %H:%M:%S')

        current_minute = [0]

        if (int(y[14:-3])) > current_minute[0]:

            current_minute[0] = (int(y[14:-3]))

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



# Print error if any
def on_error(ws, error):
    print(error)


#
def on_close(ws):
    print("### closed ###")


# Connect to eodhistoricaldata LIVE data stream
def on_open(ws):
    def run(*args):
        ws.send('{"action": "subscribe", "symbols": "BTC-USD, ETH-USD"}')

    thread.start_new_thread(run, ())


if __name__ == "__main__":
    ws = websocket.WebSocketApp("wss://ws.eodhistoricaldata.com/ws/crypto?api_token=63e9be52e52de8.36159257",
                                on_message=on_message,
                                on_error=on_error,
                                on_close=on_close)
    ws.on_open = on_open

    # Keep alive socket
    ws.run_forever()
