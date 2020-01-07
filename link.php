<?php
	session_start();
	include_once("lib.php");
	include_once("class/FastTemplate.php");
	include_once("class/Login.php");
	include_once("class/Database.php");
	include_once("parameters.php");
	include_once("head.php");

	$delete = $_GET["DELETE"];
	if ($login && $delete != "") {
		$db->saveSnapshot();
		$db->getSql("delete from links where href = :href");
		$db->bind(":href", $delete);
		if ($db->execute()) {
			$message = "Link removido da lista com sucesso.";
		} else {
			$message = "Falha ao remover Link da lista.";
		}
		$db->loadSnapshot();
	}

	$href = $_POST["txtHref"];
	$description = $_POST["txtDescription"];
	if ($login && $href != "" && $description != "") {
		if (!strstr($href, "http://"))
			$href = "http://".$href;
		$name = str_replace("http://", "", $href);
		$pos = strpos($name, "/");
		if ($pos !== false)
			$name = substr($name, 0, $pos);
		if ($name != "") {
			$db->saveSnapshot();
			$db->getSql("
				insert into links(href, name, description)
				values(:href, :name, :description)
			");
			$db->bind(":href", $href);
			$db->bind(":name", $name);
			$db->bind(":description", $description);
			if ($db->execute()) {
				$message = "Link adicionado a lista com sucesso.";
			} else {
				$message = "Falha ao adicionar Link a lista.";
			}
			$db->loadSnapshot();
		}
	}

	$aTemplates = array(
		"link"=>"link.html",
		"linkadd"=>"linkAdd.html"
	);
	$template->define($aTemplates);
	$template->define_dynamic("link.item", "link");

	if ($login) {
		$template->parse("{LINK.ADD}", "linkadd");
		$template->assign("{LINK.HEADER}", "<th>Excluir</td>");
	} else {
		$template->assign("{LINK.ADD}", "");
	}
	$template->assign("{LINK.HREF}", "");
	$template->assign("{LINK.NAME}", "");
	$template->assign("{LINK.DESCRIPTION}", "");

	$db->saveSnapshot();
	$db->getSql("
		select href, name, description
		from links"
	);
	$db->define("href", $href);
	$db->define("name", $name);
	$db->define("description", $description);
	$db->execute();
	while ($db->fetch()) {
		$hrefDel = "<td style='text-align: center'><a href='?DELETE=".urlencode($href)."'>X</a></td>";
		$template->assign("{LINK.HREF}", $href);
		$template->assign("{LINK.NAME}", $name);
		$template->assign("{LINK.DESCRIPTION}", $description);
		$template->assign("{LINK.DELETE}", $hrefDel);
		$template->parse("LINK.ITEM", ".link.item");
	}
	$db->loadSnapshot();
	
	if ($message != "")
		$message = '<div id="message">'.$message.'</div>';

	$template->assign("{LINK.MSG}", $message);
	$template->parse("{DESCRIPTION}", "link");

	include_once("foot.php");
?>
