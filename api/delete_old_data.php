<?php

// Following code runs a query to match this requirement - 
// "Only 1000 days of daily for any teid will be held from the current date, make sure the oldest date is removed from the table."

include('../dbconnect/credentials.php'); //database connector

// $tables = ['d1d', 'd1m', 'd1w', 'd3m', 'd6m', 'd12m'];
$tables = ['d1d']; // Except daily data table, no other table has more than 1000 data points

foreach ($tables as $table){

    // Delete old data more than 1000 days
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("DELETE FROM `$table` WHERE `$table` < NOW() - interval 1000 DAY"); // Delete old data more than 1000 days
        $stmt->execute();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

}

// Output
header('Content-Type: application/json; charset=utf-8');

echo("Success!");

?>