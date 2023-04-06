<?php

// Following code reads CSV file from data directory and store it into MySQL Database

  include('../dbconnect/credentials.php'); //database connector

  // Get the list of tickers (feed=1) and read files then insert into MySQL database
  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT * FROM te");
    $stmt->execute();
  
    // set the resulting array to associative
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    foreach((new RecursiveArrayIterator($stmt->fetchAll())) as $k => $v) {
      if($v['feed'] == 1){//if getting data from eodhistorical is set to active only then read CSV and upload to database
        read_csv_file_and_upload_to_database($v['teid'],$v['ref']);
      }
    }
  } catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
  }


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


  function read_csv_file_and_upload_to_database($ticker_id, $tickercode){
    // Setting timezone to UTC, Otherwise eodhistoricaldata.com does not provide data
    date_default_timezone_set('UTC');

    $crypto = $tickercode;

    $durations = ["1d", "1m", "1w", "3m", "6m", "12m"];
    
    foreach ($durations as $duration){

    // ************* Get data from CSV file and upload to MySQL database *************

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


                $uid = md5($datum['Date']."-".$ticker_id); // Unique key: md5(date+ticker_id) (2021-06-09-1 = 0001cf489c77d159b76cbb5599838ce5)
                $date = $datum['Date'];
                $o = (float)$datum['Open'];
                $h = (float)$datum['High'];
                $l = (float)$datum['Low'];
                $c = (float)$datum['Close'];

                $tableName = 'd' . $duration;
                
                include('../dbconnect/credentials.php');  //database connector
                // insert data into MySQL database, update existing data on re-import data
                try {
                  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                  // set the PDO error mode to exception
                  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                  $sql = "INSERT INTO `$tableName` (`uid`, `$tableName`, `teid`, `o`, `h`, `l`, `c`, `v`)
                  VALUES ('$uid', '$date', $ticker_id, $o, $h, $l, $c, $volume)
                  ON DUPLICATE KEY UPDATE `uid` = '$uid', `$tableName` = '$date', `teid` = $ticker_id, `o` = $o, `h` = $h, `l` = $l, `c` = $c, `v` = $volume";
                  // use exec() because no results are returned
                  $conn->exec($sql);
                } catch(PDOException $e) {
                  echo $sql . "<br>" . $e->getMessage();
                }

            }
        
        }

    }
    
    // ************* END - Get data from CSV file and upload to MySQL database *************

    }

  }

// Output
header('Content-Type: application/json; charset=utf-8');

echo("Success!");

?>