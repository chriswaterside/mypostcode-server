<?php

const POSTCODEFOLDER = 'postcodefiles';

error_reporting(E_ALL);
ini_set('display_errors', 'On');
if (version_compare(PHP_VERSION, '8.0.0') < 0) {
    echo 'You MUST be running on PHP version 7.0.0 or higher, running version: ' . \PHP_VERSION . "\n";
    die();
}
define("NOTIFYEMAILADDRESS", "feeds@ramblers-webs.org.uk");
// set current directory to current run directory
$exepath = dirname(__FILE__);
define('BASE_PATH', dirname(realpath(dirname(__FILE__))));
chdir($exepath);
if (file_exists("config.php")) {
    require_once 'config.php';
} else {
    require_once 'configtest.php';
}
require_once 'classes/autoload.php';
spl_autoload_register('autoload');
require 'classes/phpmailer/src/PHPMailer.php';
require 'classes/phpmailer/src/SMTP.php';
require 'classes/phpmailer/src/Exception.php';
date_default_timezone_set('Europe/London');
Logfile::create("logfiles/logfile");
Timeout::setTime();

$config = new Config();

// get a csv file
$csvs = new PostcodeCsvfiles(POSTCODEFOLDER);
$files = $csvs->GetFiles();

if (count($files) > 0) {
    $db = new PostcodeDatabase($config->database);
    $db->connect();
    if (!$db->connected()) {
        PostcodesEmail::send("Postcodes Task: Unable to connect to database", $db->error());
    } else {
        $file = POSTCODEFOLDER . '/' . $files[0];
        Logfile::writeWhen("Processing file " . $file);
        echo "Processing file " . $file . "<br/>";
        // process csv file
        $csv = new PostcodeCsvfile($file);
        $csv->ProcessCSVFile($db);
        // rename csv file
        PostcodeCsvfile::rename($file);
        if (count($files) == 1) {
            // last file processed, remove old records
            echo "Removing old postcodes";
            $db->removeOldPostcodes();
        }
        Functions::eMail("Postcodes within file " . $file . " updated.");
        $db->closeConnection();
    }
}
Logfile::writeWhen("End of processing");
