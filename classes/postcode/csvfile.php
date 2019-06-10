<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of csvfile
 *
 * @author Chris Vaughan
 */
class PostcodeCsvfile {

    private $path;

    public function __construct($path) {
        $this->path = $path;
    }

    public function ProcessCSVFile($db) {
        $handle = fopen($this->path, "r");
// check file exsists
        if ($handle == false) {
//error
        } else {
            $day = date("Y-m-d");
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
//  echo "<p> $num fields in line $row: <br /></p>". PHP_EOL;
                $row++;
                if ($num >= 4) {
                    $postcode = $data[0];
                    $quality = $data[1];
                    $easting = $data[2];
                    $northing = $data[3];
                    $values = array();
                    $values['postcode'] = $postcode;
                    $values['quality'] = $quality;
                    $values['east'] = $easting;
                    $values['north'] = $northing;
                    $values['updated'] = $day;
                    $names = array();
                    $names[] = "postcode";
                    $names[] = "quality";
                    $names[] = "easting";
                    $names[] = "northing";
                    $names[] = "updated";
                    $result = $db->addPostcode($postcode, $names, $values);
//  $result = $db->insertRecord('postcodes', $names, $values);
                    if ($result === false) {
                        Logfile::writeError("Unable to update/insert postcode " . $postcode);
                    }
                }
            }
            fclose($handle);

            return;
        }
    }

    public static function rename($file) {
        if (file_exists($file . ".done")) {
            unlink($file . ".done");
        }
        rename($file, $file . ".done");
        echo $file . " - file renamed<br/>";
    }

}
