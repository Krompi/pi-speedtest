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
$classData = new getData(25);

$kategorie = substr(basename($_SERVER["REQUEST_URI"]), 0, strpos(basename($_SERVER["REQUEST_URI"]), "."));
if ( $kategorie == "day" && strtotime($_GET["date"]) ) {
    $start = date("Y-m-d", strtotime($_GET["date"]))." 00:00:00";
    $ende  = date("Y-m-d", strtotime($_GET["date"]))." 23:59:59";
    include('day.html');
} else {
    $table = $classData->getDays();
    include('index.html');
}
