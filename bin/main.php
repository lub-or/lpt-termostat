<?php

date_default_timezone_set('Europe/Prague');
define('TEMP_WATER_LOW', 45);
define('TEMP_WATER_HIGH', 52);
define('TEMP_PRUSER', 100);//-10
define('TEMP_TRUBKA', 6.5);

$mysqli = @new mysqli('localhost', '****', '****', 'termostat');
if ($mysqli->connect_error) {
    header('HTTP/1.1 500 Internal Server Error');
    exit;
} else {
    $mysqli->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
}

// load TEMPs
$fn = 'state.temp';
$f = fopen($fn, 'r');
$ftemp = fread($f, filesize($fn)-3);
fclose($f);
list($t1Str, $t2Str, $t3Str) = explode('|', $ftemp);
$t1 = (float)$t1Str;
$t2 = (float)$t2Str;
$t3 = (float)$t3Str;

// load last HEATING state = are we heating (rising) or cooling (falling)
$res = $mysqli->query('SELECT temp2, flag_heating FROM log ORDER BY id_log DESC LIMIT 1');
$ret = mysqli_fetch_array($res);
$flagHeating = (int)$ret['flag_heating'];
$oldTemp2 = (float)$ret['temp2'];

// false measurement
if (abs($t2 - $oldTemp2) > 5) exit;

// load settings
$res = $mysqli->query('
       SELECT temp_low, temp_high
         FROM settings
        WHERE '.date('N').' BETWEEN day_from AND day_until
          AND \''.date('H:i:s').'\' BETWEEN time_from AND time_until
        LIMIT 1');
if (mysqli_num_rows($res) == 0) return;
$ret = mysqli_fetch_array($res);
$tempLow = (float)$ret['temp_low'];
$tempHigh = (float)$ret['temp_high'];


// current rele state
$fn = 'state.rele';
$f = fopen($fn, 'r');
$ftemp = fread($f, filesize($fn));
fclose($f);
$state = (int)$ftemp;


// ====== MAIN DECISION ======

// not heating currently
if ($flagHeating == 0) {
    // START to heat
    if ($t2 <= $tempLow || $t1 <= TEMP_TRUBKA) {
	$state = 1;
	$flagHeating = 1;
	copy('/home/lubo/cron/teplomer/sms-topim', '/home/lubo/cron/sms/outgoing/sms-topim-'.date('Y-m-d-H-i-s'));
    }
} else

// heating currently
{
    // STOP to heat
    if ($t2 >= $tempHigh) {
	$state = 0;
	$flagHeating = 0;
	copy('/home/lubo/cron/teplomer/sms-netopim', '/home/lubo/cron/sms/outgoing/sms-netopim-'.date('Y-m-d-H-i-s'));
    }

    // STOP to heat temporarely
    else if ($t1 >= TEMP_WATER_HIGH) {
	$state = 0;
    }

    // START to heat temporarely
    else if ($t1 <= TEMP_WATER_LOW) {
	$state = 1;
    }
}

// poplach mrazak
if ($t3 > TEMP_PRUSER) {
    $state = 1;
    $flagHeating = 1;
    copy('/home/lubo/cron/teplomer/sms-poplach-1', '/home/lubo/cron/sms/outgoing/sms-poplach-1-'.date('Y-m-d-H-i-s'));
}

// set rele
$f = fopen('state.rele', 'w');
fwrite($f, $state);
fclose($f);

// write log
$mysqli->query("INSERT INTO log (temp1, temp2, temp3, flag_state, flag_heating) VALUES ('{$t1}', '{$t2}', '{$t3}', {$state}, {$flagHeating})");

$mysqli->close();
