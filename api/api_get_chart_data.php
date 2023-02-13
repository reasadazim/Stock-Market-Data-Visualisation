<?php
   
// Folloing code reads the file from a date till date and outputs JSON data for candlestick plot

// Setting timezone to UTC, Otherwise eodhistoricaldata.com does not provide data
date_default_timezone_set('UTC');

// Get query parameters
$start_date = $_GET["startDate"];
$end_date = $_GET["endDate"];
$crypto = $_GET["crypto"];
 

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








// ************* Get data from file and output as json *************

    $local_csv_file_name = "../data/".$crypto."/".$crypto."-data.csv"; 

    if(file_exists($local_csv_file_name)){

        $data = csvToJson($local_csv_file_name);

        if(($crypto == 'US2Y.INDX')||($crypto == 'BCOMCO.INDX')||$crypto == 'BCOMGC.INDX'){
            // For EOD data
            // Filter data -> converting miliseconds to year
            foreach($data as $datum){
                $mil = new DateTime($datum['Date']." 23:59:59"); //setting time manually for end of day
                $seconds = $mil->getTimestamp();
                $filtered_data[] = array(
                    'time' => $seconds,
                    'open' => (float)$datum['Open'],
                    'high' => (float)$datum['High'],
                    'low' => (float)$datum['Low'],
                    'close' => (float)$datum['Close'],
                );
            }
            // END - Filter data -> converting miliseconds to year

        }else{
            // For Intra day data
            // Filter data -> converting miliseconds to year
            foreach($data as $datum){
            $mil = new DateTime($datum['Datetime']);
            $seconds = $mil->getTimestamp();
            $filtered_data[] = array(
                'time' => $seconds,
                'open' => (float)$datum['Open'],
                'high' => (float)$datum['High'],
                'low' => (float)$datum['Low'],
                'close' => (float)$datum['Close'],
            );
            }
            // END - Filter data -> converting miliseconds to year 

        }

    }


// ************* END - Get data from file and output as json *************


// Output

header('Content-Type: application/json; charset=utf-8');

echo(json_encode($filtered_data));


?>




