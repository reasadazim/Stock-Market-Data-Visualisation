<?php

// Following code gets all ticker lists from database to show in fronend ticker select option

include('../dbconnect/credentials.php'); //database connector

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT * FROM te");
    $stmt->execute();
  
    // set the resulting array to associative
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    foreach((new RecursiveArrayIterator($stmt->fetchAll())) as $k => $v) {
        if(!is_null($v['ref'])){ // if ticker code is not null
            $tickers[] = array(
                "ticker" => $v['ticker'],
                "ticker_code" => $v['ref'] 
            );
        }
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Output
header('Content-Type: application/json; charset=utf-8');

echo(json_encode($tickers));


?>