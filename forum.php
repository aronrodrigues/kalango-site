<?php
	session_start();
	include_once("lib.php");
	include_once("class/FastTemplate.php");
	include_once("class/Login.php");
	include_once("class/Database.php");
	include_once("parameters.php");
	include_once("head.php");

	$delete = $_GET["DELETE"];
	if ($delete) {
		$timepost = $_GET["TIMEPOST"];
		$name = $_GET["NAME"];
		if ($timepost != "" && $name != "") {
			$db->saveSnapshot();
			$db->getSql("
				delete from forum 
				where timepost = :timepost and name = :name
			");
			$db->bind(":timepost", $timepost);
			$db->bind(":name", $name);
			if ($db->execute()) {
				$message = "Notação deletada com sucesso.";
			} else {
				$message = "Falha ao tentar deletar a notação.";
			}
			$db->loadSnapshot();
		}
	}

	$edit = $_REQUEST["EDIT"];
	if ($edit) {
		$aTemplates = array("forum"=>"forumPost.html");
		$template->define($aTemplates);
		$name = $_POST["txtName"];
		$content = $_POST["txaContent"];
		if ($name != "" && $content != "") {
			$db->saveSnapshot();
			$db->getSql("
				insert into forum(timepost, name, content)
				values(now(), :name, :content)
			");
			$db->bind(":name", htmlentities($name));
			$db->bind(":content", htmlentities($content));
			if ($db->execute()) {
				$message = "Sua anotação foi adicionada a lista com sucesso.";
			} else {
				$message = "Sua anotação não pode ser inserida na lista.";
				$message .= " Tente novamente.";
			}
		}
	} else {
		$aTemplates = array("forum"=>"forum.html");
		$template->define($aTemplates);
		$template->define_dynamic("forum.item", "forum");
		$template->define_dynamic("forum.subitem", "forum");

		$db->saveSnapshot();
		$db->getSql("
			select timepost, date_format(timepost, '%d/%m/%Y %H:%i') as datetime , name, content
			from forum
			group by name, timePost
			order by timePost desc
		");
		$db->define("timepost", $timepost);
		$db->define("datetime", $datetime);
		$db->define("name", $name);
		$db->define("content", $content);
		$db->execute();
		$namePrev = "";

		// Valor padrão
		$template->assign("{HEADER.FORUM.LINK}", "");
		$template->assign("{HEADER.FORUM.TIME}", "");
		$template->assign("{DESCRIPTION.FORUM}", "Lista vazia.");
		while ($db->fetch()) {
			if ($namePrev != "" && $namePrev != $name) {
				$template->assign("{HEADER.FORUM.USER}", $namePrev);
				$template->parse("{FORUM.ITEM}", ".forum.item");
				$template->clear_parse("{FORUM.SUBITEM}");
			}
			if ($login) {
				$href = "?TIMEPOST=".$timepost."&NAME=".$name;
				$href .= "&DELETE=1";
				$link = '<a href="'.$href.'">Deletar</a>';
			} else {
				$link = "";
			}
			$template->assign("{HEADER.FORUM.LINK}", $link);
			$template->assign("{HEADER.FORUM.TIME}", $datetime);
			$template->assign("{DESCRIPTION.FORUM}", $content);
			$template->parse("{FORUM.SUBITEM}", ".forum.subitem");
			$namePrev = $name;
		}
		$db->loadSnapshot();
		$template->assign("{HEADER.FORUM.USER}", $namePrev);
		$template->parse("{FORUM.ITEM}", ".forum.item");
	}
	if ($message != "")
		$message = '<div id="message">'.$message.'</div>';

	$template->assign("{FORUM.MSG}", $message);
	$template->parse("{DESCRIPTION}", "forum");

	include_once("foot.php");
?>
