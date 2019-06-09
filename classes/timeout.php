<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of timeout
 *
 * @author Admin
 */
class Timeout {

    static function setTime() {
        // Attempt to use an infinite time limit, in case you are using the PHP CGI binary instead
        // of the PHP CLI binary. This will not work with Safe Mode, though.
        $safe_mode = true;
        if (function_exists('ini_get')) {
            $safe_mode = ini_get('safe_mode');
        }
        if (!$safe_mode && function_exists('set_time_limit')) {
            Logfile::writeWhen("Time limit restrictions removed");
            @set_time_limit(0);
        } elseif (!$safe_mode) {
            Logfile::writeWhen("Could not remove time limit restrictions; you may get a timeout error\n");
        } else {
            Logfile::writeWhen("You are using PHP's Safe Mode; you may get a timeout error\n");
        }
    }

}
