<?php

// Nicolas Padfield nicolas@padfield.dk
// Esben Rune Hansen esben@studentergaarden.dk

$print_sql_queries = 0;


// find out if mac is blocked for offending 
// if it is, return reason
// if it is not, return false

function mac_offending($mac) {

    $sql_query = "SELECT block FROM mac_info WHERE mac = '$mac'";
    $result = mysql_query($sql_query)
	or die (mysql_error());
	
    $current_row = mysql_fetch_assoc($result);
    
    $block = $current_row['block'];
    
    if (strlen($block)>3)
	return $block;
    else
	return FALSE;
	
}

function mac_expired($mac) {

    $sql_query = "SELECT mac FROM mac_info WHERE mac = '$mac' AND expiry_date < NOW()";
    $result = mysql_query($sql_query)
	or die (mysql_error());
	
    if (mysql_num_rows($result) > 0)
	return TRUE;
    else
	return FALSE;
	
}



// find user name from MAC address - can also be used to see if a mac is known in the DB

function user_from_mac($mac, $table) {

    $sql_query = "SELECT user FROM " . $table . " WHERE mac = '$mac' AND expiry_date > NOW()";    
    $result = mysql_query($sql_query)
	or die (mysql_error());

    if (mysql_num_rows($result) > 1)
      print '';
      //print("Warning: MAC address is allocated to more than one user!");
    elseif (mysql_num_rows($result) < 1)
        return FALSE;
	
    $current_row = mysql_fetch_assoc($result);
    return $current_row['user'];

}

// find MAC adresses from user name (returns array)

function macs_from_user($user) {

    $sql_query = "SELECT * FROM mac_info WHERE user = '$user'";
    $result = mysql_query($sql_query)
	or die (mysql_error());

    while ($current_row = mysql_fetch_assoc($result)) {
	$ret[] = Array ('mac' => $current_row['mac'], 'comment' => $current_row['comment'], 'limit' => $current_row['limit'], 'expiry_date' => $current_row['expiry_date']);
    }
	
    
    return $ret;

}
 
// Make hash-map that maps IP-addresses to MAC-addresses	

function arp_table(){
	/*$arp_entries = explode("\n",`arp -n`);
	foreach ($arp_entries as $entry) {
		$fields = split("[ \t]+",$entry);
		if ($fields[1] == "ether") {
			$ip = $fields[0];
			$mac = $fields[2];
			$arp_table[$ip] = $mac;
		}
	}
	return $arp_table;*/
    $arp_entries = explode("\n",`arp -an`);
    foreach ($arp_entries as $entry)
    {
    	$fields = explode(" ",$entry);
	if ($fields[3] != "<incomplete>") {
	        $ip = substr($fields[1],1,-1); //from second to second last letter: remove paranthesises
	        $mac = $fields[3];
        	$arp_table[$ip] = $mac;
	}
    }	
    return $arp_table;
}
//print_r(arp_table());


function check_ip($ip){

$regex = '/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/';
	if (preg_match($regex, $ip))
	    return TRUE;
	else
	    return FALSE;


}

function check_internal_ip($ip){

$regex = '/^172\.16\.([0-9]{1,3})\.([0-9]{1,3})$/';
	if (preg_match($regex, $ip))
	    return TRUE;
	else
	    return FALSE;


}

function check_mac($mac){

$regex = '/^([0-9A-F][0-9A-F]\:){5}[0-9A-F][0-9A-F]$/';
	if (preg_match($regex, $mac))
	    return TRUE;
	else
	    return FALSE;


}

//print check_mac('4E:EE:EE:EE:EE:EE');
//print check_internal_ip('172.16.2.3');

function get_mac(){
	
	//Nicolas Padfield nicolas@padfield.dk
	
	$debug = 0;

	$adr = $_SERVER[REMOTE_ADDR];
		
	usleep(10000);
	
	$arpstring = '/usr/sbin/arp -a '.$adr;
	
	$arp = exec($arpstring);
	
	//echo $arp.'<BR><BR>';
	
	$match = preg_match ('/..\:..\:..\:..\:..\:../', $arp, $matches);
	
	//echo $match.'<BR><BR>';
	
	if (!$match)
	{ $mac = "Kunne ikke detektere MAC adresse automatisk - prøv venligst igen";}
	else
	{ $mac = $matches[0]; }
	
	if($debug) {
	    echo 'IP address: '.$adr.'<BR>';
	    echo 'MAC address: '.$mac.'<BR>';
	}
	
	return $mac;
}

?>
