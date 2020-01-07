<?php
	session_start();
	include_once("lib.php");
	include_once("class/FastTemplate.php");
	include_once("class/Login.php");
	include_once("class/Database.php");
	include_once("parameters.php");
	include_once("head.php");

	// Exclui o arquivo
	$delete = $_GET["DELETE"];
	if ($login && $delete != "") {
		$db->saveSnapshot();
		$db->getSql("select 1 from files where id = :id");
		$db->bind(":id",  $delete);
		if ($db->execute() && $db->fetch()) {
			$db->getSql("
				delete from files
				where	id = :id
			");
			$db->bind(":id",  $delete);
			if ($db->execute() && unlink($upLoadDir.$delete)) {
				$message .= "Arquivo deletado com sucesso.";
			} else {
				$message .= $upLoadDir.$delete;
				$message .= "Erro ao tentar deletar o arquivo.";
			}
		}
		$db->loadSnapshot();
	}

	// Faz o download do arquivo
	if ($id = $_GET["IDFILE"]) {
		$db->saveSnapshot();
		$db->getSql("
			select name, type, size
			from	files
			where id = :id
		");
		$db->bind(":id",  $id);
		$db->define("name",  $name);
		$db->define("type",  $type);
		$db->define("size",  $size);
		$db->execute();
		$db->fetch();
		$db->loadSnapshot();
		header("Content-type: $type");
		header("Content-Disposition: attachment; filename=\"$name\"");
		readfile($upLoadDir.$id);
		exit;
	}

	// Exibe os links para downloads
	$aTemplates = array("download"=>"download.html");
	$template->define($aTemplates);
	$template->define_dynamic("download.file", "download");

	$cntFetch = 0;
	$db->saveSnapshot();
	$db->getSql("
		select id, name, type, size
		from	files
	");
	$db->define("id",  $id);
	$db->define("name",  $name);
	$db->define("type",  $type);
	$db->define("size",  $size);
	$db->execute();
	while($db->fetch()) {
		$cntFetch++;
		$delete = "";
		if ($login) {
			$href = $_SERVER["PHP_SELF"]."?DELETE=$id";
			$delete = '<td><a href="'.$href.'">Delete</a></td>';
		}
		$href = $_SERVER["PHP_SELF"]."?IDFILE=".$id;
		$template->assign("{HREF}", $href);
		$template->assign("{NAME}", $name);
		$template->assign("{TYPE}", $type);
		$template->assign("{SIZE}", $size);
		$template->assign("{DELETE}", $delete);
		$template->parse("FILES", ".download.file");
	}
	$db->loadSnapshot();
	
	$delete = "";
	if ($login) {
		$delete = '<th>Operação</th>';
	}
	$template->assign("{DELETE}", $delete);
	if ($cntFetch)
		$template->parse("{DESCRIPTION}", "download");
	else
		$template->assign("{DESCRIPTION}", "<h1>Lista vazia!!!</h1>");

	include_once("foot.php");
?>
