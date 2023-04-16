<?php

// This file uploads CSV file upon form submission.

// Getting form data
$ref = substr($_FILES['file']['name'], 0, -9);
$duration = $_POST['duration'];


if (isset($_POST['submit'])){

    include('../dbconnect/credentials.php'); //database connector

    // Get the list of tickers (feed=1) and read files then insert into MySQL database
    try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT * FROM te WHERE `ref` = '$ref'");
    $stmt->execute();

    // set the resulting array to associative
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    foreach((new RecursiveArrayIterator($stmt->fetchAll())) as $k => $v) {
        $ticker_id = $v['teid'];
    }
    } catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    }


    $fileMimes = array(
        'text/x-comma-separated-values',
        'text/comma-separated-values',
        'application/octet-stream',
        'application/vnd.ms-excel',
        'application/x-csv',
        'text/x-csv',
        'text/csv',
        'application/csv',
        'application/excel',
        'application/vnd.msexcel',
        'text/plain'
    );
 

    // Validate selected file is a CSV file or not
    if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $fileMimes)){
 
        // Open uploaded CSV file with read-only mode
        $csvFile = fopen($_FILES['file']['tmp_name'], 'r');

        // Skip the first line
        fgetcsv($csvFile);


        // insert data into MySQL database, update existing data on re-import data
        try {
          $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
          // set the PDO error mode to exception
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Parse data from CSV file line by line        
            while (($getData = fgetcsv($csvFile, 10000, ",")) !== FALSE){

                if(is_null($getData[6])){
                    // If volume is empty
                    $volume = 0;
                }else{
                    $volume = $getData[6];
                }
                
                $uid = md5($getData[0]."-".$ticker_id); // Unique key: md5(date+ticker_id) (2021-06-09-1 = 0001cf489c77d159b76cbb5599838ce5)
                $date = $getData[0];
                $o = (float)$getData[1];
                $h = (float)$getData[2];
                $l = (float)$getData[3];
                $c = (float)$getData[4];
                
                // Uncomment if upload P1, P2, P3, P4 values
                // $p1 = (float)$getData[7];
                // $p2 = (float)$getData[8];
                // $p3 = (float)$getData[9];
                // $p4 = (float)$getData[10];

                $tableName = 'd' . $duration;
                
                // Comment following query if upload P1, P2, P3, P4 values
                $sql = "INSERT INTO `$tableName` (`uid`, `$tableName`, `teid`, `o`, `h`, `l`, `c`, `v`)
                VALUES ('$uid', '$date', $ticker_id, $o, $h, $l, $c, $volume)
                ON DUPLICATE KEY UPDATE `uid` = '$uid', `$tableName` = '$date', `teid` = $ticker_id, `o` = $o, `h` = $h, `l` = $l, `c` = $c, `v` = $volume";
                
                // Uncomment if upload P1, P2, P3, P4 values
                // $sql = "INSERT INTO `$tableName` (`uid`, `$tableName`, `teid`, `o`, `h`, `l`, `c`, `v`, `p1`, `p2`, `p3`, `p4`)
                // VALUES ('$uid', '$date', $ticker_id, $o, $h, $l, $c, $volume)
                // ON DUPLICATE KEY UPDATE `uid` = '$uid', `$tableName` = '$date', `teid` = $ticker_id, `o` = $o, `h` = $h, `l` = $l, `c` = $c, `v` = $volume, `p1` = $p1, `p2` = $p2, `p3` = $p3, `p4` = $p4";

                
                // use exec() because no results are returned
                $conn->exec($sql);
            }

            // Close opened CSV file
            fclose($csvFile);

        } catch(PDOException $e) {
            echo $sql . "<br>" . $e->getMessage();
        }

        header('Location: ' . $_SERVER['HTTP_REFERER'] . '#success'); //redirect back

    }else{
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '#error'); //redirect back
    }
}

?>