#!/usr/bin/php
<?php
   // Paw Møller

   /*
     Made for christmas-prank 2014
    */

$debug = 0;

// command for iptables
$cmd = "iptables -t nat -I PREROUTING -p tcp -d %s -j DNAT --to-destination 172.16.0.10\n";


// array of allowed hostnames
$a[]='youporn.com';
$a[]='redtube.com';
//$a[]='thepiratebay.se';
//$a[]='kickass.so';


$ips = array();

foreach($a as $host){
    $h = gethostbynamel($host); //get list of all IP numbers (there may be several IP numbers fro one hostname) (returns array)
    
    if($h)
        $ips = array_merge($ips, $h); // add the array of IP numbers for this host to the big array of all IP numbers

}

if ($debug)
    print_r($ips);


foreach($ips as $ip){
    
    $out .= sprintf($cmd,$ip); // make the actual iptables command to insert rules for these IP numbers
    
}

if($debug)
    print $out; // for debugging
else
    `$out`; // execute everything in $out

?>
