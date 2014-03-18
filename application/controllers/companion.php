<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Companion extends MY_Controller {

	private $debug = false;

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
		$newEmergency = false;
		$id = $this->input->get('i', TRUE);
		$data = $this->input->get('d', TRUE);
		
		if($this->debug)
			echo "id: ".$id."<br/>";
		log_message('info', "id: ".$id);
		
		if($this->debug)
			echo "raw data: ".$data."<br/>";
		log_message('info', "raw data: ".$data);
		
		//data is at least 10 hex digits length (could be more if multiple data updates)
		if( $id === FALSE || $data === FALSE || ctype_digit($id) === FALSE || ctype_xdigit($data) === FALSE || strlen($data) < 10 ) {
			$error = 'either the id is not specified or is not a digit or the data is not specified or is not a hex digit or is not the correct length.';
			log_message('error', "Error: ".$error);
		}
		else {
			$chunks = str_split($data,2);
			$chunksLen = count($chunks);
			for( $i=0; $i < $chunksLen; $i++ ) {
			
				if($this->debug)
					echo "before convert hex to binary string: ".$chunks[$i]."<br/>";
				log_message('info', "before convert hex to binary string: ".$chunks[$i]);
				
				$chunks[$i] = base_convert($chunks[$i], 16, 2);
				
				if($this->debug)
					echo "after convert hex to binary string: ".$chunks[$i]."<br/>";
				log_message('info', "after convert hex to binary string: ".$chunks[$i]);
				
				$dataLen = strlen($chunks[$i]);
				$dataReminder = $dataLen % 8;
				$chunks[$i] = $dataReminder != 0 ? str_repeat('0', 8 - $dataReminder) . $chunks[$i] : $chunks[$i];
				
				if($this->debug)
					echo "binary string padded with zeros: ".$chunks[$i]."<br/>";
				log_message('info', "binary string padded with zeros: ".$chunks[$i]);
				
				$chunks[$i] = strrev($chunks[$i]);
				
				if($this->debug)
					echo "binary string in LSB: ".$chunks[$i]."<br/>";
				log_message('info', "binary string in LSB: ".$chunks[$i]);
			}
			$data = implode("",$chunks);
			
			if($this->debug)
				echo "data in LSB: ".$data."<br/>";
			log_message('info', "data in LSB: ".$data);
				
			$this->load->model('Companion_model');
			
			//update model one chunk at a time
			$chunks = str_split($data,38);
			$chunksLen =  count($chunks);
			for( $j=0; $j < $chunksLen; $j++ ) {
			
				if( strlen($chunks[$j]) < 38 )
					break;
					
				if($this->debug)
					echo "update model data: ".$j.": ".$chunks[$j]."<br/>";
				log_message('info', "update model data: ".$j.": ".$chunks[$j]);
			
				try {
					$data = $this->Companion_model->updateCompanionState($id, $chunks[$j], false);
					
					$output .= $data['output'];
					$newEmergency = $data['newEmergency'];
					
					//only set the pending message if there is a new message
					if($data['pendingMessage'] !== false)
						$pendingMessage = $data['pendingMessage'];
				}
				catch(Exception $e) {
					$error = $e->getMessage();
					if($this->debug)
						echo 'DEBUG... error: '.$error;
 					log_message('error', "Error: ".$error);
				}
			}
 		}
 		
 		if(!$this->debug)
 			ob_clean();
 		
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
				if($this->debug)
					echo "DEBUG... Error: EMERGENCY ALERT RESPONSE - ".$this->ion_auth->errors();
				log_message('error', "Error: EMERGENCY ALERT RESPONSE - ".$this->ion_auth->errors());
			}
		}
 		
 		if($this->debug)
			echo 'DEBUG... PENDING MESSAGE: '.$pendingMessage.', NEW EMERGENCY: '.$newEmergency.', OUTPUT: '.$output;
 		log_message('debug', 'PENDING MESSAGE: '.$pendingMessage.', NEW EMERGENCY: '.$newEmergency.', OUTPUT: '.$output);
 		
 		if($pendingMessage !== false)
			header('HTTP/1.1 207 '.$pendingMessage);
		else
			header('HTTP/1.1 444 No Response');
 		
 		die();
	}
	
}