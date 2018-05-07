<?php
/**
 * Created by PhpStorm.
 * User: kromp
 * Date: 07.05.2018
 * Time: 16:48
 */
// PDO-Verbindung
$dbh = new PDO('mysql:host=localhost;dbname=speedtest', "speedtest", "speedy");

// Variablen setzen
$results = [];
$parameter    = explode("-", substr(basename($_SERVER["REQUEST_URI"]), 0, strpos(basename($_SERVER["REQUEST_URI"]), ".")) );
$parPrecision = $parameter[1];
$parPeriod    = $parameter[2];

if ( $parPrecision == "tag" ) {
    $sql  = "SELECT YEAR(time) AS year, MONTH(time) AS month, DAY(time) AS day, AVG(down) AS down_avg, MIN(down) AS down_min, MAX(down) AS down_max 
               FROM results 
           GROUP BY  YEAR(time), MONTH(time), DAY(time)";
} elseif ( $parPrecision == "stunde" ) {
    $sql  = "SELECT YEAR(time) AS year, MONTH(time) AS month, DAY(time) AS day, HOUR(time) AS hour, AVG(down) AS down_avg, MIN(down) AS down_min, MAX(down) AS down_max 
               FROM results 
           GROUP BY  YEAR(time), MONTH(time), DAY(time), HOUR(time)
           ORDER BY year, month, day, hour";
}


foreach ( $dbh->query( $sql ) as $row ) {
    // Variablen setzen
    $buffer = [];
    $xaxis  = "Date.UTC(".$row["year"].", ".$row["month"].", ".$row["day"].", ".$row["hour"].")";

    // Durchschnitt
    $buffer["average"] = [
        $xaxis,
        number_format($row["down_avg"], 2)
    ];
    $results["averages"][] = "[".implode(",", $buffer["average"])."]";
    // Bereich
    $buffer["ranges"] = [
        $xaxis,
        number_format($row["down_min"], 2),
        number_format($row["down_max"], 2)
    ];
    $results["ranges"][] = "[".implode(",", $buffer["ranges"])."]";
}

// Graph-Variablen setzen
$ranges   = implode(",\n", $results["ranges"]);
$averages = implode(",\n", $results["averages"]);