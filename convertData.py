# Convert stream data to OHLC data and save it for frontend render
import pandas as pd
import csv
import os


# Convert
df = pd.read_csv('realTimeMarketData.csv', names=['price', 'date'], index_col=1, parse_dates=True, header=None)
data = df['price'].resample('1min').ohlc()
print(data)

# Write data in csv file
data.to_csv('resampledData.csv', mode='a', header=False)