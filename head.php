<?php
if ($_GET["LOGOUT"]) {
	$_SESSION["login"] = false;
}

$login = doLogin($_SESSION["login"]);

$db = new Database();
$db->connect($dbHost, $dbId, $dbUser, $dbPassword);

if (!$_SESSION["login"]) {
	$db->saveSnapshot();
	$db->getSql("
		select count(*) as cnt from login
	");
	$db->define("cnt", $countUser);
	$db->execute();
	$db->fetch();
	if ($countUser == 0)
		$login = $_SESSION["login"] = true;
	$db->loadSnapshot();
}

if ($login) {
	$links = array(
		"index.php" => "Principal",
		"forum.php" => "Forum",
		"upload.php" => "Up Load",
		"download.php" => "Download",
		"link.php" => "Links",
		"user.php" => "Usuários"
	);
	$logout = getScriptFilename()."?LOGOUT=1";
	$links[$logout] = "Logout";
} else {
	$links = array(
		"index.php" => "Principal",
		"forum.php" => "Forum",
		"download.php" => "Download",
		"link.php" => "Links",
		"login.php" => "Login"
	);
}

// Visualizacao,
$template = new FastTemplate("./templates");

$template->define(
	array (
		"main" => "main.html",
		"menu" => "menu.html",
		"head" => "head.html",
		"content" => "content.html",
		"foot" => "foot.html"
	)
);
$template->define_dynamic("menu.item", "menu");
$template->assign("{BODY.ONLOAD}", "");

// Constroi o menu
$script = getScriptFilename();
foreach ($links as $href => $label) {
	$class = ($href == $script)? " class=\"selected\"" : "";
	$template->assign("{CLASS.MENU.ITEM}", $class);
	$template->assign("{HREF.MENU.ITEM}", $href);
	$template->assign("{LABEL.MENU.ITEM}", $label);
	$template->parse("LINKS", ".menu.item");
}
?>
