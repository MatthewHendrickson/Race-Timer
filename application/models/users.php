<?php
/*
CREATE TABLE IF NOT EXISTS `users` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` char(32) NOT NULL,
  `salt` char(32) NOT NULL,
  `idadd` mediumint(8) unsigned NOT NULL,
  `dateadd` datetime NOT NULL,
  `idmod` mediumint(8) unsigned default NULL,
  `datemod` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
*/
define('USER_BASIC', 1);

class Users extends Mymodel {

	function __construct() {
		$this->_table = 'users';
		$this->_columns = array(
			'username'=>(object) array('column'=>'username', 'type'=>'varchar'),
			'email'=>(object) array('column'=>'email', 'type'=>'varchar'),
			'password'=>(object) array('column'=>'password', 'type'=>'varchar'),
			'salt'=>(object) array('column'=>'salt', 'type'=>'varchar'),
		);
		parent::__construct();
	}

	function getByEmail($email)
	{
		$sql = "SELECT x.*
			FROM users AS x
			WHERE x.email = ?";
		$query = $this->db->query($sql, $email);
		if ($query->num_rows() < 1) return false;
		return $query->row();
	}

	function getByUsername($username)
	{
		$sql = "SELECT x.*
			FROM users AS x
			WHERE x.username = ?";
		$query = $this->db->query($sql, $username);
		if ($query->num_rows() < 1) return false;
		return $query->row();
	}
	/* function getRow($id) - see BaseModel */

	/* function getRows($filter, $find=null, $orderby = 'name', $orderbydirection = 'asc') - see BaseModel */

	/* function updateRow($id, $data, $modby = true) - see BaseModel */

	/* function addRow($data) - see BaseModel */
}
?>
