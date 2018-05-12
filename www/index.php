<?php
/**
 * Created by PhpStorm.
 * User: kromp
 * Date: 10.05.2018
 * Time: 14:56
 */


// PDO-Verbindung
$dbh = new PDO('mysql:host=localhost;dbname=speedtest', "speedtest", "speedy");

if ( strstr($_GET["url"], "csv") ) {
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Description: File Transfer');
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$fileName}");
    header("Expires: 0");
    header("Pragma: public");

    $fh = @fopen( 'php://output', 'w' );
    $time = date("U");
    for ($i = 1; $i <= 10; $i++) {
        $time = $time - 600;
        $tmp  = 1000*$time;
////        echo $tmp, PHP_EOL;
//        $array[] = [(string)$tmp, rand(0, 2500)/100];
        fputcsv($fh, [(string)$tmp, rand(0, 2500)/100]);
    }

    exit;
}

if ( strstr($_GET["url"], "jsonp") ) {
    $array = [];

    $sql = "SELECT * FROM results ORDER BY time DESC LIMIT 10";
//    echo $sql;
    foreach ( $dbh->query( $sql ) as $row ) {
        $dt = new DateTime($row["time"], new DateTimeZone('Europe/Berlin'));
        $time = $dt->format('U')*1000;
//        $timestamp = strtotime( $row["time"]);
//        $time = "Date.UTC(".date("Y", $timestamp).", ".date("m", $timestamp).", ".date("d", $timestamp).", ".date("H", $timestamp).", ".date("i", $timestamp).")";
//        echo $dt->format('Y-m-d H:i:s');
//        echo "<pre>";
//        echo print_r($row,true);
//        echo "</pre>";
        $array[] = [$time, (double)number_format($row["down"],3)];
    }
//    exit;

//    $time = date("U");
//    for ($i = 1; $i <= 10; $i++) {
//        $time = $time - 600;
//        $tmp  = 1000*$time;
////        echo $tmp, PHP_EOL;
//        $array[] = [$tmp, rand(0, 2500)/100];
//    }

//    $array = array(7,4,2);

    header("content-type: application/json");
//    echo print_r($array,true)   ;
    echo str_replace("\"", "", json_encode($array));
    exit;
}

//echo "<pre>";
//echo print_r($_GET,true), PHP_EOL;
//echo "</pre>";


$recentValue = "";
$sql = "SELECT * FROM results ORDER BY time DESC LIMIT 1";
//    echo $sql;
foreach ( $dbh->query( $sql ) as $row ) {
//        echo "<pre>";
//        echo print_r($row,true);
//        echo "</pre>";
    $recentValue = number_format($row["down"], 2);
}

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