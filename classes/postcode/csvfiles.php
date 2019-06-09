<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of csvfiles
 *
 * @author Chris Vaughan
 */
class PostcodeCsvfiles {

    private $path;

    public function __construct($path) {
        $this->path = $path;
    }

    function GetFiles() {
        // echo "Handle: " . $d->handle . PHP_EOL;
        //   echo "Path: " . $d->path . PHP_EOL;
        $d = dir($this->path);
        // check directory exists
        // if directory exists search for csv file
        $files = array();
        while (false !== ($entry = $d->read())) {
            // echo $entry . "\n";
            if ($entry != "." and $entry != "..") {
                $pieces = explode(".", $entry);
                $last = count($pieces) - 1;
                $ext = strtolower($pieces[$last]);
                if ($ext == "csv") {
                    $files[] = $entry;
                }
            }
        }
        sort($files);
        // return array of csv files
        return $files;
    }

}
