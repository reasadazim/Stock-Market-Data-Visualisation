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

    $local_csv_file_name_live = "../data/".$crypto."/".$crypto."-live-data.csv"; 

    if(file_exists($local_csv_file_name_live)){
        $data = csvToJson($local_csv_file_name_live);
    }


 
    

    if(($crypto == 'US2Y.INDX')||($crypto == 'BCOMCO.INDX')||$crypto == 'BCOMGC.INDX'){
        $date_format = str_replace("/","-",$data[0]); //remove / and replace with -
        $date = new DateTime($date_format." 23:59:59"); // convert date to timestamp
        $timestamp = $date->getTimestamp();
        // Filtered cell data 
        $filtered ['time'] = (int)$timestamp;
        $filtered ['open'] = (float)$data[1];
        $filtered ['high'] = (float)$data[2];
        $filtered ['low'] = (float)$data[3];
        $filtered ['close'] = (float)$data[4];
    }else{


        if(is_null($data[7])){
            // If volume is empty
            $volume = 0;
        }else{
            $volume = $data[7];
        }

        // Determine volume bar color
        // if the closing price is greater than the open price then GREEN else RED
        if ( ((float)$data[6]) > ((float)$data[3]) ){
            $color = "rgb(54, 217, 122)";
        }else{
            $color = "rgb(225, 50, 85)";
        }

        // Filtered cell data 
        $filtered ['time'] = (int)$data[0];
        $filtered ['open'] = (float)$data[3];
        $filtered ['high'] = (float)$data[4];
        $filtered ['low'] = (float)$data[5];
        $filtered ['close'] = (float)$data[6];
        $filtered ['volume'] = $volume;
        $filtered ['color'] = $color;
    }



// ************* END - Get data from file and output as json *************


// Output

header('Content-Type: application/json; charset=utf-8');

echo(json_encode($filtered));


?>




