<?php

// Folowing code call eodhistoricaldata.com Intraday Historical Data API
// Store the response file in CSV format in server  

// Setting timezone to UTC, Otherwise eodhistoricaldata.com does not provide data
date_default_timezone_set('UTC');

    // Get query parameters
    $crypto = $_GET["crypto"];


    $remote_file_name = "https://eodhistoricaldata.com/api/real-time/".$crypto."?api_token=63e9be52e52de8.36159257&fmt=json&filter=timestamp,gmtoffset,open,high,low,close,volume";
    var_dump($remote_file_name);
    //setting file name to save
    $local_csv_file_name_live = "../data/".$crypto."/".$crypto."-live-data.csv"; 
    

    // Call API
    $ch = curl_init();
    $source = $remote_file_name; 
    curl_setopt($ch, CURLOPT_URL, $source);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec ($ch);
    curl_close ($ch);


    $response = json_decode($data, true); //convert json data type to array
 
    $date = date("Y-m-d h:i:s", (int)$response[0]); // convert timestamp to date & time

    array_splice( $response, 2, 0, $date); //convert date time from the timestamp and push into thea array




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

    if(file_exists($local_csv_file_name_live)){
        $data = csvToJson($local_csv_file_name_live);
    }
    // END - php function to convert csv to json format, we are reading last row data from CSV to update the candlestick chart

    // If timestamps are same then do nothing, duplicate timestamp breaks the chart

    if($data[0]==$response[0]){

    }else{
        if(($crypto == 'US2Y.INDX')||($crypto == 'BCOMCO.INDX')||$crypto == 'BCOMGC.INDX'){
            if($response[0]!="NA"){//if data available
                if($data[0]==(date("Y-m-d", ($response[0])))){

                }else{
                    // Filtering data for EOD data
                    $filtered[] = substr($response[2], 0, -9);
                    $filtered[] = $response[3];
                    $filtered[] = $response[4];
                    $filtered[] = $response[5];
                    $filtered[] = $response[6];
                    $filtered[] = $response[6];
                    $filtered[] = $response[7];
    
                    $handle = fopen($local_csv_file_name_live, "a");
                    fputcsv($handle, $filtered); # $filtered is an array of strings (array|string[])
                    fclose($handle);
                }
            }
        }else{
            
            $handle = fopen($local_csv_file_name_live, "a");
            fputcsv($handle, $response); # $response is an array of strings (array|string[])
            fclose($handle);
        }
    }


// ********************** END - Get the API response and store data in CSV file **********************

?>