<?php

// Folowing code call eodhistoricaldata.com Intraday Historical Data API
// Store the response file in CSV format in server  


// Setting timezone to UTC, Otherwise eodhistoricaldata.com does not provide data
    date_default_timezone_set('UTC');


    // Get query parameters
    $crypto = $_GET["crypto"];


    if(($crypto == 'US2Y.INDX')||($crypto == 'BCOMCO.INDX')||($crypto == 'BCOMGC.INDX')){
        // For EOD data get last -1140 days data 
            $start_date = date('Y-m-d',strtotime("-365 days")); //get utc date
            $end_date = date('Y-m-d');
    }else{
        // For intra day data get last -600 days data 
            $start_date = date('Y-m-d',strtotime("-30 days")); //get utc date
            $start_date = $start_date . " 00:00:00"; //set time to 12 AM
            $end_date = date('Y-m-d H:i:s');
    }


// ********************** Get the API response and store data in CSV file **********************

    if(($crypto == 'US2Y.INDX')||($crypto == 'BCOMCO.INDX')||$crypto == 'BCOMGC.INDX'){
        // For EOD ticker we need date as normal date format e.g. 2023-02-28
        $from = $start_date;
        $to = $end_date;
        
        // Setting API URL
        $remote_file_name = "https://eodhistoricaldata.com/api/eod/".$crypto."?&from=".$from."&to=".$to."&period=d&api_token=63e9be52e52de8.36159257";
        echo $remote_file_name."<br>";
    }else{
        // For Intraday ticker we need date as timestamp e.g. 1564752900 (2019-08-02 13:35:00)
        $start_date_time_local = new DateTime($start_date);
        $from = $start_date_time_local->getTimestamp();
    
        $end_date_time_local = new DateTime($end_date);
        $to = $end_date_time_local->getTimestamp();
        
        // Setting API URL
        $remote_file_name = "https://eodhistoricaldata.com/api/intraday/".$crypto."?api_token=63e9be52e52de8.36159257&from=".$from."&to=".$to."";
        // echo $remote_file_name."<br>";
    }

    print_r($remote_file_name);

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