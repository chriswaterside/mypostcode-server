<?php

// options
// index.php?gr=Sk123456&dist=nn&maxpoints=mm
//  where 
//      gr is the grid reference
//      nn is distance from gr in Km
//      mm is the maximum number for points to return
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
$easting = $opts->gets("easting");
$northing = $opts->gets("northing");
$distance = $opts->gets("dist");

$maxpoints = $opts->gets("maxpoints");

$pcs = new PostcodePostcodes($db);
$postcodes = $pcs->getCodes($easting, $northing, $distance*1000, $maxpoints);
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
echo json_encode($postcodes);

$db->closeConnection();
