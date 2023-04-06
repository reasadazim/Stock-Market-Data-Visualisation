<?php
   
// Folloing code reads data from MySQL database and outputs JSON data to make candlestick chart

// Setting timezone to UTC, Otherwise eodhistoricaldata.com does not provide data
date_default_timezone_set('UTC');

// Get query parameters
$crypto = $_GET["crypto"];
$duration = $_GET["duration"];


function readData($crypto, $duration) {
    
    $table_name = "d".$_GET["duration"]; //setting table name

    $column_name = "d".$_GET["duration"]; //setting column name

    include('../dbconnect/credentials.php');

    // Get ticker ID from 'te' table
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT * FROM te WHERE `ref` = '$crypto'");
        $stmt->execute();
      
        // set the resulting array to associative
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        foreach((new RecursiveArrayIterator($stmt->fetchAll())) as $k => $v) {
            $ticker_id = $v['teid'];
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }


    // Get candlestick data
     
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT * FROM `$table_name` WHERE `teid` = '$ticker_id' ORDER BY $column_name");
        $stmt->execute();
      
        // set the resulting array to associative
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

        foreach((new RecursiveArrayIterator($stmt->fetchAll())) as $k => $v) {
            if( (float)$v['o'] != 0 ){

                if(is_null($v['v'])){
                    // If volume is empty
                    $volume = 0;
                }else{
                    $volume = $v['v'];
                }
        
                // Determine volume bar color
                // if the closing price is greater than the open price then GREEN else RED
                if ( ((float)$v['c']) > ((float)$v['o']) ){
                    $color = "#36d97aa6";
                }else{
                    $color = "#e13255ab";
                }
        
                $filtered_data[] = array(
                    'time' => (int)(strtotime($v[$column_name])),
                    'open' => (float)$v['o'],
                    'high' => (float)$v['h'],
                    'low' => (float)$v['l'],
                    'close' => (float)$v['c'],
                    'volume' => $volume,
                    'color' => $color,
                );
            }
        }

        return $filtered_data;

      } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
      }
}



// Output
header('Content-Type: application/json; charset=utf-8');

echo(json_encode(readData($crypto, $duration)));

?>