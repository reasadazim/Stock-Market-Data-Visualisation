<?php

// Folowing code call eodhistoricaldata.com Intraday Historical Data API
// Store the response file in CSV format in server  

// Get the list of ticker code (feed=1) 
include('../dbconnect/credentials.php'); //database connector

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT * FROM te");
    $stmt->execute();
  
    // set the resulting array to associative
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    foreach((new RecursiveArrayIterator($stmt->fetchAll())) as $k => $v) {
      if($v['feed'] == 1){
        fetch_eod_data($v['ref']); //fetch data from eodhistorical.com
        $ticker_list[] = $v['ref']; //store the 'ref' value (ticker code) for python to read
      }
    }

    // Store the ticker list text file (for python to resample only the newly imported data and do not resample again the existing data)
    $file = fopen('../data/active_tickers.txt', "w+");
    foreach ($ticker_list as $ticker){
        fputs($file, $ticker.PHP_EOL);
    }
    fclose($file);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}


function fetch_eod_data($tickercode){

// Setting timezone to UTC, Otherwise eodhistoricaldata.com does not provide data
date_default_timezone_set('UTC');

$crypto = $tickercode;

$durations = ["d", "m", "w"]; // eodhistoricaldata.com does not provide 3month, 6month and 1 year data. We have to resample it using python.

    foreach ($durations as $duration){

        // ********************** Get the historical data from API response and store data in CSV file **********************

        // Setting API URL
        $remote_file_name = "https://eodhistoricaldata.com/api/eod/".$crypto."?api_token=90j6w31n68ka2.91057371&period=".$duration."";
        // echo $remote_file_name."<br>";

        // If directory does not exists create it
        if ( !is_dir( "../data/1".$duration."/".$crypto ) ) {
            mkdir( "../data/1".$duration."/".$crypto );       
        }

        //setting file name to save
        $local_csv_file_name = "../data/1".$duration."/".$crypto."/".$crypto."-data.csv"; 

        if(file_exists($local_csv_file_name)){
            //Delete the existing file so that we can store new file.
            unlink($local_csv_file_name); 
        }

        // Call API
        $ch = curl_init();
        $source = $remote_file_name; 
        curl_setopt($ch, CURLOPT_URL, $source);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec ($ch);
        curl_close ($ch);

        // Store the data file (CSV)
        $destination = $local_csv_file_name;
        $file = fopen($destination, "w+");
        fputs($file, $data);
        fclose($file);

        // ******************** END - Get the historical data from API response and store data in CSV file ********************

    }

}


// Output
header('Content-Type: application/json; charset=utf-8');

echo("Success!");


?>