<?php
$mysqli = @new mysqli('127.0.0.1', '*******', '*******', 'termostat');
$mysqli->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

$res = $mysqli->query("SELECT * FROM log ORDER BY id_log DESC LIMIT 1");
$ret = mysqli_fetch_array($res);
$temp1 = (float)$ret['temp1'];
$temp2 = (float)$ret['temp2'];
$temp3 = (float)$ret['temp3'];
$state = $ret['flag_state'];
$heating = $ret['flag_heating'];

$res = $mysqli->query("SELECT TIMEDIFF(NOW(), DATE_ADD(dt_created, INTERVAL 3 MINUTE)) AS dt_neg FROM log WHERE flag_heating!=$heating ORDER BY id_log DESC LIMIT 1");
$ret = mysqli_fetch_array($res);
$dtNeg = $ret['dt_neg'];

$res = $mysqli->query("SELECT TIMEDIFF(NOW(), DATE_ADD(dt_created, INTERVAL 3 MINUTE)) AS dt_neg FROM log WHERE flag_state!=$state ORDER BY id_log DESC LIMIT 1");
$ret = mysqli_fetch_array($res);
$dtReleNeg = $ret['dt_neg'];

// dnes sa kurilo
$res = $mysqli->query('SELECT CAST(dt_created AS date) AS dt_created, COUNT(*)*3 AS heated FROM log WHERE flag_state=1 GROUP BY CAST(dt_created AS date) ORDER BY 1 DESC LIMIT 14');
while ($ret = mysqli_fetch_array($res)) {
    $h = floor((int)$ret['heated'] / 60);
    $m = (int)$ret['heated'] - $h*60;
    break;
}
?>
<html>
<head>
    <meta charset="utf-8" />
    <title><?=number_format($temp2, 1, ',', ' ').'°C, '.($heating==1?'kúri':'nekúri').', '.$h.':'.(strlen($m)==1?'0':'').$m?> kúrilo</title>
    <meta name="viewport" content="width=480" \>
    <meta http-equiv="refresh" content="180">
</head>
<body style="width: 460px; margin:10px auto;">
    <div style="background-color:<?=($heating=='1' ? '#b01c1c' : '#0062dc')?>; padding:10px; text-align:center; color:#fff; font-size:84pt;">
        <?=number_format($temp2, 1, ',', ' ')?> °C
    </div>
    <div style="background-color:<?=($heating=='1' ? '#ff7171' : '#81b7f9')?>; padding:10px 5px; text-align:center; color:#fff; font-size:18pt; margin: 10px 10px 10px 0; float:left; width: 215px;">
        Trubka: <?=number_format($temp1, 1, ',', ' ')?> °C
    </div>
    <div style="background-color:<?=($heating=='1' ? '#ff7171' : '#81b7f9')?>; padding:10px 5px; text-align:center; color:#fff; font-size:18pt; margin: 10px 0 10px 0; float:left; width:215px;">
        Mrazák: <?=number_format($temp3, 1, ',', ' ')?> °C
    </div>
    <div style="cleaner:both;">
        <img src="<?=($heating=='1' ? 'heating.jpg' : 'cooling.jpg')?>" style="float:left; margin-right:20px;" />
        <div style="float:left; width: 250px; padding: 30px 0; font-size: 19pt;">
            <strong style="margin-bottom:10px;"><?=$state=='1' ? 'Kúri sa' : 'Nekúri sa'?></strong> <span style="font-size:10pt;">[h:m:s]</span><br/>
            <?=$heating=='1' ? 'Kúri už' :  'Nekúri už'?>: <strong><?=$dtNeg?></strong><br/><br/>
            <strong style="margin-bottom:10px;"><?=$state=='1' ? 'Pec zapnutá' : 'Pec vypnutá'?></strong><br/>
            <?=$state=='1' ? 'Zapnutá už' :  'Vypnutá už'?>: <strong><?=$dtReleNeg?></strong>
        </div>
    </div>
    <div style="padding:10px 0 0 210px;">
        <table>
           <tr><td>Dňa</td><td style="padding-left:20px;">sa kúrilo</td></tr>
        <?
        $res = $mysqli->query('SELECT CAST(dt_created AS date) AS dt_created, COUNT(*)*3 AS heated FROM log WHERE flag_state=1 GROUP BY CAST(dt_created AS date) ORDER BY 1 DESC LIMIT 14');
        while ($ret = mysqli_fetch_array($res)) {
            $datum = new DateTime($ret['dt_created']);
            $h = floor((int)$ret['heated'] / 60);
            $m = (int)$ret['heated'] - $h*60;
            echo '<tr><td>'.$datum->format('d.m.Y').'</td><td style="padding-left:20px;"><strong>'.$h.':'.(strlen($m)==1?'0':'').$m.'</strong></td></tr>';
        }
        ?>
        </table>
    </div>
</body>
</html>
<?
$mysqli->close();
?>