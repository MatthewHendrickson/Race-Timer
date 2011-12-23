<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
CREATE TABLE IF NOT EXISTS `races` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(200) NOT NULL,
  `eventdate` date default NULL,
  `starttime` datetime default NULL,
  `racersperstart` smallint(5) unsigned default NULL,
  `startinterval` smallint(5) unsigned default NULL,
  `startbib` smallint(5) unsigned default NULL,
  `idadd` mediumint(8) unsigned NOT NULL,
  `dateadd` datetime NOT NULL,
  `idmod` mediumint(8) unsigned default NULL,
  `datemod` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
 */
class Races extends Mymodel {

	function __construct() {
		$this->_table = 'races';
		$this->_columns = array(
			'name'=>(object) array('column'=>'name', 'type'=>'varchar'),
			'eventdate'=>(object) array('column'=>'eventdate', 'type'=>'date'),
			'starttime'=>(object) array('column'=>'starttime', 'type'=>'datetime'),
			'racersperstart'=>(object) array('column'=>'racersperstart', 'type'=>'int'),
			'startinterval'=>(object) array('column'=>'startinterval', 'type'=>'int'),
			'startbib'=>(object) array('column'=>'startbib', 'type'=>'int')
		);
		parent::__construct();
	}

	/* function getRow($id) - see BaseModel */

	/* function rows($filter, $find=null, $orderby = 'name', $orderbydirection = 'asc') - see BaseModel */

	/* function updateRow($id, $data, $modby = true) - see BaseModel */

	/* function addRow($data) - see BaseModel */
}
?>
