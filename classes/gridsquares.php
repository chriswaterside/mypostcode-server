<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GridSquares
 *
 * @author Chris Vaughan
 */
class Gridsquares {

    private static $squares = null;

    public static function setupSquares() {
        if (self::$squares == null) {
            self::$squares = [];
            self::$squares[] = ["SV", "SW", "SX", "SY", "SZ", "TV", ""];
            self::$squares[] = ["", "SR", "SS", "ST", "SU", "TQ", "TR"];
            self::$squares[] = ["", "SM", "SN", "SO", "SP", "TL", "TM"];
            self::$squares[] = ["", "", "SH", "SJ", "SK", "TF", "TG"];
            self::$squares[] = ["", "", "SC", "SD", "SE", "TA", ""];
            self::$squares[] = ["", "NW", "NX", "NY", "NZ", "", ""];
            self::$squares[] = ["", "NR", "NS", "NT", "NU", "", ""];
            self::$squares[] = ["NL", "NM", "NN", "NO", "", "", ""];
            self::$squares[] = ["NF", "NG", "NH", "NJ", "NK", "", ""];
            self::$squares[] = ["NA", "NB", "NC", "ND", "", "", ""];
            self::$squares[] = ["", "HW", "HX", "HY", "HZ", "", ""];
            self::$squares[] = ["", "", "", "HT", "HU", "", ""];
            self::$squares[] = ["", "", "", "", "HP", "", ""];
        }
    }

    public static function getSquare($easting, $northing) {
        self::setupSquares();
        $square = "";
        $gridref = "";
        $east = intdiv($easting, 100000);
        $north = intdiv($northing, 100000);
        if (array_key_exists($north, self::$squares)) {
            $eastings = self::$squares[$north];
            if (array_key_exists($east, $eastings)) {
                $square = $eastings[$east];
                $gridref = $square . sprintf("%'.05d", $easting-$east*100000) . sprintf("%'.05d", $northing-$north*100000);
            }
        }
        $result=[$square,$gridref];
        return $result;
    }

}
