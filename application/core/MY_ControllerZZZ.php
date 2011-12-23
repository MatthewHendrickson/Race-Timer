<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
	protected $_cname = ''; // Controller name
	protected $_ctitle = ''; // Controller display name, defaults to controller name with first letter capitolized
	protected $_mname = ''; // Model name, defaults to controller name with 's' appended
	protected $_fields = array();
	protected $_edit_fields = array();
//	protected $_edit_method = 'edit'; // Default method to edit an item
//	protected $_add_method = 'add'; // Default method to add an item
//	protected $_listing_method = 'listing'; // Default method for listing items

	function __construct()
	{
		parent::__construct();
die('MY_Controller constructor');
		// Initial some data
		if ($this->_mname == '') $this->_name = $this->_cname . 's';
		if ($this->_ctitle == '') $this->_ctitle = strtoupper(substr($this->_cname, 0, 1)) . substr($this->_cname, 1);

		// CodeIgniter stuff
		$this->load->library('session');
		$this->load->database();
		$this->load->helper('url');

		// Pretty sure we will need this model
		$this->load->library('model');
		$this->load->library('mymodel');
		$this->load->model($this->_mname, 'MODEL');
	}

	function index()
	{
		$this->listing();
	}

	function listing($where = null, $orderby = null)
	{
		$list = $this->MODEL->rows();

		// Who are we? (and what are we doing here?)
		$this->_view_data['cTitle'] = $this->_ctitle;
		$this->_view_data['cName'] = $this->_cname;
		$this->_view_data['modelName'] = $this->_model_name;

		// end of list-related code

		// Show the view
		$this->load->view($this->_listing_view, $this->_view_data);
	}

	function add()
	{
		$this->edit(0);
	}

	function edit($id)
	{
		// Build validation fields
		$this->load->helper('my_field');
		$ff = new field_list();
		$this->_set_edit_fields($ff);

		// Post Back
		if(!empty($_POST)) {
			$this->load->library('form_validation');

			foreach($this->_edit_fields as $field) {
				$this->form_validation->set_rules($field->name, $field->label, $field->rules);
			}

			if ($this->form_validation->run() == FALSE) {
				// Data OK
				foreach($this->_edit_fields as $field) {
					$name = $field->name;
					$value = $this->input->post($field->name);
					if($value !== false) $data->$name = $value;
				}
//echo '<pre>'.print_r($data, true).'</pre>';
log_message('debug', 'edit: data = '.print_r($data, true));
//exit;
				foreach ($ff->fields as $key => $field) {
					if ($field[3] == 'bool' && $data->$key == null) $data->$key = 0;
					if (isset($field['readonly'])) unset($data->$key);
				}
log_message('debug', 'edit (after removal): data = '.print_r($data, true));
				if($id) { // Update the Item
					$this->_edit_item($id, $data, $ff);
				} else { // Add the Item
					$id = $this->_add_item($data) or terminate('failed to add item', __FILE__, __LINE__, __FUNCTION__, __CLASS__, __METHOD__);
					$this->session->set_flashdata('flashmsg', "$this->_ctitle has been added.");
				}

				// Return to "view"
				redirect(historyUri());
				return;
			} else {
				// Validation Failed
				$this->view->set('flashmsg', $this->validation->error_string);
			}

			// Get the current record
			if ($id > 0) {
//echo "<pre>Getting the current record, id = $id</pre>\n";
				$data = $ff->get_row($this->_get_item($id), $this->validation);
//echo "<pre>Got it: ".print_r($data, true)."</pre>\n";
				$data->id = $id;
			} else {
				$data = $ff->get_row();
				$data->id = 0;
			}

		// If we got an id then edit this id
		} elseif($id) {
			// Edit the record
			$data = $this->_get_item($id);
			if(!$data) {
				$this->session->set_flashdata('flashmsg', "$this->_ctitle was not found.");
				redirect($this->_cname.'/listing');
				return;
			}
			foreach ($ff->fields as $key => $field) {
				if ($field[3] == 'date') $data->$key = $this->_date_format($data->$key);
				if (isset($field['readonly']) && !isset($data->$key)) $data->$key = $field[2];
			}
			$data->id = $id;

		} else {
			// accessing form to add a new record
			foreach($this->_fields as $field) {
				$data->{$field->column} = null;
			}
			$data->id = 0;
		}

		// Pass data to view
		$this->view->set('item', $data);
		$this->view->set('fields', $this->_fields);
		$this->view->set('cTitle', $this->_ctitle);
		$this->view->set('cName', $this->_cname);
//		$this->view->set('edit_method', $this->_edit_method);
//		$this->view->set('add_method', $this->_add_method);
//		$this->view->set('find_method', $this->_find_method);
//		$this->view->set('listing_method', $this->_listing_method);

		// Set id in case of feeedback
//		$this->session->set_userdata($this->_cname.'_id', $data->id);

		// Show the view
		$this->view->load($this->_cname.'/edit');
//		$this->view->load($this->_edit_view);
	}

	protected function _edit_item($id, $data, $ff) {
		$prev = $this->MODEL->getItem($id);
log_message('debug', 'edit: prev = '.print_r($prev, true));
		if($changes = $ff->changes($prev, $data)) {
log_message('debug', 'edit: changes = '.print_r($changes, true));
			$this->MODEL->changeItem($id, $changes) or terminate('failed to update item', __FILE__, __LINE__, __FUNCTION__, __CLASS__, __METHOD__);
			$this->session->set_flashdata('flashmsg', "$this->_ctitle has been updated.");
		}
	}

	protected function _get_item($id) {
		return $this->MODEL->getItem($id);
	}

	protected function _add_item($data) {
		return $this->MODEL->addItem($data);
	}

