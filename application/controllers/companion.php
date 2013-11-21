<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Companion extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		
		/*$this->load->config('ion_auth', TRUE);
		$this->lang->load('welcome');
		$this->load->helper('url');
		$this->load->library('email');
		
		$email_config = $this->config->item('email_config', 'ion_auth');
	
		if ($this->config->item('use_ci_email', 'ion_auth') && isset($email_config) && is_array($email_config))
		{
			$this->email->initialize($email_config);
		}*/
	}
	
	public function test()
	{
		$id = $this->input->get('i', TRUE);
		$data = $this->input->get('d', TRUE);
		
		$this->load->model('Companion_model');
		$this->Companion_model->updateCompanionState($id, $data, true);
		
		echo '<br/>success!';
	}

	public function index()
	{
		$error = false;
		$output = '';
		$id = $this->input->get('i', TRUE);
		$data = $this->input->get('d', TRUE);
		
		if( $id === FALSE || $data === FALSE || ctype_alnum($id) === FALSE ) {
			$error = 'either the id is not specified or is not alphanumeric, or the data is not specified.';
		}
		else {
			//make sure it's a hex string, warning thrown if not so handle by calling
			//a handler that throws an error instead
			set_error_handler(array($this, "hexToBinHandler"), E_WARNING);
			try {
				$hexData = hex2bin($data);
				if(!$hexData)
					$error = $id.": ".$data." could not be converted to binary data using hex2bin()";
				else {
					//convert hex string to binary string (ASCII)
					$data = base_convert($data, 16, 2);
					//should be a sequence of bytes, so if not divisble by 8
					//then we need to pad in front cause that's the one losing info
					$dataLen = strlen($data);
					$data = str_repeat('0', 8 - $dataLen % 8) . $data;
					//now chunk them bytes and reverse each cause data came in MSB first
					//but we need LSB first before putting them back as a binary string
					//to return
					$chunks = str_split($data,8);
					$chunksLen =  count($chunks);
					for( $i=0; $i < $chunksLen; $i++ ) {
							$chunks[$i] = strrev($chunks[$i]);
					}
					$data = implode("",$chunks);
					
					$this->load->model('Companion_model');
					$output = $this->Companion_model->updateCompanionState($id, $data);
				}
			}
			catch(Exception $e) {
				$error = $e->getMessage();
			}
			restore_error_handler();
 		}
 		
 		ob_clean();
 		
 		if($error)
 		{
 			header('HTTP/1.1 207 '.trim(preg_replace('/\s+/', ' ', $error)));//header('HTTP/1.0 444 No Response');
 			//echo $error;
 		}
 		else
 		{
 			header('HTTP/1.1 207 '.trim(preg_replace('/\s+/', ' ', $output)));
 			//echo $output;
 		}
 		die();
 		
 		//need to get the user
 		/*$user = new stdClass();
 		$user->email = 'awelters@hugmehugyou.org';
 		
 		$data = array(
			'identity'		=> 'awelters@hugmehugyou.org',
			'emergency_response_code' => '12345678'
		);
		
		$message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_emergency_alert', 'ion_auth'), $data, true);
		$this->email->clear();
		$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
		$this->email->to($user->email);
		$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_emergency_alert_subject'));
		$this->email->message($message);

		if ($this->email->send())
		{
			//success
			header('HTTP/1.1 207 THIS IS WHERE THE DATA GOES');
			print_r($this->email);
		}
		else
		{
			//failure
			header('HTTP/1.0 444 No Response');
		}*/
	}
	
	protected function hexToBinHandler($errno, $errstr) {
		throw new Exception();
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */