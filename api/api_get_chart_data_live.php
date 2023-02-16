<?php
   
// Folloing code reads the file from a date till date and outputs JSON data for candlestick plot
// Here we are reading the last row data in order to plot live data

// Setting timezone to UTC, Otherwise eodhistoricaldata.com does not provide data
date_default_timezone_set('UTC');

// Get query parameters
$crypto = $_GET["crypto"];
 

// php function to convert csv to json format, we are reading last row data from CSV to update the candlestick chart
function csvToJson($fname) {
    // open csv file
    if (!($fp = fopen($fname, 'r'))) {
        die("Can't open file...");
    }
    
    // Get last row data
    $rows = file($fname);
    $last_row = array_pop($rows);
    $data = str_getcsv($last_row);

    // release file handle
    fclose($fp);
    
    // encode array to json
    return $data;
}
// END - php function to convert csv to json format, we are reading last row data from CSV to update the candlestick chart





// ************* Get data from file and output as json *************

    $local_csv_file_name = "../data/".$crypto."/".$crypto."-data.csv"; 

    if(file_exists($local_csv_file_name)){
        $data = csvToJson($local_csv_file_name);
    }

    // Filtered cell data 
    $filtered ['time'] = (int)$data[0];
    $filtered ['open'] = (float)$data[3];
    $filtered ['high'] = (float)$data[4];
    $filtered ['low'] = (float)$data[5];
    $filtered ['close'] = (float)$data[6];


// ************* END - Get data from file and output as json *************


// Output

header('Content-Type: application/json; charset=utf-8');

echo(json_encode($filtered));


?>




