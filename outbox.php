<?php
require __DIR__ . '/vendor/autoload.php';

use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;

$httpClient = new CurlHTTPClient('Ue6w7JASEziMETYphcd3GV9N0DpSxBrmsrcj5PqPPYbNYweIqlRkKP1Me0GtlyJyCN30pDwNJpWbd/5IiqhQZNwT7J70mZVpZgGsjkQq4spSG9bQiWqmqI5M9tPHVTYxUIk9tXYPi46x71ruJ5XDcwdB04t89/1O/w1cDnyilFU=');

$bot = new LINEBot($httpClient, ['channelSecret' => '29f0dad6d37990c7305ed6a9c8e9c7b1']);

$host        = "host=ec2-54-235-114-242.compute-1.amazonaws.com";
$port        = "port=5432";
$dbname      = "dbname = d2co76h1n5n2a9";
$credentials = "user = fkymozizzteorb password=0fa6487dd0be42b6782661b3eb4450d30b9af32f86fac2d8bcd7f87f56854c5d";

$db = pg_connect( "$host $port $dbname $credentials"  );
if(!$db) {
    echo "Error : Unable to open database\n";
}

while (true) {

    $sql ="SELECT * from tb_outbox WHERE flag = '1';";

    $ret = pg_query($db, $sql);
    if(!$ret) {
        echo pg_last_error($db);
        exit;
    } 
    while($row = pg_fetch_row($ret)) {

        $textMessageBuilder = new TextMessageBuilder($row[2]);
        $response = $bot->pushMessage($row[1], $textMessageBuilder);

        $sql = "UPDATE tb_outbox SET flag = '2' WHERE outbox_id = '$row[0]'";

        $ret = pg_query($db, $sql);
        if(!$ret) {
            echo pg_last_error($db);
        }

        echo $response->getHTTPStatus() . ' ' . $response->getRawBody();

    }


    sleep(5);
}
