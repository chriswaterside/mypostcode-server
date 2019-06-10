<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of postcodes
 *
 * @author Chris Vaughan
 */
class PostcodePostcodes {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getCodes($easting, $northing, $distance, $maxpoints) {

        if ($distance == false) {
            $distance = 10000; //10Km
        }
        $east1 = $easting - $distance;
        $east2 = $easting + $distance;
        $north1 = $northing - $distance;
        $north2 = $northing + $distance;
        $ok = $this->db->getPostcodes($east1, $east2, $north1, $north2);
        if ($ok == true) {
            $results = $this->db->getResult();
            $codes = array();
            foreach ($results as $value) {
                $east = $value['easting'];
                $north = $value['northing'];
                $dist = Functions::distance($east, $north, $easting, $northing);
                $code = new PostcodePostcode($value['postcode'], intval($value['quality']), $dist, intval($east), intval($north));
                $codes[] = $code;
            }

            $codes = Functions::sortOnDistance($codes);
            $codes = Functions::removeCodesOnDistance($codes, $distance);
            $noFound = count($codes);
            if ($noFound > $maxpoints) {
                $codes = array_slice($codes, 0, $maxpoints);
            }
            return $codes;
        }
    }

}
