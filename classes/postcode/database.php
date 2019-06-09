<?php

/**
 * Description of database
 *
 * @author Chris Vaughan
 */
class PostcodeDatabase extends Database {

    const VALIDPERIOD = 5 * 365; // 5 years

    private $tables = ["postcodes"];
    private $sql = ["CREATE TABLE `postcodes` (
  `postcode` varchar(8) NOT NULL,
  `quality` tinyint(4) NOT NULL,
  `easting` mediumint(9) NOT NULL,
  `northing` mediumint(9) NOT NULL,
  `updated` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8; ",
        "ALTER TABLE `postcodes`
  ADD UNIQUE KEY `postcode` (`postcode`);"];

    public function __construct($dbconfig) {
        parent::__construct($dbconfig);
    }

    public function addPostcode($postcode, $names, $values) {

        // delete postcode if already there
        $query = "DELETE FROM postcodes WHERE postcode = '[postcode]'";
        $query = str_replace("[postcode]", $postcode, $query);
        $ok = parent::runQuery($query);

        // insert new record
        $ok = parent::insertRecord("postcodes", $names, $values);
        if (!$ok) {
            return false;
        }
        return true;
    }

    public function getPostcodes($east1, $east2, $north1, $north2) {
        $where = " WHERE easting>=" . $east1 . " AND easting<=" . $east2 . " AND northing>=" . $north1 . " AND northing<=" . $north2;
        $ok = parent::runQuery("SELECT * FROM postcodes " . $where);
        if ($ok === false) {
            Logfile::writeError($this->db->ErrorMsg());
        }
        return $ok;
    }

    public static function AddDistanceToResults($x, $y, $results) {
        foreach ($results as $key => $value) {
            $dist = self::distance($x, $y, $value['easting'], $value['northing']);
            $results[$key]['distance'] = $dist;
        }
        return $results;
    }

    private static function distance($x, $y, $x2, $y2) {
        $val = ($x - $x2) * ($x - $x2) + ($y - $y2) * ($y - $y2);
        $value = sqrt($val);
        return $value;
    }

    public static function SortOnDistance($array) {

        function build_sorter($key) {
            return function ($a, $b) use ($key) {
                return ($a[$key] > $b[$key]);
            };
        }

        usort($array, build_sorter('distance'));
        return $array;
    }

    public function removeOldPostcodes() {
        $today = new DateTime(NULL);
        $date = $today;
        $date->sub(new DateInterval('P365D'));
        $formatdate = $date->format('Y-m-d');
        $query = "DELETE FROM `postcodes` WHERE `updated` <= '[Date]'";
        $query = str_replace("[Date]", $formatdate, $query);
        $ok = parent::runQuery($query);
        if (!$ok) {
            Logfile::writeError(parent::error());
        }
    }

    public function connect() {
        parent::connect();
        parent::createTables($this->sql);
    }

    public function closeConnection() {
        parent::closeConnection();
    }

}
