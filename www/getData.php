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
            return current($result)["down"];
        }
    }

    public function fetchData($sql) {
        global $dbh;
        $result = [];
        foreach ( $dbh->query( $sql ) as $row ) {
            $result[] = [
                "time" => $row["time"],
                "down" => $row["down"],
            ];
        }
        return $result;
    }
}