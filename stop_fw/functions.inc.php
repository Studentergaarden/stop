<?php

function check_mac($mac){
$regex = '/^([0-9A-F][0-9A-F]\:){5}[0-9A-F][0-9A-F]$/';
	if (preg_match($regex, $mac))
	    return TRUE;
	else
	    return FALSE;
}

?>