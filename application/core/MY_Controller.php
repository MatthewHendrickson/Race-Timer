<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	protected $_access = 0;
	protected $_cname = ''; // Controller name
	protected $_ctitle = ''; // Controller display name, defaults to controller name with first letter capitolized
	protected $_mname = ''; // Model name, defaults to controller name with 's' appended
	protected $_fields = array();
//	protected $_edit_fields = array();
	protected $_iduser = 0;
	protected $_user = null;
	protected $_data = array(); // Gets passed to the view

	function __construct()
	{
		parent::__construct();
		// Initial some data
		if (empty($this->_mname)) $this->_mname = $this->_cname . 's';
		if (empty($this->_ctitle)) $this->_ctitle = strtoupper(substr($this->_cname, 0, 1)) . substr($this->_cname, 1);

		// This is from system/core/Loader.php
		if ( ! class_exists('CI_Model'))
		{
			load_class('Model', 'core');
		}

		$this->load->library('mymodel');
		$this->load->model('users', 'users');
		if ($this->_mname != 'users') $this->load->model($this->_mname, 'MODEL');
		else $this->MODEL = $this->users;

		// Access control
		if ($this->_access) {
			$id = $this->session->userdata('iduser');
			try {
				$message = 'You must be logged in to access this function';
				if ($id === FALSE) {
					throw new Exception($message);
				}
				$user = $this->users->getRow($id);
				if ($user === FALSE) {
					throw new Exception($message);
				}
			}
			catch (Exception $ex) {
				$this->session->set_flashdata('message', $ex->getMessage());
				redirect('user/login');
			}
			$this->_iduser = $id;
			$this->_user = $user;
		}

		$this->_fields = &$this->MODEL->columns();
//foreach($this->_fields as $col) echo "<pre>col: ".print_r($col, true)."</pre>\n";
		$message = $this->session->flashdata('message');
		if ($message) $this->_data['message'] = $message;
		$this->_data['method'] = $this->uri->segment(2);
		$this->_data['class'] = $this->uri->segment(1);
	}

	public function index()
	{
		$this->_data['method'] = $this->uri->segment('rows');
		$this->rows();
	}

	protected function _access($roles)
	{
		if (($roles & $user->roles) == $roles) return TRUE;
		return FALSE;
	}

	public function rows()
	{
		$data = &$this->_data;
		$data['rows'] = $this->MODEL->getRows();
echo "<pre>".print_r($data['rows'], TRUE)."</pre>\n";
		// Show the view
		$this->_rows_view($data);
	}

	protected function _rows_view($data)
	{
		if (file_exists(APPPATH.'views/'.$this->_cname.'/header'.EXT)) $this->load->view($this->_cname.'/header', $data);
		else $this->load->view('header', $data);
		if (file_exists(APPPATH.'views/'.$this->_cname.'/rows'.EXT)) $this->load->view($this->_cname.'/rows', $data);
		else $this->load->view('rows', $data);
		if (file_exists(APPPATH.'views/'.$this->_cname.'/footer'.EXT)) $this->load->view($this->_cname.'/footer', $data);
		else $this->load->view('footer', $data);
	}

	public function add()
	{
		$this->edit(0);
	}

	public function edit($id = 0)
	{
		$data = &$this->_data;
		// Post Back
		if(!empty($_POST)) {
			$this->load->library('form_validation');

			foreach($this->_fields as $name=>$field) {
				if (strpos($field->access, 'E') !== FALSE) $this->form_validation->set_rules($name, $field->label, $field->rules);
			}

			if ($this->form_validation->run() != FALSE) {
				// Data OK
				$item = new stdClass();
//foreach($this->_fields as $name=>$col) echo "<pre>name: $name, col: ".print_r($col, true)."</pre>\n";
				foreach($this->_fields as $name=>$field) {
					$value = $this->input->post($name);
//echo "<pre>post value: $value</pre>\n";
					if($value !== false) $item->$name = $value;
				}
//echo '<pre>item: '.print_r($item, true).'</pre>';

log_message('debug', 'edit: data = '.print_r($item, true));
				if($id) { // Update the Item
					$this->_edit_item($id, $item);
				} else { // Add the Item
					$id = $this->_add_item($item);
				}

				// Return to "view"
//$uri = $this->_cname.'/view/'.$id;
//echo "<pre>redirect($uri)</pre>";
				redirect($this->_cname.'/view/'.$id);
				return;
			} else {
				// Validation Failed
echo "<pre>Validation Failed</pre>\n";
echo "<pre>".validation_errors()."</pre>\n";
				return;
			}
		// If we got an id then edit this id
		} elseif($id) {
			// Edit the record
			$item = $this->_get_item($id);
			if(!$item) {
echo "<pre>Could not find item: $id</pre>\n";
//				redirect($this->_cname.'/listing');
				return;
			}
//			$item->id = $id;
		} else {
			// accessing form to add a new record
			foreach($this->_fields as $name=>$field) {
				$item->$name = null;
			}
			$item->id = 0;
		}

		$data['item'] = $item;
		$data['header_title'] = ($item->id ? 'Edit' : 'Create').' '.$this->_ctitle;
		// Pass data to view
		$data['fields'] = $this->_fields;
		$data['title'] = $this->_ctitle;
		$data['cName'] = $this->_cname;
		$data['id'] = $id;

		// Show the view
		$this->_edit_view($data);
	}

	protected function _edit_view($data)
	{
		$this->load->helper('form');
//		$this->load->view('header', $data);
//		$this->load->view('edit', $data);
//		$this->load->view('footer', $data);
		if (file_exists(APPPATH.'views/'.$this->_cname.'/header'.EXT)) $this->load->view($this->_cname.'/header', $data);
		else $this->load->view('header', $data);
		if (file_exists(APPPATH.'views/'.$this->_cname.'/edit'.EXT)) $this->load->view($this->_cname.'/edit', $data);
		else $this->load->view('edit', $data);
		if (file_exists(APPPATH.'views/'.$this->_cname.'/footer'.EXT)) $this->load->view($this->_cname.'/footer', $data);
		else $this->load->view('footer', $data);
	}

	protected function _get_item($id)
	{
		return $this->MODEL->getRow($id);
	}

	protected function _edit_item($id, $data)
	{
		$this->_modby($data);
		$this->MODEL->updateRow($id, $data);
	}

	protected function _add_item($data)
	{
		$this->_addby($data);
		return $this->MODEL->addRow($data);
	}

	protected function _addby($data)
	{
		$data->idadd = $this->_iduser;
		$data->dateadd = date('Y-m-d G:i:s');
	}

	protected function _modby($data)
	{
		$data->idmod = 1;
		$data->datemod = date('Y-m-d G:i:s');
	}

	public function view($id)
	{
		$data = &$this->_data;
		$item = $this->MODEL->getRow($id);
//echo "<pre>row: ".print_r($item, true)."</pre>\n";
		$data['item'] = $item;
		// Pass data to view
		$data['header_title'] = $this->_ctitle;
		$data['fields'] = $this->_fields;
		$data['title'] = $this->_ctitle;
		$data['cName'] = $this->_cname;
		$data['id'] = $id;

		// Show the view
		$this->_view($data);
	}

	protected function _view($data)
	{
//echo "<pre>in _view: data = ".print_r($data, true)."</pre>\n";
//		$this->load->view('header', $data);
//		$this->load->view('view', $data);
//		$this->load->view('footer', $data);
		if (file_exists(APPPATH.'views/'.$this->_cname.'/header'.EXT)) $this->load->view($this->_cname.'/header', $data);
		else $this->load->view('header', $data);
		if (file_exists(APPPATH.'views/'.$this->_cname.'/view'.EXT)) $this->load->view($this->_cname.'/view', $data);
		else $this->load->view('view', $data);
		if (file_exists(APPPATH.'views/'.$this->_cname.'/footer'.EXT)) $this->load->view($this->_cname.'/footer', $data);
		else $this->load->view('footer', $data);
	}
}
?>