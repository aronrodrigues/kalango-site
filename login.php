<?
	session_start();
	include_once("class/FastTemplate.php");
	include_once("class/Login.php");
	include_once("class/Database.php");
	include_once("parameters.php");

	//var_dump($_REQUEST);
	//echo md5($_POST["txtEmail"].$_POST["txtPassword"]);

	$db = new Database();
	$db->connect($dbHost, $dbId, $dbUser, $dbPassword);

	$message = "";

	if ($email = $_POST["txtEmail"]) {
		$user = new Login($db,$email);
		if($user->verify($_POST["txtPassword"])) {

			$_SESSION["login"]=true;

			$querys = array();
			$querys[] = "PHPSESSID=".$_REQUEST["PHPSESSID"];

			$href = "index.php";
			if($_POST["HREF"]) {
				$href = $_POST["HREF"];
			}

			if($_POST["QUERY"]) {
				$querys[] = $_POST["QUERY"];
			}
			$href = $href."?".join("&", $querys);
			header("location: $href");
			exit;
		} else {
			$_SESSION["login"]=false;
			$message = "Usuário ou senha inválido(s).";
		}
	}

	// Visualizacao,
	$template = new FastTemplate("./templates");

	$template->define(
		array (
			"main" => "main.html",
			"head" => "head.html",
			"foot" => "foot.html",
			"login" => "login.html"
		)
	);

	$template->assign("{LOGIN.MSG}", ": ".$message);
	$template->assign("{HREF}", $_GET["HREF"]);
	$template->assign("{QUERY}", $_GET["QUERY"]);

	// Atualiza o MENU
	$template->assign("{MENU}","");
	$template->assign("{BODY.ONLOAD}","document.login.txtEmail.focus()");

	// Link das variaveis com as constantes.
	$template->parse("{HEAD}","head");
	$template->parse("{CONTENT}","login");
	$template->parse("{FOOT}","foot");
	$template->parse("OUTPUT","main");

	$template->FastPrint("OUTPUT");
?>
