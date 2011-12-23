<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller {

	function  __construct()
	{
		$this->_cname = 'user';

		parent::__construct();

		// Field titles
		$this->_fields['username']->label = 'User Name';
		$this->_fields['username']->access = 'EVL';
		$this->_fields['username']->rules = 'required';

		$this->_fields['email']->label = 'User Name';
		$this->_fields['email']->access = 'EVL';
		$this->_fields['email']->rules = 'required|valid_email';
	}

	public function login()
	{
		$data = &$this->_data;
		// Post Back
		if (!empty($_POST)) {
			$this->load->library('form_validation');

			$this->form_validation->set_rules('email', 'Email Address', 'required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'required');

			if ($this->form_validation->run() != FALSE) {
				$email = $this->input->post('email');
				$password = $this->input->post('password');
//echo "<pre>ready to check password for $email</pre>\n";
				$user = $this->MODEL->getByEmail($email);
				if ($user && $user->password === md5($password.$user->salt)) {
echo "<pre>Logged in</pre>\n";
					$this->session->set_userdata('iduser', $user->id);
					return;					
				}
echo "<pre>user->password: $user->password, md5: ".md5($password.$user->salt)."</pre>\n";
//				$data['email'] = $email;
				$data['message'] = 'Invalid email or password';
			} else {
//				$data['email'] = $this->input->post('email') ? $this->input->post('email') : '';
				$data['message'] = validation_errors();
			}
//		} else {
//			$data['email'] = '';
		}

		$data['title'] = 'Login';
		$data['header_title'] = 'Login';
		$this->load->helper('form');
		$this->load->view('header', $data);
		$this->load->view('user/login', $data);
	}

	public function logout($uri = null)
	{
		$this->session->unset_userdata('iduser');
		if ($uri) redirect($uri);
		redirect('user/login');
	}

	public function add()
	{
		$data = &$this->_data;
		// Post Back
		if (!empty($_POST)) {
			$this->load->library('form_validation');

			$this->form_validation->set_rules('username', 'User Name', 'trim|required');
			$this->form_validation->set_rules('email', 'Email Address', 'trim|required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('password2', 'Repeat Password', 'required|matches[password]');

			if ($this->form_validation->run() != FALSE) {
				try {
					$data['username'] = $user->username = $this->input->post('username');
					$data['email'] = $user->email = $this->input->post('email');
					$user->password = $this->input->post('password');
					$user->salt = md5($user->email.date('Gisu'));
					$user->password = md5($user->password.$user->salt);
					$user->roles = USER_BASIC;
					if ($this->MODEL->getByUsername($user->username) !== FALSE) {
						throw new Exception('That username is already taken; please choose another.');
					}
					if ($this->MODEL->getByEmail($user->email) !== FALSE) {
						throw new Exception('That username is already taken; please choose another.');
					}
					$this->_addby($user);
					$id = $this->MODEL->addRow($user);
echo "<pre>Added account for $user->username</pre>\n";
					return;
				}
				catch (Exception $ex) {
					$data['message'] = $ex->getMessage();
					$this->load->view('header', $data);
					$this->load->view('user/login', $data);
					return;
				}
			} else {
				$data['message'] = validation_errors();
			}
//		} else {
//			$data['username'] = '';
//			$data['email'] = '';
		}
		$data['title'] = 'Create New Account';
		$data['header_title'] = 'Create New Account';
		$this->load->helper('form');
		$this->load->view('header', $data);
		$this->load->view('user/add', $data);
	}
}