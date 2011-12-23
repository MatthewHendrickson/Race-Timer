<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mymodelx extends CI_Model {
//class Mymodelx {
	protected $_table = "";
//	protected $_columns = array();
	protected $_foreign_fields = '';
	protected $_foreign_joins = '';

	public function __construct()
	{
		parent::__construct();
	}

}
