<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Companion extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$error = false;
		$output = '';
		$pendingMessage = false;
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
					throw new Exception($data." could not be converted to binary data using hex2bin()");
				else {
					//convert hex string to binary string (ASCII)
					$data = base_convert($data, 16, 2);
					
					//should be a sequence of bytes, so if not divisble by 8
					//then we need to pad in front cause that's the one losing info
					$dataLen = strlen($data);
					$dataReminder = $dataLen % 8;
					$data = $dataReminder != 0 ? str_repeat('0', 8 - $dataReminder) . $data : $data;
					
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
					$data = $this->Companion_model->updateCompanionState($id, $data, false);
					
					$output = $data['output'];
					$newEmergency = $data['newEmergency'];
					$pendingMessage = $data['pendingMessage'];
					
					//$newEmergency = 0;
					
					//send out an emergency alert
					if($newEmergency)
					{
						$output .= "<br/>There was a new EMERGENCY ALERT!!!";
						$result = $this->ion_auth->emergency_alert($this->Companion_model->get_companion_by_id($id)->name, $this->Companion_model->get_group_id_by_companion_id($id));
						if($result)
						{
							//$output .= "<br/>Emergency Alert successfully handled";
						}
						else {
							//throw new Exception($this->ion_auth->errors());
							log_message('error', "id: ".$id.", Error: ".$this->ion_auth->errors());
						}
					}
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
 			log_message('error', "id: ".$id.", Error: ".$error.", output: ".$output.', pendingMessage: '.$pendingMessage);
 			//echo 'DEBUG... id: '.$id.', error: '.$error.' output: '.$output.', pendingMessage: '.$pendingMessage;
 			header('HTTP/1.1 444 No Response');
 		}
 		else
 		{
 			log_message('debug', "id: ".$id.", output: ".$output.', pendingMessage: '.$pendingMessage);
 			//echo 'DEBUG... id: '.$id.', output: '.$output.', pendingMessage: '.$pendingMessage;
 			if($pendingMessage !== false)
 				header('HTTP/1.1 207 '.$pendingMessage);
 			else
 				header('HTTP/1.1 444 No Response');
 		}
 		
 		die();
	}
	
	public function hexToBinHandler($errno, $errstr) {
		throw new Exception();
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */