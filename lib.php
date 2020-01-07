<?php
function getScriptFilename() {
	$script = $_SERVER["SCRIPT_NAME"];
	$pos = strrpos($script, "/") + 1;
 	$script = substr($script, $pos);
	return $script;
}
function removeParamQuery($_param, $_query) {
	$query = split("&", $_query);
	$tmp = array();
	foreach($query as $key => $value) {
		if (!strstr($value, $_param))
			$tmp[] = $value;
	}
	return join("&", $tmp);
}
function doLogin($login) {
	global $applLogin;
	$script = getScriptFilename();
	$href = urlencode($_SERVER["SCRIPT_NAME"]);
	$query = removeParamQuery("PHPSESSID", $_SERVER["QUERY_STRING"]);
	$query = removeParamQuery("LOGOUT", $query);
	$query = urlencode($query);
	if (in_array($script, $applLogin) && !$login) {
		header("location: login.php?HREF=$href&QUERY=$query");
		exit;
	}
	return $login;
}
// Array com nomes dos scripts que necessitam de usuario autenticado.
$applLogin = array("upload.php", "user.php");
?>
