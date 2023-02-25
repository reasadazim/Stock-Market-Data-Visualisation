<?php
include "./vendor/autoload.php";
 
$client = new WebSocket\Client("ws://ws.eodhistoricaldata.com/ws/crypto?api_token=63e9be52e52de8.36159257");

 
while (true) {
    try {
        $message = $client->receive();
        print_r($message);
        echo "\n";
 
      } catch (\WebSocket\ConnectionException $e) {
        // Possibly log errors
        $client->text('{"action": "subscribe", "symbols": "BTC-USD"}');
        print_r("Error: ".$e->getMessage());
        echo "\n";
    }
}
$client->close();
?>