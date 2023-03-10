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

        if(($crypto == 'US2Y.INDX')||($crypto == 'BCOMCO.INDX')||($crypto == 'BCOMGC.INDX')){
            // For EOD data
            // Filter data -> converting miliseconds to year
            foreach($data as $datum){

                $date_format = str_replace("/","-",$datum['Date']); //remove / and replace with -
                $mil = new DateTime($date_format." 23:59:59"); //setting time manually for end of day
                $seconds = $mil->getTimestamp();

                // date to milliseconds
                $date_from = date(substr($start_date, 0, -9)); // trimming 2023-02-14+00:00:00 to 2023-02-14
                $mil_from = new DateTime($date_from." 23:59:59"); // adding manual time for EOD since they do not gives time in their API
                $seconds_from = $mil_from->getTimestamp();

                // End date to milliseconds
                $date_to = date(substr($end_date, 0, -9)); // trimming 2023-02-14+00:00:00 to 2023-02-14;
                $mil_to = new DateTime($date_to);
                $seconds_to = $mil_to->getTimestamp();


                if(($seconds_from < $seconds)&&($seconds_to > $seconds)){ //check the time is within the time selected time frame
                    if( (float)$datum['Open'] != 0 ){
                        $filtered_data[] = array(
                            'time' => $seconds,
                            'open' => (float)$datum['Open'],
                            'high' => (float)$datum['High'],
                            'low' => (float)$datum['Low'],
                            'close' => (float)$datum['Close'],
                        );
                    }
                }
            }
            // END - Filter data -> converting miliseconds to year

        }else{
                // For Intra day data

                foreach($data as $datum){
                
                // Start date to milliseconds
                $date_from = date($start_date);
                $mil_from = new DateTime($date_from);
                $seconds_from = $mil_from->getTimestamp();

                // End date to milliseconds
                $date_to = date($end_date);
                $mil_to = new DateTime($date_to);
                $seconds_to = $mil_to->getTimestamp();
            

                if(($seconds_from<(int)$datum['Timestamp'])&&($seconds_to>(int)$datum['Timestamp'])){ //check the time is within the time selected time frame
                    if( (float)$datum['Open'] != 0 ){
                        $filtered_data[] = array(
                            'time' => (int)$datum['Timestamp'],
                            'open' => (float)$datum['Open'],
                            'high' => (float)$datum['High'],
                            'low' => (float)$datum['Low'],
                            'close' => (float)$datum['Close'],
                        );
                    }
                }
            }
            // END - For Intra day data

        }

    }


// ************* END - Get data from file and output as json *************






// ************* Get LIVE data from file and output as json *************

$local_csv_file_name_live = "../data/".$crypto."/".$crypto."-live-data.csv"; 

if(file_exists($local_csv_file_name_live)){

    $data = csvToJson($local_csv_file_name_live);

    if(($crypto == 'US2Y.INDX')||($crypto == 'BCOMCO.INDX')||($crypto == 'BCOMGC.INDX')){
        // For EOD data
        // Filter data -> converting miliseconds to year
        foreach($data as $datum){

            $date_format = str_replace("/","-",$datum['Date']); //remove / and replace with -
            $mil = new DateTime($date_format." 23:59:59"); //setting time manually for end of day
            $seconds = $mil->getTimestamp();

            // date to milliseconds
            $date_from = date(substr($start_date, 0, -9)); // trimming 2023-02-14+00:00:00 to 2023-02-14
            $mil_from = new DateTime($date_from." 23:59:59"); // adding manual time for EOD since they do not gives time in their API
            $seconds_from = $mil_from->getTimestamp();

            // End date to milliseconds
            $date_to = date(substr($end_date, 0, -9)); // trimming 2023-02-14+00:00:00 to 2023-02-14;
            $mil_to = new DateTime($date_to);
            $seconds_to = $mil_to->getTimestamp();


            if(($seconds_from < $seconds)&&($seconds_to > $seconds)){ //check the time is within the time selected time frame
                if( (float)$datum['Open'] != 0 ){
                    $filtered_data[] = array(
                        'time' => $seconds,
                        'open' => (float)$datum['Open'],
                        'high' => (float)$datum['High'],
                        'low' => (float)$datum['Low'],
                        'close' => (float)$datum['Close'],
                    );
                }
            }
        }
        // END - Filter data -> converting miliseconds to year

    }else{
            // For Intra day data

            foreach($data as $datum){
            
            // Start date to milliseconds
            $date_from = date($start_date);
            $mil_from = new DateTime($date_from);
            $seconds_from = $mil_from->getTimestamp();

            // End date to milliseconds
            $date_to = date($end_date);
            $mil_to = new DateTime($date_to);
            $seconds_to = $mil_to->getTimestamp();
        

            if(($seconds_from<(int)$datum['Timestamp'])&&($seconds_to>(int)$datum['Timestamp'])){ //check the time is within the time selected time frame
                if( (float)$datum['Open'] != 0 ){
                    $filtered_data[] = array(
                        'time' => (int)$datum['Timestamp'],
                        'open' => (float)$datum['Open'],
                        'high' => (float)$datum['High'],
                        'low' => (float)$datum['Low'],
                        'close' => (float)$datum['Close'],
                    );
                }
            }
        }
        // END - For Intra day data

    }

}


// ************* END - Get LIVE data from file and output as json *************







// Output

header('Content-Type: application/json; charset=utf-8');

echo(json_encode($filtered_data));


?>




