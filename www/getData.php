<?php
/**
 * Created by PhpStorm.
 * User: kromp
 * Date: 12.05.2018
 * Time: 21:40
 */

class getData
{
    function __construct()
    {
    }

    public function getRecent($value = "down") {
        $sql = "SELECT * FROM results ORDER BY time DESC LIMIT 1";
        $result = $this->fetchData($sql);
        if ( $value == "down" ) {
            return number_format(current($result)["down"], 2, ",", "");
        }
    }

    public function getAverage($value = "down") {
        $sql = "SELECT AVG(down) AS down, time FROM results ORDER BY time DESC LIMIT 1";
        $result = $this->fetchData($sql);
        if ( $value == "down" ) {
            return number_format(current($result)["down"], 2, ",", "");
        }
    }

    public function getLastValues($count = 10) {
        $sql = "SELECT * FROM results ORDER BY time DESC LIMIT ".(int)$count;
        return $this->fetchData($sql, "ms");
//
    }

    public function getLastValuesJson($count = 10) {
        return $this->convertJson($this->getLastValues($count, "ms"));
    }

    public function fetchData($sql, $type = "db") {
        global $dbh;
        $result = [];
        foreach ( $dbh->query( $sql ) as $row ) {
            if ( $type == "db" ) {
                $time = $row["time"];
            } elseif ( $type == "ms" ) {
                $time = date("U", strtotime($row["time"]))*1000;
            } else {
                continue;
            }
            $result[] = [
                "time" => $time,
                "down" => (double)$row["down"],
            ];
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