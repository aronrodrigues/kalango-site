<?php
class Database {
	var $connection;		// conexão com o banco dados.
	var $bindList;		// lista das variaveis em binds.
	var $defineList;		// lista das variaveis em defines.
	var $resultSet;		// tabela com os resultados da ultima instrução executada.
	var $sql;			// instrução SQL.
	var $snapshot;		// imagem de instruções salvas.
	
	// retorna erro do banco de dados.	
	function dbError($_local, $_msg) {
		$msgSQL = mysql_error();
		$_local = $_SERVER['PHP_SELF']." - ".$_local;
		
		print("<p><font color=\"#FF0000\">");
		print("Error: (".$_local.") ".$_msg."<br />".$msgSQL);
		print("</font></p>");
		print("<p><a href=\"javascript:history.go(-1)\">Back</a></p>");
		
		exit;
	}

	// salva estado atual do objeto.
	function saveSnapshot() {
		$oldSnapshot = $this->snapshot;
		$this->snapShot = array(
			"connection" => $this->connection,
			"bindList" => $this->bindList, 
			"defineList" => $this->defineList,
			"resultSet" => $this->resultSet,
			"sql" => $this->sql,
			"snapshot" => $oldSnapshot);
		
	}

	// retorna estado do objeto.
	function loadSnapshot() {
		$this->resultSet = $this->snapShot["resultSet"];
		$this->bindList = $this->snapshot["bindList"];
		$this->defineList = $this->snapShot["defineList"];
		$this->sql = $this->snapShot["sql"];
		$this->defineList = $this->snapShot["defineList"];
		$this->sql = $this->snapShot["sql"];
		$this->connection = $this->snapShot["connection"];
		$this->snapshot = $this->snapShot["snapshot"];
	}
	
	// cria conexão com o banco de dados.
	function connect($_host, $_db, $_user, $_pwd) {
		$this->connection = mysql_connect($_host,$_user,$_pwd) or
			$this->dbError("main","Connection refused.");
	 	mysql_select_db($_db) or
			$this->dbError("main","Wrong database.");
	}

	// associa uma variável do sistema a campo do banco de dados.
	function bind($_field, &$_var) {
		$this->bindList[$_field] = &$_var;
	}
	
	// associa uma coluna no result set a uma variável do sistema.
	function define($_result, &$_var) {
		$this->defineList[strtolower($_result)] = &$_var;
	}

	// executa instrução no banco de dados.
	function execute() {
		$this->resultSet = mysql_query($this->parse())
			or $this->dbError("Database.execute()","Wrong SQL.");
		return $this->resultSet;
	}

	// pega a proxima linha no result set e passa seu resultado para os defines.
	function fetch() {
		$row = mysql_fetch_array($this->resultSet);
		// se existirem linhas.
		if($row) {
			// devolve valor da coluna para cada define. 
			foreach($this->defineList as $field => $value)
				$this->defineList[$field] = $row[$field];
			return true;
		}
		return false;
	}

	// pega os valores dos binds e passa para a instrução SQL.	
	function parse() {
		$sql = "";
		$tokens = explode("'", $this->sql);
		foreach ($tokens as $i => $token) {
			if ($i % 2) {
				$sql .= "'" . $token . "'";
				continue;
			}
			if (!is_array($this->bindList)) {
				$sql .= $token;
				continue;
			}
			// coloca os binds.
			foreach ($this->bindList as $bind_name => $bind_var) {
				$token = eregi_replace(
					$bind_name . "([^a-z0-9_])",
					"'" . $bind_var . "'" . "\\1", $token);
				$token = eregi_replace($bind_name . "$",
					"'" . $bind_var . "'", $token);
			}
			$sql .= $token;
		}
		return $sql;
	}

	// cria uma nova instrução.
	function getSql($_sql) {
		$this->bindList = Array();
		$this->defineList = Array();
		$this->resultSet = NULL;
		$this->sql = $_sql;
	}

	// fecha conexão com o banco de dados.
	function close() {
		mysql_query("commit");
		if($this->connection != -1)
			@mysql_close($this->connection);	
	}

	// busca o próximo valor de uma sequence;
	function nextValue($_sequence) {

		$this->saveSnapshot();
		
		// trava a tabela.
		$this->getSql("lock tables sys_sequence write");
		$this->execute();
		
		// altera o valor.
		$this->getSql("update sys_sequence 
					set value=value+1 where name = :sequence");
		$this->bind(":sequence",&$_sequence);
		$this->execute();
		
		// busca o valor.
		$this->getSql("select value 
					from sys_sequence where name = :sequence");
		$this->bind(":sequence", &$_sequence);
		$this->define("value", &$value);
		$this->execute();
		$this->fetch();
		
		// libera o recurso.
		$this->getSql("unlock tables");
		$this->execute();

		$this->loadSnapshot();
		return $value;
	}
}
?>
