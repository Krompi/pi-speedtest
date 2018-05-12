<?php
/**
 * Created by PhpStorm.
 * User: kromp
 * Date: 10.05.2018
 * Time: 14:56
 */


include('getData.php');


// PDO-Verbindung
$dbh = new PDO('mysql:host=localhost;dbname=speedtest', "speedtest", "speedy");
$classData = new getData();


if ( strstr($_GET["url"], "jsonp") ) {
    $array = [];
    $sql = "SELECT * FROM results ORDER BY time DESC LIMIT 10";
    foreach ( $dbh->query( $sql ) as $row ) {
        $dt = new DateTime($row["time"], new DateTimeZone('Europe/Berlin'));
        $time = $dt->format('U')*1000;
        $array[] = [$time, (double)number_format($row["down"],3)];
    }
    header("content-type: application/json");
    echo str_replace("\"", "", json_encode($array));
    exit;
}


//$recentValue = "";
//$sql = "SELECT * FROM results ORDER BY time DESC LIMIT 1";
//foreach ( $dbh->query( $sql ) as $row ) {
//    $recentValue = number_format($row["down"], 2);
//}

$stdSpeed = 25;

$sql = "SELECT YEAR(time) AS year, MONTH(time) AS month, DAY(time) AS day, time, down FROM results GROUP BY YEAR(time), MONTH(time), DAY(time) ORDER BY YEAR(time) DESC, MONTH(time) DESC, DAY(time) DESC";

$table = [];
foreach ( $dbh->query( $sql ) as $row ) {
    $percentage = $row["down"]/$stdSpeed;
    if ( $percentage >= 0.9 ) {
        $color = "rgba(62, 255, 0, .5 )";
    } elseif ( $percentage >= 0.8 ) {
        $color = "rgba(232, 227, 11, .6 )";
    } elseif ( $percentage >= 0.65 ) {
        $color = "rgba(255, 189, 1, .6 )";
    } elseif ( $percentage >= 0.5 ) {
        $color = "rgba(232, 113, 11, .7 )";
    } else {
        $color = "rgba(255, 40, 12, .8 )";
    }

    $table[] = [
        "color" => $color,
        "down"  => number_format($row["down"], 2, ",", ""),
        "date"  => date("d.m.Y", strtotime($row["time"]))
    ];
}




include('index.html');