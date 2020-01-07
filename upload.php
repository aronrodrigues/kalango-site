<?php
	session_start();
	include_once("lib.php");
	include_once("class/FastTemplate.php");
	include_once("class/Login.php");
	include_once("class/Database.php");
	include_once("parameters.php");
	include_once("head.php");

	// include source code
	$message = "";

	$files = $_FILES["userfile"];
	if ($files) {

		$name = $files["name"];
		$type = $files["type"];
		$size = $files["size"];
		$temp = $files["tmp_name"];

		// armazena no banco as informações
		$db->saveSnapshot();
		$db->getSql("
			insert into files(name, type, size)
			values (:name, :type, :size)
		");
		$db->bind(":name", $name);
		$db->bind(":type", $type);
		$db->bind(":size", $size);
		if (($temp != "") && ($name != "") && $db->execute()) {
			$db->getSql("
				select max(id) as maxid
				from files
				where	name = :name
				and	type = :type
				and	size = :size
				group by name, type, size
			");
			$db->bind(":name", $name);
			$db->bind(":type", $type);
			$db->bind(":size", $size);
			$db->define("maxid", $maxid);
			if (!$db->execute() || !$db->fetch()) {
				$message .= "Erro ao realizar o Upload do arquivo.";
			}
		}
		$db->loadSnapshot();

		if ($maxid) {
			// uploaddir está configurada em parameters.php
			$dst_name = $upLoadDir.$maxid;

			// mensagem customizada
			if (move_uploaded_file($temp, $dst_name)) {
				$message .= "Arquivo $name carregado com sucesso.";
			} else {
				$message .= "Erro ao carrega o arquivo $name.";
				$db->saveSnapshot();
				$db->getSql("delete from files where id = :id");
				$db->bind(":id", $maxid);
				$db->execute();
				$db->loadSnapshot();
			}
		}
	}

	$operation = $_POST["hidOperation"];
	if ($operation == "")
		$operation = "I";

	$operationTitle = "Modo de inserção:";
	$title = "";
	$content = "";
	$idNews = $_POST["idNews"];

	$editar = $_GET["EDITAR"];
	if ($editar != "" || $operation == "E") {
		if ($editar != "")
			$idNews = $editar;
		$operation = "E";
		$operationTitle = "Modo de Edição:";
		$db->saveSnapshot();
		$db->getSql("select
			titulo, descricao
			from news where id = :id
		");
		$db->bind(":id", $idNews);
		$db->define("titulo", $title);
		$db->define("descricao", $content);
		if ($db->execute() && $db->fetch()) {
			$newsFinded = true;
			$message .= "News encontrado para Edição.";
		} else {
			$message .= "News não encontrada.";
		}
		$db->loadSnapshot();
	}

	$txtTitulo = $_POST["txtTitulo"];
	$txaContent = $_POST["txaContent"];
	if (($txtTitulo != "") && ($txaContent != "")) {
		$title = $txtTitulo;
		$content = $txaContent;
		if ($newsFinded && $operation == "E") {
			$db->saveSnapshot();
			$db->getSql("
				update news set
				titulo = :titulo,
				descricao = :descricao
				where	id = :id
			");
			$db->bind(":id", $idNews);
			$db->bind(":titulo", $title);
			$db->bind(":descricao", $content);
			if ($db->execute()) {
				$message .= "News alterado com sucesso.";
			} else {
				$message .= "Erro ao alterar news.";
			}
			$db->loadSnapshot();
		}
		if ($operation == "I") {
			$db->saveSnapshot();
			$db->getSql("
				insert into news(tempo, titulo, descricao)
				values (now(), :titulo, :descricao)
			");
			$db->bind(":titulo", $title);
			$db->bind(":descricao", $content);
			if ($db->execute()) {
				$message .= "News inserido com sucesso.";
			} else {
				$message .= "Erro ao tentar inserir news.";
			}
			$db->loadSnapshot();
		}
	}

	if ($message != "")
		$message = '<div id="message">'.$message.'</div>';

	$aTemplates = array("upload"=>"upload.html");
	$template->define($aTemplates);
	$template->assign("{UPLOAD.OPERATION}", $operation);
	$template->assign("{UPLOAD.OPERATION.TITLE}", $operationTitle);
	$template->assign("{UPLOAD.TITLE}", $title);
	$template->assign("{UPLOAD.CONTENT}", $content);
	$template->assign("{UPLOAD.IDNEWS}", $idNews);
	$template->assign("{UPLOAD.MSG}", $message);
	$template->parse("{DESCRIPTION}", "upload");
	
	include_once("foot.php");
?>
