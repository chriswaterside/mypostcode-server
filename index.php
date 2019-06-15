<?php

// options
// index.php?easting=394120&northing=806540&dist=1&maxpoints=1000
//  where 
//      easting and northing is the location
//      dist is distance from location in Km
//      maxpoints is the maximum number for points to return
// index.php?postcode=de221jt
//  where 
//      postcode is the postcode!

// task.php scheduled task to update postcodes from OS datasets


error_reporting(-1);
ini_set('display_errors', 'On');
if (file_exists("config.php")) {
    require_once 'config.php';
} else {
    require_once 'configtest.php';
}

require_once 'classes/autoload.php';
spl_autoload_register('autoload');
$config = new Config();
$db = new PostcodeDatabase($config->database);
$db->connect();
if (!$db->connected()) {
    PostcodesEmail::send("Postcodes Task: Unable to connect to database", $db->error());
}
$opts = new Options();
$postcode = $opts->gets("postcode");
$easting = $opts->gets("easting");
$northing = $opts->gets("northing");
$distance = $opts->gets("dist");
$maxpoints = $opts->gets("maxpoints");
if ($postcode !== null) {
    $pcs = new PostcodePostcodes($db);
    $postcodes = $pcs->getPostcode($postcode);
    header("Access-Control-Allow-Origin: *");
    header("Content-type: application/json");
    echo json_encode($postcodes);
} else {
    $exit = false;
    if ($easting === null) {
        $exit = true;
    }
    if ($northing === null) {
        $exit = true;
    }
    if ($distance === null) {
        $distance = 1;
    }
    if ($maxpoints === null) {
        $maxpoints = 1;
    }
    if ($exit) {
        $postcodes = [];
        echo json_encode($postcodes);
        exit;
    }

    $pcs = new PostcodePostcodes($db);
    $postcodes = $pcs->getCodes($easting, $northing, $distance * 1000, $maxpoints);
    header("Access-Control-Allow-Origin: *");
    header("Content-type: application/json");
    echo json_encode($postcodes);
}
$db->closeConnection();
