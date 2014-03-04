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
		
		if( $id === FALSE || $data === FALSE || ctype_alnum($id) === FALSE || ctype_xdigit($data) === FALSE ) {
			$error = 'either the id is not specified or is not alphanumeric, or the data is not specified or is not a hex digit.';
		}
		else {
			//make sure it's a hex string, warning thrown if not so handle by calling
			//a handler that throws an error instead
			set_error_handler(array($this, "hexToBinHandler"), E_WARNING);
			try {
			
				echo "raw data: ".$data."<br/>";
				log_message('info', "raw data: ".$data);
				
				$hexData = hex2bin($data);
				
				echo "hex data: ".$hexData."<br/>";
				log_message('info', "hex data: ".$hexData);
				
				if(!$hexData)
					throw new Exception($data." could not be converted to binary data using hex2bin()");
				else {
				
					$origData = $data;
					
					//convert hex string to binary string (ASCII)
					$data = base_convert($data, 16, 2);
					
					echo "convert hex to binary string: ".$data."<br/>";
					log_message('info', "convert hex to binary string: ".$data);
					
					if(substr($origData,0,2) == '00')
						$data = '0'.$data;
					
					//should be a sequence of bytes, so if not divisble by 8
					//then we need to pad in front cause that's the one losing info
					$dataLen = strlen($data);
					$dataReminder = $dataLen % 8;
					$data = $dataReminder != 0 ? str_repeat('0', 8 - $dataReminder) . $data : $data;
					
					echo "data padded with zeros: ".$data."<br/>";
					log_message('info', "data padded with zeros: ".$data);
					
					//now chunk them bytes and reverse each cause data came in MSB first
					//but we need LSB first before putting them back as a binary string
					//to return
					$chunks = str_split($data,8);
					$chunksLen =  count($chunks);
					for( $i=0; $i < $chunksLen; $i++ ) {
							$chunks[$i] = strrev($chunks[$i]);
					}
					$data = implode("",$chunks);
					
					echo "data as LSB: ".$data."<br/>";
					log_message('info', "data as LSB: ".$data);
					
					$this->load->model('Companion_model');
					$data = $this->Companion_model->updateCompanionState($id, $data, true);
					
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
							log_message('error', "id: ".$id.", Error: EMERGENCY ALERT RESPONSE - ".$this->ion_auth->errors());
						}
					}
				}
			}
			catch(Exception $e) {
				$error = $e->getMessage();
			}
			restore_error_handler();
 		}
 		
 		//ob_clean();
 		
 		if($error)
 		{
 			//log_message('error', "id: ".$id.", Error: ".$error.", output: ".$output.', pendingMessage: '.$pendingMessage);
 			echo 'DEBUG... id: '.$id.', error: '.$error.' output: '.$output.', pendingMessage: '.$pendingMessage;
 			//header('HTTP/1.1 444 No Response');
 		}
 		else
 		{
 			//log_message('debug', "id: ".$id.", output: ".$output.', pendingMessage: '.$pendingMessage);
 			echo 'DEBUG... id: '.$id.', output: '.$output.', pendingMessage: '.$pendingMessage;
 			/*if($pendingMessage !== false)
 				header('HTTP/1.1 207 '.$pendingMessage);
 			else
 				header('HTTP/1.1 444 No Response');*/
 		}
 		
 		die();
	}
	
	public function hexToBinHandler($errno, $errstr) {
		throw new Exception();
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */