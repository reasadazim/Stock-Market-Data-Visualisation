<?php

// Folowing code call eodhistoricaldata.com Intraday Historical Data API
// Store the response file in CSV format in server  

// Setting timezone to UTC, Otherwise eodhistoricaldata.com does not provide data
date_default_timezone_set('UTC');

    // Get query parameters
    $crypto = $_GET["crypto"];


    $remote_file_name = "https://eodhistoricaldata.com/api/real-time/".$crypto."?api_token=63e9be52e52de8.36159257&fmt=json&filter=timestamp,gmtoffset,open,high,low,close,volume";

    //setting file name to save
    $local_csv_file_name = "../data/".$crypto."/".$crypto."-data.csv"; 
    

    // Call API
    $ch = curl_init();
    $source = $remote_file_name; 
    curl_setopt($ch, CURLOPT_URL, $source);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec ($ch);
    curl_close ($ch);


    $response = json_decode($data, true); //convert json data type to array
 
    $date = date("d/m/Y h:i", $response[0]); // convert timestamp to date & time

    array_splice( $response, 2, 0, $date); //convert date time from the timestamp and push into thea array

    $handle = fopen($local_csv_file_name, "a");
    fputcsv($handle, $response); # $line is an array of strings (array|string[])
    fclose($handle);

// ********************** END - Get the API response and store data in CSV file **********************

?>