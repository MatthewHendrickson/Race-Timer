<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
CREATE TABLE IF NOT EXISTS `racers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `idrace` mediumint(9) unsigned NOT NULL,
  `idperson` mediumint(9) unsigned NOT NULL,
  `bibno` smallint(5) unsigned NOT NULL,
  `category` varchar(10) NOT NULL,
  `idadd` mediumint(9) NOT NULL,
  `dateadd` datetime NOT NULL,
  `idmod` mediumint(9) default NULL,
  `datemod` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
 */
class Racers extends Mymodel {
	function __construct() {
		$this->_table = 'racers';
		$this->_columns = array(
			'bibno'=>(object) array('column'=>'bibno', 'type'=>'int'),
			'category'=>(object) array('column'=>'category', 'type'=>'varchar'),
			'idrace'=>(object) array('column'=>'idrace', 'type'=>'foreign', 'table'=>'race')
		);
		parent::__construct();
	}

	/* function getItem($id) - see BaseModel */

	/* function getListing($filter, $find=null, $orderby = 'name', $orderbydirection = 'asc') - see BaseModel */

	/* function updateItem($id, $data, $modby = true) - see BaseModel */

	/* function addItem($data) - see BaseModel */

	/* function getOptions($all = false) - see BaseModel */

	/* function getCountAll() - see BaseModel */

	function getBib($raceId, $bib)
	{
		$sql = "SELECT *
			FROM racers
			WHERE idrace = ? AND bib = ?";
		$query = $this->db->query($sql, $raceId, $bib);
		if ($query->num_rows() < 1) return false;
		return $query->row();
	}
}
?>
