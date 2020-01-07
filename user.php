<?php
	session_start();
	include_once("lib.php");
	include_once("class/FastTemplate.php");
	include_once("class/Login.php");
	include_once("class/Database.php");
	include_once("parameters.php");
	include_once("head.php");

	// include source code
	$operation = "Administração de usuário:";
	$name = $_POST["txtName"];
	$email = $_POST["txtEmail"];
	$password1 = $_POST["txtPassword1"];
	$password2 = $_POST["txtPassword2"];
	if (($_GET["EDIT"] && $_GET["EMAIL"] != "") || ($email != "")) {
		$email = ($email != "")? $email :$_GET["EMAIL"];
		$user = new Login($db, $email);
		$user->loadNamePassword();
		if ($user->name != "")
			$name = $user->name;
	}
	if (($_GET["DELETE"] && $_GET["EMAIL"] != "")) {
		$user = new Login($db, $_GET["EMAIL"]);
		$user->delete();
		$email = "";
	}
	if (($email != "") && ($password1 != "") && ($password2 != "")) {
		if ($password1 == $password2) {
			$user = new Login($db, $email);
			if ($user->change($password1, $name))
				$message .= "User changed.";
			else
				$message .= "User not changed.";
		} else {
			$message .= "Password Cancel.";
		}
	}

	$aTemplates = array("user"=>"user.html");
	$template->define($aTemplates);
	$template->define_dynamic("user.row", "user");
	$template->assign("{DELETE.LINK}", "#");

	// lista os usuários
	$db->saveSnapshot();
	$db->getSql("
		select name, email from login
	");
	$db->define("name", $loginName);
	$db->define("email", $loginEmail);
	$db->execute();
	while ($db->fetch()) {
		$href = $_SERVER["PHP_SELF"]."?EDIT=1&EMAIL=".urlencode($loginEmail);
		$template->assign("{USER.LINK}", $href);
		$href = $_SERVER["PHP_SELF"]."?DELETE=1&EMAIL=".urlencode($loginEmail);
		$template->assign("{DELETE.LINK}", $href);
		$template->assign("{NAME}", $loginName);
		$template->assign("{EMAIL}", $loginEmail);
		$template->parse("USERROW", ".user.row");
	}
	$db->loadSnapshot();

	$template->assign("{OPERACAO}", $operation);
	$template->assign("{NAME}", $name);
	$template->assign("{EMAIL}", $email);
	$template->assign("{PASSWORD1}", "");
	$template->assign("{PASSWORD2}", "");
	$template->assign("{MESSAGE}", $message);
	$template->parse("{DESCRIPTION}", "user");

	include_once("foot.php");
?>
