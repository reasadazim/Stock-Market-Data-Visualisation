<?php
  
// Folloing code reads all the file from a date till today and outputs JSON data for candlestick plot
date_default_timezone_set('UTC');
   
$start_date = $_GET["startDate"];
$end_date = $_GET["endDate"];
$crypto = $_GET["crypto"];
 
//  Add 1 day to end date in order to get 1 day interval  (P1D)
$next_date = new DateTime($end_date);
$next_date->add(new DateInterval("P1D"));
$end_date =  $next_date->format('Y-m-d');

// $today = date("Y-m-d"); //current date

if((is_null($start_date))&&(is_null($start_date))){
    // if start date and end data is empty do nothing
}else{

    // Create a date interval array of 1 day from start date to end date
    $period = new DatePeriod(
        new DateTime($start_date),
        new DateInterval('P1D'),
        new DateTime($end_date )
    );

}






// php function to convert csv to json format

function csvToJson($fname,$crypto) {
    // open csv file
    if (!($fp = fopen($fname, 'r'))) {
        die("Can't open file...");
    }
    
    if($crypto == 'EURUSD'){
        //read csv headers
        $key = array(
            'Time', 
            'Open', 
            'High', 
            'Low', 
            'Close', 
            'Open', 
            'High', 
            'Low', 
            'Close', 
        );
    }else{
        //read csv headers
        $key = array(
            'Time', 
            'Open', 
            'High', 
            'Low', 
            'Close', 
        );
    }

    
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

  foreach ($period as $key => $value) {

    $local_csv_file_name = "../data/realtime/1m/".$crypto."/".$crypto."-".$value->format('Y-m-d')."-1m.csv";
    // echo $local_csv_file_name;

    if(file_exists($local_csv_file_name)){

        $data = csvToJson($local_csv_file_name, $crypto);

        if(is_null($data)){}else{
            // Filter data -> converting miliseconds to year
            foreach($data as $datum){
                $mil = strtotime($datum['Time']);
                // $seconds = $mil / 1000;
                if( (float)$datum['Open'] != 0 ){
                    $filtered_data[] = array(
                        // 'time' => date("Y-m-d", $seconds),
                        'time' => $mil,
                        'open' => (float)$datum['Open'],
                        'high' => (float)$datum['High'],
                        'low' => (float)$datum['Low'],
                        'close' => (float)$datum['Close'],
                    );
                }
            }
        }
        // END - Filter data -> converting miliseconds to year

    }

}


// ************* END - Get data from file and output as json *************






// Output

header('Content-Type: application/json; charset=utf-8');

echo(json_encode($filtered_data));


?>
