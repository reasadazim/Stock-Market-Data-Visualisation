import pandas as pd
import os

# ********* Reads 1 month CSV Resample data to 3 months and store it in data directory for frontend chart **************
# Get the list of all files and directories
tickers = ['AAPL.US', 'MSFT.US', 'TSLA.US', 'EURUSD.FOREX', 'ETH-USD.CC', 'BTC-USD.CC', 'GSPC.INDX', 'IXIC.INDX',
           'FTSE.INDX', 'DJI.INDX', 'NDX.INDX']

for ticker in tickers:
    path = "../data/m/" + ticker + "/"
    dir_list = os.listdir(path)

    for file_name in dir_list:
        # Get file_name size
        size = len(file_name)
        # Slice string to remove last 3 characters from string

        # Convert
        df = pd.read_csv(path + file_name)
        df['Date'] = pd.to_datetime(df['Date'])
        df = pd.DataFrame(df)
        data = df.resample('12M', on='Date').agg({'Open': 'first',
                                                 'High': 'max',
                                                 'Low': 'min',
                                                 'Close': 'last',
                                                 'Adjusted_close': 'last',
                                                 'Volume': 'sum',
                                                 })

        # Write data in csv file inside data directory
        data.to_csv('../data/1y/' + ticker + "/" + file_name, mode='w', header=True)
# ********* Reads 1 month CSV Resample data to 3 months and store it in data directory for frontend chart **************
