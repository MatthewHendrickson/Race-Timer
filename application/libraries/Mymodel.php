<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mymodel extends CI_Model {
	protected $_table = "";
	protected $_columns = array();
	protected $_foreign_fields = '';
	protected $_foreign_joins = '';

//	public function __construct()
//	{
//		parent::__construct();
//	}

	public function columns()
	{
		return $this->_columns;
	}

	public function getRows($where = null, $orderby = null)
	{
		$where = '';
		$where_and = '';
		if (is_array($where)) {
			foreach ($where as $item) {
				$where .= $where_and."$item->column $item->op '$item->value'";
				$where_and = ' AND ';
			}
		}
		if(!empty($where)) $where = 'WHERE '.$where;
		$sql = "SELECT x.* $this->_foreign_fields
			FROM $this->_table AS x $this->_foreign_joins
			$where";
		$query = $this->db->query($sql);
		return $query->result();
	}

	public function getRow($id)
	{
		$sql = "SELECT x.* $this->_foreign_fields
			FROM $this->_table AS x $this->_foreign_joins
			WHERE x.id = ?";
		$query = $this->db->query($sql, $id);
		if ($query->num_rows() < 1) return false;
		return $query->row();
	}

	public function updateRow($id, $data)
	{
		$this->db->where('id', $id);
		$result = $this->db->update($this->_table, $data);
		return $result;
	}

	public function deleteRow($id)
	{
		$this->db->where('id', $id);
		$result = $this->db->delete($this->_table);
		return $result;
	}

	public function addRow($data)
	{
		if (!$this->db->insert($this->_table, $data)) return false;
		$id = $this->db->insert_id();
		return $id;
	}
}
