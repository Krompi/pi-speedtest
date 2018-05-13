<?php
/**
 * Created by PhpStorm.
 * User: kromp
 * Date: 12.05.2018
 * Time: 21:40
 */

class getData
{
    function __construct($maxDSL)
    {
        $this->maxDSL = $maxDSL;
        $this->colors = [
            "rgba( 62, 255,   0, .5 )",
            "rgba(232, 227,  11, .6 )",
            "rgba(255, 189,   1, .6 )",
            "rgba(232, 113,  11, .7 )",
            "rgba(255,  40,  12, .8 )"
        ];
    }

    public function getRecent() {
        $sql = "SELECT * FROM results ORDER BY time DESC LIMIT 1";
        $result = $this->fetchData($sql);
        return number_format((double)current($result)["down"], 2, ",", "");
    }

    public function getAverage() {
        $sql = "SELECT AVG(down) AS down, time FROM results ORDER BY time DESC LIMIT 1";
        $result = $this->fetchData($sql, ["down"]);
        return number_format((double)current($result)["down"], 2, ",", "");
    }

    public function getDays() {
        $sql = "SELECT time, down 
                  FROM results 
              GROUP BY YEAR(time), MONTH(time), DAY(time) 
              ORDER BY YEAR(time) DESC, MONTH(time) DESC, DAY(time) DESC";
        return $this->fetchData($sql, ["down", "date", "date_Ymd", "percentage", "color"]);
    }

    public function getLastValues($count = 10) {
        $sql = "SELECT * FROM results ORDER BY time DESC LIMIT ".(int)$count;
        return $this->fetchData($sql);
    }

    public function getValues($start, $ende) {
        $whereItems = [];
        if ( strtotime($start) ) $whereItems[] = "time>='".$start."'";
        if ( strtotime($ende) )  $whereItems[] = "time<='".$ende."'";
        if ( count($whereItems) > 0 ) {
            $where = "WHERE ".implode(" AND ", $whereItems);
        }

        $sql = "SELECT * FROM results 
                 ".$where."";
        return $this->fetchData($sql);
    }

    public function getValuesJson($start, $ende) {
        return $this->convertJson($this->getValues($start, $ende));
    }

    public function getLastValuesJson($count = 10) {
        return $this->convertJson($this->getLastValues($count, "ms"));
    }

    public function fetchData($sql, $fields = ["time_ms", "down"]) {
        global $dbh;
        $result = [];
        $i = 0;
        foreach ( $dbh->query( $sql ) as $row ) {
            foreach ( $fields as $field ) {
                if ( $field == "db" )         $result[$i][$field] = $row["time"];
                if ( $field == "time_ms" )    $result[$i][$field] = date("U", strtotime($row["time"]))*1000;
                if ( $field == "time_s" )     $result[$i][$field] = date("U", strtotime($row["time"]));
                if ( $field == "date" )       $result[$i][$field] = date("d.m.Y", strtotime($row["time"]));
                if ( $field == "date_Ymd" )   $result[$i][$field] = date("Y-m-d", strtotime($row["time"]));
                if ( $field == "down" )       $result[$i][$field] = (double) number_format((double) $row["down"], 2, ".", "");
                if ( $field == "percentage" ) $result[$i][$field] = (double) number_format((double)$row["down"]*100/$this->maxDSL, 2, ".", "");
                if ( $field == "color" ) {
                    if ( $row["down"]/$this->maxDSL >= 0.9 ) {
                        $result[$i][$field] = $this->colors[0];
                    } elseif ( $row["down"]/$this->maxDSL >= 0.8 ) {
                        $result[$i][$field] = $this->colors[1];
                    } elseif ( $row["down"]/$this->maxDSL >= 0.6 ) {
                        $result[$i][$field] = $this->colors[2];
                    } elseif ( $row["down"]/$this->maxDSL >= 0.4 ) {
                        $result[$i][$field] = $this->colors[3];
                    } else {
                        $result[$i][$field] = $this->colors[4];
                    }
                };
            }
            $i++;
        }
        return $result;
    }

    function convertJson($array) {
        $result = [];
        foreach ( $array as $item ) {
            $buffer = [];
            foreach  ($item as $value) {
                $buffer[] = $value;
            }
            $result[] = $buffer;
        }
        return json_encode($result);
    }
}