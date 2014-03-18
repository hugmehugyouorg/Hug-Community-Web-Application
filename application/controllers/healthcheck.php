<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class HealthCheck extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->model('Companion_model');
		$error = $this->Companion_model->curfew_check();
		if($error != '')
			log_message('error', "Error: ".$error);
		$this->output->set_output("UP");
	}
	
}