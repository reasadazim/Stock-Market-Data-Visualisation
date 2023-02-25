import websocket
import time
import schedule
import shutil
import json
import csv
import datetime
import pandas as pd





try:
    import thread
except ImportError:
    import _thread as thread



def on_message(ws, message):
    # Convert data into json
    data = json.loads(message)
    # Convert timestamp to date and time
    convert_timestamp_to_date_time = pd.to_datetime(data["t"], unit='ms')
    format_date_time = convert_timestamp_to_date_time.strftime('%Y-%m-%d %H:%M:%S')
    # Preparing data for inserting in CSV
    data_filtered = [data['p'], format_date_time]
    # Delay
    print(data_filtered)
    # Write data in csv file
    f = open("realTimeMarketData.csv", "a", encoding='UTF8', newline='')
    writer = csv.writer(f)
    writer.writerow(data_filtered)
    # f.write(data_filtered  +  "\n" )
    f.close()


def on_error(ws, error):
    print(error)

def on_close(ws):
    print("### closed ###")

def on_open(ws):
    def run(*args):
        ws.send('{"action": "subscribe", "symbols": "BTC-USD"}')
    thread.start_new_thread(run, ())


if __name__ == "__main__":
    ws = websocket.WebSocketApp("ws://ws.eodhistoricaldata.com/ws/crypto?api_token=63e9be52e52de8.36159257",
                              on_message = on_message,
                              on_error = on_error,
                              on_close = on_close)
    ws.on_open = on_open
    ws.run_forever()


