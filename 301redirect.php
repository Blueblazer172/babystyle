<?php

function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function curPageURL() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	$pageURL .= 'www.'.$_SERVER["SERVER_NAME"];
	return $pageURL;
}

switch(curPageURL()){

	case 'http://www.babystyle.ca/':
									$redirect_url = 'https://www.babystyle.ca/';
									header("Location: ".$redirect_url, true, 301);
									exit();

	case 'https://babystyle.ca/':
								$redirect_url = 'https://www.babystyle.ca/';
								header("Location: ".$redirect_url, true, 301);
								exit();

	case 'http://babystyle.ca/':
								$redirect_url = 'https://www.babystyle.ca/';
								header("Location: ".$redirect_url, true, 301);
								exit();
}

if(strstr($_SERVER['REQUEST_URI'],"?SID="))
{
	header("Location: https://www.babystyle.ca/", true, 301);
	exit();
}
if($_SERVER['REQUEST_URI'] == '/home')
{
	header("Location: https://www.babystyle.ca/", true, 301);
	exit();
}
?>