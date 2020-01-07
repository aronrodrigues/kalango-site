<?php
class Login {
	var $email;
	var $name;
	var $password;
	var $db;
	var $status;
	function Login($_db, $_loginKey) {
		$this->db = $_db;
		$this->email = $_loginKey;
		$this->status = false;
	}
	function loadNamePassword() {
		$db = $this->db;
		$db->saveSnapshot();
		$db->getSql("
			select	name, password
			from	login
			where	email = :email
		");
		$db->bind(":email", $this->email);
		$db->define("name", $name);
		$db->define("password", $password);
		$db->execute();
		$db->fetch();
		$this->name = $name;
		$this->password = $password;
		$db->loadSnapshot();
	}
	function verify($_password = null) {
		if ($this->status === false) {
			$this->loadNamePassword();
			$this->status = (md5($this->email.$_password) == $this->password);
		}
		return $this->status;
	}
	function change($_password, $_name) {
		$ret = false;
		$password = md5($this->email.$_password);
		$db = $this->db;
		$db->saveSnapshot();
		$db->getSql("
			replace into login(email, name, password)
			values(:email, :name, :password)
		");
		$db->bind(":email", $this->email);
		$db->bind(":name", $_name);
		$db->bind(":password", $password);
		$ret = $db->execute();
		$db->loadSnapshot();
		return $ret;
	}
	function delete() {
		$ret = false;
		$db = $this->db;
		$db->saveSnapshot();
		$db->getSql("
			delete from login where email = :email
		");
		$db->bind(":email", $this->email);
		$ret = $db->execute();
		$db->loadSnapshot();
		return $ret;
	}
}
?>
