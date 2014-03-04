<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Companion extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}
	
	//examples:  GET /?i=1&d=00C303080, GET /?i=1&d=24C3C3070049EEE00100, etc.
	public function index()
	{
		$error = false;
		$output = '';
		$pendingMessage = false;
		$id = $this->input->get('i', TRUE);
		$data = $this->input->get('d', TRUE);
		
		//data is 10 hex digits length (could be multiple)
		if( $id === FALSE || $data === FALSE || ctype_digit($id) === FALSE || ctype_xdigit($data) === FALSE /*|| strlen($data) % 10 != 0*/ ) {
			$error = 'either the id is not specified or is not a digit or the data is not specified or is not a hex digit or is not the correct length.';
		}
		else {
			
			try {
			
				log_message('info', "raw data: ".$data);
				
				$value = unpack('H*', $data);
				var_dump(base_convert($value[1], 16, 2));
				die();
				
				$zero = substr($data,0,2) == '00';
				
				//convert hex string to binary string (ASCII)
				$data = base_convert($data, 16, 2);
				
				//0 at front is ignored
				if( $zero )
					$data = '0'.$data;
				
				log_message('info', "convert hex to binary string: ".$data);
				
				//should be a sequence of bytes, so if not divisble by 8
				//then we need to pad in front cause that's the one losing info
				$dataLen = strlen($data);
				$dataReminder = $dataLen % 8;
				$data = $dataReminder != 0 ? str_repeat('0', 8 - $dataReminder) . $data : $data;
				
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
				
				log_message('info', "data as LSB: ".$data);
				$this->load->model('Companion_model');
				
				//update model one chunk at a time
				$chunks = str_split($data,40);
				$chunksLen =  count($chunks);
				for( $j=0; $j < $chunksLen; $j++ ) {
				
					log_message('info', "binary chunk ".$j.": ".$chunks[$j]);
				
					$data = $this->Companion_model->updateCompanionState($id, $chunks[$j], false);
					
					$output .= $data['output'];
					$newEmergency = $data['newEmergency'];
					
					//only set the pending message if there is a new message
					if($data['pendingMessage'] !== false)
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
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */