import pandas as pd
df = pd.read_csv('realTimeMarketData.csv', names=['price', 'date'], index_col=1, parse_dates=True, header=None)
# df = pd.DataFrames(df)
data = df['price'].resample('1min').ohlc()
print(data)