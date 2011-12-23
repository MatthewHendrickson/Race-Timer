<?php
/*
 */
class Basemodel extends Model {
	protected $table = "";
	protected $isactive = true;
	protected $sortable = false;
	protected $filter = array('all'=>'1=1');
	protected $filter_list = array('all'=>'All Records');
	protected $foreign = array();
	protected $foreign_fields = '';
	protected $foreign_joins = '';
	protected $default_order_by = 'name';
	protected $find_fields = array();
	protected $listing_sql = null;

	public function __construct() {
		parent::Model();
		if ($this->isactive) {
			$this->filter['active'] = '(r.isactive = 1)';
			$this->filter['inactive'] = '(r.isactive = 0 or r.isactive is null)';
			$this->filter_list['active'] = 'Active Records';
			$this->filter_list['inactive'] = 'Inactive Records';

			$this->find_fields['isactive'] = array('r.isactive', 'BOOL');
		} else {
			$this->filter['active'] = '1=1';
		}
	}

	public function getItem($id) {
		$sql = "
			SELECT r.* $this->foreign_fields
			FROM $this->table as r $this->foreign_joins
			where r.id = ?";
		$query = $this->db->query($sql, $id);
		if ($query->num_rows() < 1) return false;
		return $query->row();
	}

	public function getListing($filter, $find = null, $orderby = false, $orderbydirection = 'asc') {
log_message('debug', 'Basemodel->getListing(): filter: '.print_r($filter, true).', find: '.print_r($find, true).', orderby: '.print_r($orderby, true));
		if ($orderby === false) $orderby = $this->default_order_by;
		$where_values = array();

		if (isset($this->filter[$filter])) { $where = $this->filter[$filter]; }
		else if ($filter == 'find') {
			if(count((array) $find)) {
				$where = '';
				foreach($find as $k => $v) {
					// This allows multiple find rows with the same key
					if (is_object($v)) {
						$k = $v->key;
						$v = $v->value;
					}
					if(!empty($where))
						$where .= " and ";

					if(array_key_exists($k, $this->find_fields)) {
						if(($this->find_fields[$k][1] == 'STR') && empty($this->find_fields[$k][2])) {
							$where .= "{$this->find_fields[$k][0]} like ?";
							$v = "%$v%";
						} else { // Assume 'INT'
							$c = empty($this->find_fields[$k][2]) ? '=' : $this->find_fields[$k][2];
							$where .= "{$this->find_fields[$k][0]} $c ?";
						}
					} else {
						// Just assume STR
						$where .= "r.$k like ?";
						$v = "%$v%";
					}
					$where_values[] = $v;
				}
			} else {
				$where = '1=1';
			}
		} else { terminate('invalid filter = '.$filter, __FILE__, __LINE__, __FUNCTION__, __CLASS__, __METHOD__); }

		$orderbydirection == 'asc' or $orderbydirection = 'desc';
		$orderbyreverse = $orderbydirection == 'asc' ? 'desc' : 'asc';
		$foreign = $this->foreign;
		$orderby = str_replace('<dir>', $orderbydirection, str_replace('<rdir>', $orderbyreverse, !empty($foreign[$orderby]) ? $foreign[$orderby] : 'r.'.$orderby.' <dir>'));

		if (empty($this->listing_sql)) {
			$sql = "
				SELECT r.* $this->foreign_fields
				FROM $this->table as r $this->foreign_joins
				WHERE $where
				ORDER BY $orderby";
		} else {
			$sql = $this->listing_sql;
		}

		$query = $this->db->query($sql, $where_values);

		if($query)
			return $query->result();
		else
			return array();
	}

	public function getFilterList($hasfind = false)
	{
		if($hasfind)
			$this->filter_list['find'] = "Search Results";
		return $this->filter_list;
	}

	function changeItem($id, $changes) {
//log_message('debug', 'Basemodel->changeItem(): changes: '.print_r($changes, true));
		$data = array();
		foreach($changes as $k=>$c)
			$data[$k] = $c[1];
		$this->modby($data, IDUSER);
		$this->db->where('id', $id);
		$result = $this->db->update($this->table, $data);
		if ($this->sortable) $this->updateSortOrder();
		return $result;
	}

	public function updateItem($id, $data, $modby = true) {
//		if($modby)
//			$this->modby($data, IDUSER);
		$this->db->where('id', $id);
		$result = $this->db->update($this->table, $data);
		if ($this->sortable) $this->updateSortOrder();
		return $result;
	}

	public function deleteItem($id) {
		$this->db->where('id', $id);
		$result = $this->db->delete($this->table);
		return $result;
	}

	public function addItem($data) {
//		$this->addby($data, IDUSER);
		if (!$this->db->insert($this->table, $data))
			return false;
		$result = $this->db->insert_id();
		if ($this->sortable) $this->updateSortOrder();
		return $result;
	}

	function updateSortOrder() {
		if ($this->isactive) $where = 'WHERE isactive = 1';
		else $where = '';
		$sql = "
			SELECT id
				, sortorder
			FROM $this->table
			$where
			ORDER BY sortorder";

		if($query = $this->db->query($sql)) {
			$i = 2;
			foreach ($query->result() as $row) {
				if($row->sortorder != $i) {
					$this->db->where('id', $row->id);
					$this->db->update($this->table, array('sortorder'=>$i));
				}
				$i++;
				$i++;
			}
		}
	}

	public function getOptions($all = false) {
		if(!$all && $this->isactive)
			$this->db->where('isactive', 1);
		$this->db->orderby($this->default_order_by, "asc");
		$query = $this->db->get($this->table);
		$opt = array();
		$opt[''] = '';
		$column = $this->default_order_by;
		foreach ($query->result() as $row) {
			$opt[$row->id] = $row->$column;
		}
		return $opt;
	}

	public function getCountAll() {
		return $this->db->count_all($this->table);
	}
}
