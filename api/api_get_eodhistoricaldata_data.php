<?php

// Folowing code call eodhistoricaldata.com Intraday Historical Data API
// Store the response file in CSV format in server  


// Setting timezone to UTC, Otherwise eodhistoricaldata.com does not provide data
    date_default_timezone_set('UTC');

    // Get query parameters
    $start_date = $_GET["startDate"];
    $end_date = $_GET["endDate"];
    $crypto = $_GET["crypto"];

    // echo $start_date."<br>";
    // echo $end_date."<br>";
    // echo $crypto."<br>";

// ********************** Get the API response and store data in CSV file **********************

    $start_date_time_local = new DateTime($start_date);
    $from = $start_date_time_local->getTimestamp();

    $end_date_time_local = new DateTime($end_date);
    $to = $end_date_time_local->getTimestamp();

    // Setting API URL
    $remote_file_name = "https://eodhistoricaldata.com/api/intraday/".$crypto."?api_token=63e9be52e52de8.36159257&interval=5m&from=".$from."&to=".$to."";
    // echo $remote_file_name."<br>";
    //setting file name to save
    $local_csv_file_name = "../data/".$crypto."/".$crypto."-data.csv"; 
    
    //Delete the existing file so that we can store new file.
    unlink($local_csv_file_name); 

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

// ********************** END - Get the API response and store data in CSV file **********************

?>