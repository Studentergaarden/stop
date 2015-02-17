#!/usr/bin/php
<?php
// Paw

require_once('dbconnect.inc.php');
require_once('functions.inc.php');


$debug = 0;
$run = 1;


// command for iptables
$cmd = "iptables -t nat -D PREROUTING -p tcp -m mac --mac-source %s -j DNAT --to-destination 172.16.0.12\n";

// $argv[1] is the first option given on the command line (0 is the name of the script itself)
if(check_mac($argv[1]))
    $sql_query = "SELECT mac, expiry_date, user, url FROM porn_block WHERE mac = '".$argv[1]."'";
else
    $sql_query = "SELECT mac, expiry_date FROM karsten_block WHERE expiry_date < NOW()";

// get mac/macs from the db - that have expired
$result = mysql_query($sql_query)
    or die(mysql_error());

// log the used mac-adr
$file = '/etc/firewall/stop/removed_macs.txt';
while ($current_row = mysql_fetch_array($result)) {
  
  $log = sprintf("%s \t %s \t %s \t %s \n",$current_row['mac'], $current_row['expiry_date'], $current_row['user'], $current_row['url']);
  file_put_contents($file, $log , FILE_APPEND);
  // make the actual iptables command to insert rules for these MACs
  $out = sprintf($cmd,$current_row['mac']);
	    
  if($debug){
    print $out;
  }
  if($run){
    `$out`; // execute
  }
}


if($argv[1])
    $sql_query = "DELETE FROM porn_block WHERE mac = '".$argv[1]."'";
else
    $sql_query = "DELETE FROM porn_block WHERE expiry_date < NOW()";
$result = mysql_query($sql_query)
    or die(mysql_error());


?>
