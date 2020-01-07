<?php
	session_start();
	include_once("lib.php");
	include_once("class/FastTemplate.php");
	include_once("class/Login.php");
	include_once("class/Database.php");
	include_once("parameters.php");
	include_once("head.php");

	// include source code
	$delete = $_GET["DELETE"];
	if ($login && ($delete != "")) {
		$db->saveSnapshot();
		$db->getSql("
			delete from news where id = :id
		");
		$db->bind(":id", $delete);
		$db->execute();
		$db->loadSnapshot();
	}
	
	$aTemplates = array("description"=>"description.html");
	$template->define($aTemplates);
	$template->define_dynamic("description.item", "description");

	$db->saveSnapshot();
	$db->getSql("
		select id, tempo, titulo, descricao 
		from news"
	);
	$db->define("id", $id);
	$db->define("tempo", $time);
	$db->define("titulo", $title);
	$db->define("descricao", $content);
	$db->execute();
	while ($db->fetch()) {

		$tool = "";
		if ($login) {
			$href = "upload.php?EDITAR=$id";
			$tool = '<a href="'.$href.'">Editar</a>&nbsp;';
			$href = $_SERVER["PHP_SELF"]."?DELETE=$id";
			$tool .= '<a href="'.$href.'">Deletar</a>';
		}

		$template->assign("{DESCRIPTION.TITLE}", $title);
		$template->assign("{DESCRIPTION.CONTENT}", $content);
		$template->assign("{DESCRIPTION.TOOL}", $tool);
		$template->parse("DESCRIPTION.ITEM", ".description.item");
	}
	$db->loadSnapshot();
	
	$template->parse("{DESCRIPTION}", "description");

	include_once("foot.php");
?>