//	public function delete($id) {
//		return $this->STD_MODEL->deleteItem($id);
//	}

	abstract protected function _set_find_fields($ff);

	function find() {
		$this->load->helper('my_field');
		$ff = new field_list();
		$this->_set_find_fields($ff);
//		$ff->fields['name'] = array('Discipline\'s Name', 'trim', '', '');
//		$ff->fields['idperfver'] = array('Survey Version', 'trim', '', 'int');
//		$ff->fields['isactive'] = array('Status', 'trim', '', 'int');

		if(!empty($_POST)) {
			// use form validator to prep find criteria
			$this->load->library('validation');

log_message('debug', "find (before validation): ".print_r($_POST, true));
			$this->validation->set_rules($ff->get_rules());
			$this->validation->set_fields($ff->get_fields());
			$this->validation->run();

			$find = $ff->get_row(null, $this->validation);

log_message('debug', "find (before filter): ".print_r($find, true));
			foreach($find as $k => $v) {
				if(empty($v) && ($v!==0))
					unset($find->$k);
			}

log_message('debug', "find (after filter): ".print_r($find, true));
			$this->session->set_userdata($this->_cname.'_find', $find);
			redirect($this->_cname.'/'.$this->_listing_method.'/find');
			return;
		} else {
			// accessing form to enter find criteria
			$find = $ff->get_row();
			if($saved_find = $this->session->userdata($this->_cname.'_find')) {
				foreach($saved_find as $k => $v)
					$find->$k = $v;
			}
		}

		// Pass data to view
		$this->view->set('find', $find);
		$this->view->set('fields', $ff->fields);
		$this->view->set('cTitle', $this->_ctitle);
		$this->view->set('cName', $this->_cname);
		$this->view->set('edit_method', $this->_edit_method);
		$this->view->set('add_method', $this->_add_method);
		$this->view->set('find_method', $this->_find_method);
		$this->view->set('listing_method', $this->_listing_method);

		// Show the view
//		$this->view->load($this->_cname.'/find');
		$this->view->load($this->_find_view);
	}

	private function _date_format($db_str) {
		if (empty($db_str)) return NULL;
		return date('n/j/Y', strtotime($db_str));
	}

	public function import() {
//		$this->view->set('flashmsg', '');
		if(!empty($_FILES)) {
			$config['upload_path'] = APPPATH.'/uploads/';
			$config['allowed_types'] = 'txt';
			$this->load->library('upload', $config);
			if($this->upload->do_upload())
			{
				$data = $this->upload->data();
				$output = $this->_do_import($data['full_path']);
				$this->view->set('output', $output);
				$this->view->load('imported');

				return;
			}
			$this->view->set('flashmsg', $this->upload->display_errors());
		}
		$this->view->set('title', $this->_ctitle.' Import');
		$this->view->set('action', site_url($this->_cname.'/import'));
		$this->view->load('import');
	}
}
