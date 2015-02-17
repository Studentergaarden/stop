#!/usr/bin/php
<?php
// Paw

// Must be run (by rc.firewall) on boot) and periodically by cron


require_once('dbconnect.inc.php');
require_once('functions.inc.php');


$debug = 0;

# command for iptables
$cmd = "iptables -t nat -I PREROUTING -p tcp -m mac --mac-source %s --dport 80 -j DNAT --to-destination 208.113.205.237\n";
$cmd = "iptables -t nat -D PREROUTING -p tcp -m mac --mac-source %s --dport 80 -j DNAT --to-destination 208.113.205.237\n";

$cmd = "iptables -t nat -I PREROUTING -p tcp -m mac --mac-source %s  -j DNAT --to-destination 172.16.0.11\n";
// $cmd = "iptables -t nat -D PREROUTING -p tcp -m mac --mac-source %s -j DNAT --to-destination 172.16.0.11\n";


$macs = array();

// get blocked macs from the db

if(check_mac($argv[1]))
  $sql_query = "SELECT mac FROM porn_block WHERE mac = '".$argv[1]."' AND expiry_date > NOW()";
else
  $sql_query = "SELECT mac FROM porn_block WHERE expiry_date > NOW()";


$result = mysql_query($sql_query)
    or die(mysql_error());

while ($current_row = mysql_fetch_array($result)) {
	$macs[] = $current_row['mac'];
}


// now we have an array of macs to block - now do it
foreach($macs as $mac){
    $out .= sprintf($cmd,$mac); // make the actual iptables command to insert rules
}



if($debug)
    print $out;
else{
    `$out`; // execute everything in $out
}


?>
