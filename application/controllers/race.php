<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Race extends MY_Controller {

	function  __construct()
	{
		$this->_cname = 'race';
		$this->_access = 1;

		parent::__construct();

		// Field titles
		$this->_fields['name']->label = 'Event Name';
		$this->_fields['name']->access = 'EVL';
		$this->_fields['name']->rules = 'required';

		$this->_fields['eventdate']->label = 'Event Date';
		$this->_fields['eventdate']->access = 'EVL';
		$this->_fields['eventdate']->rules = 'required';

		$this->_fields['starttime']->label = 'Start Time';
		$this->_fields['starttime']->access = 'V';
		$this->_fields['starttime']->rules = 'required';

		$this->_fields['racersperstart']->label = 'Racers per Start';
		$this->_fields['racersperstart']->access = 'EV';
		$this->_fields['racersperstart']->rules = 'required|integer';

		$this->_fields['startinterval']->label = 'Start Interval';
		$this->_fields['startinterval']->access = 'EV';
		$this->_fields['startinterval']->rules = 'required|integer';

		$this->_fields['startbib']->label = 'Starting Bib';
		$this->_fields['startbib']->access = 'EV';
		$this->_fields['startbib']->rules = 'required|integer';
	}

	function main()
	{

	}

	public function rows()
	{
		$data = &$this->_data;
		$data['rows'] = $this->MODEL->getRows(array((object) array('column'=>'iduser', 'value'=>$this->_iduser, 'op'=>'=')));
//echo "<pre>".print_r($data['rows'], TRUE)."</pre>\n";
		$data['header_title'] = 'Races';
		$data['title'] = 'Races';
		$data['fields'] = $this->_fields;
		$data['fields']['racers'] = (object) array('text'=>'Racers', 'location'=>site_url('racer/rows/'), 'access'=>'L', 'type'=>'button', 'label'=>'Racers');
		// Show the view
		$this->_rows_view($data);
	}

//	function _view($data)
//	{
//		$this->load->view('race/view', $data);
//	}

	function upload()
	{
		$race_json = $this->input->post('race');
		$race = json_decode($race_json);
log_message('info', 'race = '.print_r($race, TRUE));
		$results = $race->results;
		unset($race->results);
		$race->racedate = date('Y-m-d', strtotime($race->racedate));
		$race->startsize = $race->racers_start; unset($race->racers_start);
		$race->startinterval = $race->start_interval; unset($race->start_interval);
		$race->startbib = $race->start_bib; unset($race->start_bib);
// ==>> We lose the msecs of the start time here!!!
		$race->starttime = date('Y-m-d H:i:s', intval($race->race_start/1000)); unset($race->race_start);
		unset($race->fastest_time);

		$race_db = $this->STD_MODEL->getItem($race->id);
		if ($race_db === FALSE) {
log_message('info', 'Creating a new race record');
			$id = $this->STD_MODEL->addItem($race);
		} else {
log_message('info', 'Updating a race record');
			$id = $race->id; unset($race->id);
			$this->STD_MODEL->updateItem($id, $race);
		}

		$result = (object) array('status'=>'success', 'id'=>$id);
		$json = json_encode($result);
		header('Content-type: application/json'); // This works
		echo $json;
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */