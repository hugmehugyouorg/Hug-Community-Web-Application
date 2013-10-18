<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends MY_Controller {

	public function index()
	{
		$d = $this->headerViewData();
		$d['homeLink'] = '/';
		$this->load->view('partials/header', $d);
		$this->load->view('welcome_message');
		$this->load->view('partials/footer');
	}
}