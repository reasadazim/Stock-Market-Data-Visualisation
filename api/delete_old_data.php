<?php

include('../dbconnect/credentials.php'); //database connector

$tables = ['d1d', 'd1m', 'd1w', 'd3m', 'd6m', 'd12m'];

foreach ($tables as $table){

    // Delete old data more than 1000 days
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("DELETE FROM `$table` WHERE `$table` < NOW() - interval 1000 DAY");
        $stmt->execute();
        
        // set the resulting array to associative
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        foreach((new RecursiveArrayIterator($stmt->fetchAll())) as $k => $v) {
            if($v['feed'] == 1){//if getting data from eodhistorical is set to active only then read CSV and upload to database
            readcsvfile($v['teid'],$v['ref']);
            }
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

}

?>