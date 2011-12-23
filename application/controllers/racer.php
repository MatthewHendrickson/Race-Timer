<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Racer extends MY_Controller {

	function  __construct()
	{
		$this->_cname = 'racer';
		$this->_access = 1;

		parent::__construct();

		// Field titles
		$this->_fields['bibno']->label = 'Bib Number';
		$this->_fields['bibno']->access = 'EVL';
		$this->_fields['bibno']->rules = 'required';

		$this->_fields['category']->label = 'Category';
		$this->_fields['category']->access = 'EVL';
		$this->_fields['category']->rules = 'required';

		$this->_fields['idrace']->label = 'Race';
		$this->_fields['idrace']->access = 'EV';
		$this->_fields['idrace']->rules = 'required';
	}

	function index()
	{
		$this->load->view('welcome_message');
	}

	public function rows($idrace)
	{
		$data = &$this->_data;
		$data['rows'] = $this->MODEL->getRows(array((object) array('column'=>'iduser', 'value'=>$this->_iduser, 'op'=>'='),
							(object) array('column'=>'idrace', 'value'=>$idrace, 'op'=>'=')));
//echo "<pre>".print_r($data['rows'], TRUE)."</pre>\n";
		$data['header_title'] = 'Racers';
		$data['title'] = 'Racers';
		$data['fields'] = $this->_fields;
		$data['idrace'] = $idrace;
//		$data['fields']['racers'] = (object) array('text'=>'Racers', 'location'=>site_url('racer/rows/'), 'access'=>'L', 'type'=>'button', 'label'=>'Racers');
		// Show the view
		$this->_rows_view($data);
	}

	public function add($idrace)
	{
		$data = &$this->_data;
		$data['idrace'] = $idrace;
		parent::edit(0);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */