<?php

// Folowing code call eodhistoricaldata.com Intraday Historical Data API
// Store the response file in CSV format in server  


// Setting timezone to UTC, Otherwise eodhistoricaldata.com does not provide data
    date_default_timezone_set('UTC');

    // Get query parameters
    $crypto = $_GET["crypto"];
    $duration = $_GET["duration"];

// ********************** Get the historical data from API response and store data in CSV file **********************

    // Setting API URL
    $remote_file_name = "https://eodhistoricaldata.com/api/eod/".$crypto."?api_token=90j6w31n68ka2.91057371&period=".$duration."";
    // echo $remote_file_name."<br>";

    //setting file name to save
    $local_csv_file_name = "../data/".$duration."/".$crypto."/".$crypto."-data.csv"; 
    
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

// Output
header('Content-Type: application/json; charset=utf-8');

echo("Success!");


?>