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

$local_csv_file_name_live = "../data/realtime/1m/".$crypto."/".$crypto."-".date('Y-m-d')."-1m.csv";

    if(file_exists($local_csv_file_name_live)){
        $data = csvToJson($local_csv_file_name_live);
    }

    // if(is_null($data[8])){
    //     // If volume is empty
    //     $volume = 0;
    // }else{
    //     $volume = $data[8];
    // }

    // // Determine volume bar color
    // // if the closing price is greater than the open price then GREEN else RED
    // if ( ((float)$data[7]) > ((float)$data[4]) ){
    //     $color = "#36d97aa6";
    // }else{
    //     $color = "#e13255ab";
    // }

    // Filtered cell data 
    $filtered ['time'] = strtotime($data[0]);
    $filtered ['open'] = (float)$data[1];
    $filtered ['high'] = (float)$data[2];
    $filtered ['low'] = (float)$data[3];
    $filtered ['close'] = (float)$data[4];
    // $filtered ['volume'] = $volume;
    // $filtered ['color'] = $color;


// ************* END - Get data from file and output as json *************


// Output

header('Content-Type: application/json; charset=utf-8');

echo(json_encode($filtered));


?>




