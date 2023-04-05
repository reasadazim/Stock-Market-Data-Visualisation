<?php
   
// Folloing code reads the a CSV file and outputs JSON data to make candlestick chart

// Setting timezone to UTC, Otherwise eodhistoricaldata.com does not provide data
date_default_timezone_set('UTC');

// Get query parameters
$crypto = $_GET["crypto"];
$duration = $_GET["duration"];
 
// php function to convert csv to json format
function csvToJson($fname) {
    // open csv file
    if (!($fp = fopen($fname, 'r'))) {
        die("Can't open file...");
    }
    
    //read csv headers
    $key = fgetcsv($fp,"1024",",");
    
    // parse csv rows into array
    $json = array();
        while ($row = fgetcsv($fp,"1024",",")) {
        $json[] = array_combine($key, $row);
    }
    
    // release file handle
    fclose($fp);
    
    // encode array to json
    return $json;
}

// END - php function to convert csv to json format

// ************* Get data from CSV file and output as json *************

    $local_csv_file_name = "../data/".$duration."/".$crypto."/".$crypto."-data.csv"; 

    if(file_exists($local_csv_file_name)){

        $data = csvToJson($local_csv_file_name);

        foreach($data as $datum){
        
            if( (float)$datum['Open'] != 0 ){

                if(is_null($datum['Volume'])){
                    // If volume is empty
                    $volume = 0;
                }else{
                    $volume = $datum['Volume'];
                }

                // Determine volume bar color
                // if the closing price is greater than the open price then GREEN else RED
                if ( ((float)$datum['Close']) > ((float)$datum['Open']) ){
                    $color = "#36d97aa6";
                }else{
                    $color = "#e13255ab";
                }

            
                $filtered_data[] = array(
                    'time' => (int)(strtotime($datum['Date'])),
                    'open' => (float)$datum['Open'],
                    'high' => (float)$datum['High'],
                    'low' => (float)$datum['Low'],
                    'close' => (float)$datum['Close'],
                    'volume' => $volume,
                    'color' => $color,
                );
            }
        
        }

    }
    
// ************* END - Get data from file and output as json *************


// Output
header('Content-Type: application/json; charset=utf-8');

echo(json_encode($filtered_data));

?>