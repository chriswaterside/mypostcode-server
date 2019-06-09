<?php

/**
 * Description of logfile
 *
 * @author Chris Vaughan
 */
class Logfile {

    private static $logfile;
    private static $noerrors = 0;
    private static $errors;
    private static $name;

    public static function create($name) {
        self::$name = $name;
        $subname = date("YmdHis");
        self::$logfile = fopen($name . $subname . ".log", "w") or die("Unable to open logfile file!");
        Logfile::writeWhen("Logfile " . $subname . ".log created");
        self::deleteOldFiles();
        self::$errors = [];
    }

    public static function deleteOldFiles() {
        $today = date("Y-m-d");
        $date = new DateTime($today);
        $date->sub(new DateInterval('P7D'));
        $datestring = $date->format('Y-m-d');
        foreach (glob(self::$name . "*.log") as $filename) {
            //echo "$filename size " . filesize($filename) . "\n";
            $modified = date("Y-m-d", filemtime($filename));
            if ($modified < $datestring) {
                unlink($filename);
                logfile::writeWhen("Old logfile deleted: " . $filename);
            }
        }
    }

    public static function write($text) {
        if (isset(self::$logfile)) {
            fwrite(self::$logfile, $text . "\n");
        }
    }

    public static function writeWhen($text) {
        $today = new DateTime(NULL);
        $when = $today->format('Y-m-d H:i:s');
        self::write($when . " " . $text);
    }

    public static function writeError($text) {
        self::$noerrors+=1;
        self::writeWhen(" ERROR: " . $text);
        self::addError($text);
    }

    private static function addError($text) {
        if (self::$noerrors <= 10) {
            self::$errors[] = $text;
        }
    }

    public static function getNoErrors() {
        return self::$noerrors;
    }

    public static function getErrors() {
        return self::$errors;
    }

    public static function resetNoErrrors() {
        self::$noerrors = 0;
    }

    public static function close() {
        if (isset(self::$logfile)) {
            fclose(self::$logfile);
            self::$logfile = NULL;
        }
    }

}
