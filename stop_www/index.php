<!DOCTYPE html>
<meta charset="utf-8"> 
<html>
<body>

<br><br><br>
<font size=8 color=red><center><b> STOP </b></center></font>
 
<hr color="black" size="5" width="50%" noshade>

<center>
<p>
   Som følge af vedtagelsen af lovforslag 
<!-- <a href="http://www.folketingstidende.dk/RIpdf/samling/20131/lovforslag/L192/20131_L192_som_vedtaget.pdf"><b>L 192</b></a> -->
   <a href="img/20131_L192_som_vedtaget.pdf"><b>L 192</b></a>
   har <i>Center for Cybersikkerhed</i> spærret for adgang til

   <?php
   $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   echo '<b>' .  $url . '</b>';
   ?>
   <br>

   Siden mistænkes for distribution af børneporno, finansering af kriminelle netværk samt spredning af vira og malware.

   <br>
   <br>
   Samtidig - med hjemmel i §10 - er dit besøg registreret og gemmes til senere brug i efterforskningen af aktiviteterne på siden.

   <br>
   Du er registreret med følgende oplysninger:

</p>
</center>


<?php

require_once "functions.inc.php";   
require_once "dbconnect.inc.php";
// require_once "dbconnect.loki.inc.php";

function formattt($str){
  return '<tt>' . $str . '</tt>';
}

/* MySQL is case insensitive, unless you do a binary comparison - thus we don't need to
capitalize mac. mac-adr in table mac_info is capitalized */
$mac=get_mac();
$username = user_from_mac($mac,'mac_info');
$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$sql = "select user_id, user, name, user.name_id from user, name where user = '$username' and name.name_id = user.name_id";
$result = mysql_query($sql,$db_loki);
$onerow = mysql_fetch_array($result);
$name = $onerow['name'];

// insert mac into db, if not already there
if (!user_from_mac($mac,'porn_block')){

/*
$sql_delete_old="DELETE FROM porn_block WHERE expiry_date <= NOW()"; 
mysql_query($sql_delete_old) 
or die(mysql_error());
*/

  $expiry_date =  date('Y-m-d H:i:s', time()+60*5); // block for 5 min
  $sql_insert_host="INSERT INTO porn_block (mac, expiry_date, user, url) VALUES ('$mac','$expiry_date','$username','$url')";
  mysql_query($sql_insert_host,$dblink)
    or die(mysql_error());

// call script to actually insert firewall rule (sudo lets www-data do this)
system("sudo /etc/firewall/stop/block_macs.php $mac > /dev/null");

}


echo '<center>';

echo 'MAC-adressse:' . formattt($mac) . '<br>' ;
echo 'IP addresse: ' . ' ' . formattt($_SERVER['REMOTE_ADDR']).'<br>';
echo 'Navneserver: ' . formattt('ns.studentergaarden.dk') . '<br>';
//echo 'Brugernavn: ' . formattt($username . '<br>');
echo 'Navn: ' . formattt($name);

echo '</center>';

?>



<center>
<p>
Dit internet vil nu blive blokeret de næste fem minutter - du kan bruge tiden på at tænke over
<br>
hvad du var på vej til at gøre. Tænk i stedet på din kæreste.
<br><br>
Der påbegyndes en efterforskning for at klargøre formålet med dit besøg på siden.<br>
For at hjælpe din sag på vej, kan du skrive en mail til <a href="mailto:cfcs@cfcs.dk">cfcs@cfcs.dk</a>,
hvori du kort forklarer dit formål med besøget på siden. <br>

Emnefeltet skal være <br>

   <?php
   $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   echo '<tt> \'' . $url . '+' . $_SERVER['REMOTE_ADDR'] . '\' </tt>';
   ?>

<br>
<br>

Du bør ligeledes kontakte din lokale netværksadministrator - vi vil rette henvendelse til denne <br>
og efterforskningen skrider hurtige frem hvis han har de relevante oplysninger. 


</p>
</center>

<hr color="black" size="5" width="50%" noshade>

<center>
<img src="img/Internet_Logo_CFCS_Med_navnetraek.jpg" alt="CFCS logo">
</center>


</body>
</html>