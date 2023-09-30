<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of postcode
 *
 * @author Chris Vaughan
 */
class PostcodePostcode {

    //put your code here
    public $Postcode;
    public $Quality;
    public $Distance;
    public $Easting;
    public $Northing;
    public $Square;
    public $Gridref;

    public function __construct($pc, $quality, $dist, $east, $north) {
        $pcode = $pc;
//        if (strlen($pcode) == 7) {
//            $pcode = substr($pc, 0, 4) . " " . substr($pc, 4);
//        }
        $this->Postcode = $pcode;
        $this->Quality = $quality;
        $this->Distance = $dist;
        $this->Easting = $east;
        $this->Northing = $north;
        $loc = Gridsquares::getSquare($east, $north);
        $this->Square = $loc[0];
        $this->Gridref = $loc[1];
    }

}
