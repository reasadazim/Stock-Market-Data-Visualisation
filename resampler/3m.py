import pandas as pd
import os

# ********* Reads 1 month CSV Resample data to 3 months and store it in data directory for frontend chart **************

# Get the list of all files and directories
# Open the text file for reading
with open("../data/active_tickers.txt", "r") as file:
    # Read the contents of the file into a list
    contents = file.readlines()

# Create an array from the list of file contents
tickers = []
for line in contents:
    tickers.append(line.strip())

for ticker in tickers:
    path = "../data/1m/" + ticker + "/"
    dir_list = os.listdir(path)

    for file_name in dir_list:
        # Get file_name size
        size = len(file_name)
        # Slice string to remove last 3 characters from string

        # Convert
        df = pd.read_csv(path + file_name)
        df['Date'] = pd.to_datetime(df['Date'])
        df = pd.DataFrame(df)
        data = df.resample('3M', on='Date').agg({'Open': 'first',
                                                 'High': 'max',
                                                 'Low': 'min',
                                                 'Close': 'last',
                                                 'Adjusted_close': 'last',
                                                 'Volume': 'sum',
                                                 })
        
        # Check the directory exists
        if not os.path.exists('../data/3m/' + ticker):
            # if the demo_folder directory is not present 
            # then create it.
            os.makedirs('../data/3m/' + ticker)

        # Write data in csv file inside data directory
        data.to_csv('../data/3m/' + ticker + "/" + file_name, mode='w', header=True)
# ********* Reads 1 month CSV Resample data to 3 months and store it in data directory for frontend chart **************
