<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Companion_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /*
		# Bits -> Data
		
		3 -> Voltage Reading (whole number, e.g. #.00) ... max value is 5
		7 -> Voltage (after decimal point, e.g. 0.##) ... max value is 99
		1 -> Is Charging ... if value is 0 then false, if 1 then true
		2 -> Emotional State ... if value is 0 then None, 1 then Happy, if 2 then Unhappy, if 3 then Emergency (HUG protocol)
		1 -> Is Quiet Time ... if value is 0 then false, if 1 then true
		9 -> Last Said (Companion/Interaction initiated) ... if value is 0 then nothing said yet, represent id's in the database
		9 -> Last Message Said (Community initiated) ... if value is 0 then nothing said yet, represent id's in the database
	
		Total # of bits = 32 bits
		
		EXAMPLE:  00100111011110010001010000000000, SPLITS LIKE THIS:  001 0011101 1 11 0 010001010 000000000
		
			Voltage Reading (whole number, e.g. #.00):
			outgoing data: 4
			001
			Voltage (after decimal point, e.g. 0.##):
			outgoing data: 92
			0011101
			Is Charging? (0 = NO, 1 = YES)
			outgoing data: 1
			1
			Emotional State (0 - None, 1 - Happy, 2 - UNHAPPY, 3 - EMERGENCY)
			outgoing data: 3
			11
			Is Quiet Time? (0 = NO, 1 = YES)
			outgoing data: 0
			0
			Last thing Safety Sam said:
			outgoing data: 162
			010001010
			Last Safety Team message Safety Sam said.
			outgoing data: 0
			000000000
			bits: 00100111
			bits: 01111001
			bits: 00010100
			bits: 00000000
			
			bits length: 32
			bytes to write: 4
			bytes written: 4
	*/
	public function updateCompanionState($id, $data, $debug = false)
	{
		$output = '';
		if(!$id)
			throw new Exception('id not specified');
		
		if( !($companion = $this->get_companion_by_id($id)) )
			throw new Exception($id.": Couldn't find companion by this identifier");
		
		if(!$data)
			throw new Exception($id.": No data to update companion with");
			
		if(!is_string($data))
			throw new Exception($id.": ".$data." is not a string");
			
		$dataLength = strlen($data);
		if($dataLength < 32)
			throw new Exception($id.": ".$data." has a length ".$dataLength.", which is less than 32");
		if($dataLength > 32)
			throw new Exception($id.": ".$data." has a length ".$dataLength.", which is greater than 32");
		
		if(strspn($data,'01') != $dataLength)
			throw new Exception($id.": ".$data." is not a binary string");
			
		$result = 'data = '.$data;
		$output .= $result;
		if($debug)
			echo $result;	
			
		$current = 0;
		
		$voltageWholeNumber = strrev(substr($data,$current,3));
		$current+=3;
		
		$result = '<br/>voltageWholeNumber = '.$voltageWholeNumber;
		$output .= $result;
		if($debug)
			echo $result;
			
		$voltageAfterDecimalPoint = strrev(substr($data,$current,7));
		$current+=7;
		
		$result = '<br/>voltageAfterDecimalPoint = '.$voltageAfterDecimalPoint;
		$output .= $result;
		if($debug)
			echo $result;
			
		$isCharging = substr($data,$current,1);
		$current+=1;
		
		$result = '<br/>isCharging = '.$isCharging;
		$output .= $result;
		if($debug)
			echo $result;
			
		$emotionalState = strrev(substr($data,$current,2));
		$current+=2;
		
		$result = '<br/>emotionalState = '.$emotionalState;
		$output .= $result;
		if($debug)
			echo $result;
			
		$isQuietTime = substr($data,$current,1);
		$current+=1;
		
		$result = '<br/>isQuietTime = '.$isQuietTime;
		$output .= $result;
		if($debug)
			echo $result;
			
		$lastSaid = strrev(substr($data,$current,9));
		$current+=9;
		
		$result = '<br/>lastSaid = '.$lastSaid;
		$output .= $result;
		if($debug)
			echo $result;
			
		$lastMessageSaid = strrev(substr($data,$current,9));
		$current+=9;
		
		$result = '<br/>lastMessageSaid = '.$lastMessageSaid;
		$output .= $result;
		if($debug)
			echo $result;
	
		if($voltageWholeNumber != '000')
		{
			$temp = bindec($voltageWholeNumber);
			if($temp > 5)
				throw new Exception($id.": ".$voltageWholeNumber." is 6 volts or greater which is not possible");
			$voltageWholeNumber = floatval($temp);
		}
		else
			$voltageWholeNumber = 0.0;
		
		$result = '<br/>voltageWholeNumber = '.$voltageWholeNumber;
		$output .= $result;
		if($debug)
			echo $result;
			
		if($voltageAfterDecimalPoint != '0000000')
		{
			$temp = bindec($voltageAfterDecimalPoint);
			
			$result = '<br/>voltageAfterDecimalPoint = '.$temp;
			$output .= $result;
			if($debug)
				echo $result;
				
			if($temp > 99)
				throw new Exception($id.": ".$voltageAfterDecimalPoint." is greater than 99 which is not possible");
			if($temp != 0)
				$voltageAfterDecimalPoint = floatval($temp) / 100.0;
			else
				$voltageAfterDecimalPoint = 0.0;
		}
		else
			$voltageAfterDecimalPoint = 0.0;
	
		$result = '<br/>voltageAfterDecimalPoint = '.$voltageAfterDecimalPoint;
		$output .= $result;
		if($debug)
			echo $result;
	
		//voltage calculation
		$voltage = $voltageWholeNumber + $voltageAfterDecimalPoint;
		
		$result = '<br/>voltage = '.$voltage;
		$output .= $result;
		if($debug)
			echo $result;
		
		$isCharging = bindec($isCharging);
		
		$result = '<br/>isCharging = '.$isCharging;
		$output .= $result;
		if($debug)
			echo $result;
			
		$emotionState = bindec($emotionalState);
		
		$result = '<br/>emotionState = '.$emotionState;
		$output .= $result;
		if($debug)
			echo $result;
			
		$isQuietTime = bindec($isQuietTime);
		
		$result = '<br/>isQuietTime = '.$isQuietTime;
		$output .= $result;
		if($debug)
			echo $result;
			
		$lastSaid = bindec($lastSaid);
		
		$result = '<br/>lastSaid = '.$lastSaid;
		$output .= $result;
		if($debug)
			echo $result;
			
		$lastSaid = $this->get_audio_association_by_audio_num($lastSaid);
		$lastMessageSaid = bindec($lastMessageSaid);
		
		$result = '<br/>lastMessageSaid = '.$lastMessageSaid;
		$output .= $result;
		if($debug)
			echo $result;
			
		$lastMessageSaid = $this->get_audio_association_by_audio_num($lastMessageSaid);
		
		$data = array(
		   'companion_id' => $companion->id,
		   'voltage' => $voltage,
		   'is_charging' => $isCharging,
		   'emotional_state' => $emotionState,
		   'quiet_time' => $isQuietTime,
		   'last_said_id' => $lastSaid ? $lastSaid->id : NULL,
		   'last_message_said_id' => $lastMessageSaid ? $lastMessageSaid->id : NULL
		);
		
		$result = '<br/>'.json_encode($data);
		$output .= $result;
		if($debug)
			echo $result;
			
		$this->db->insert('companion_updates',$data);
		
		$result = '<br/>insert id: '.$this->db->insert_id().'<br/>affected rows: '.$this->db->affected_rows().'<br/>db error: '.$this->db->_error_message();
		$output .= $result;
		if($debug)
			echo $result;
			
		if( $this->db->affected_rows() < 1 )
    		throw new Exception("Couldn't update the companion_updates table");
    		
    	if($emotionState == 3)
    	{
    		//state of emergency
    		$data = array(
    			'emergency_alert' => 1
    		);
    		$this->db->where('id', $companion->id);
    		$updateResult = $this->db->update('companions', $data);
    		$newEmergency = $this->db->affected_rows();
    		
    		$result = '<br/>updating companion because of an emergency alert for companion id: '.$companion->id.'<br/>affected rows: '.$newEmergency.'<br/>update result: '.$updateResult .'<br/>db error: '.$this->db->_error_message();
			$output .= $result;
			if($debug)
				echo $result;
    		
    		if( !$updateResult )
    			throw new Exception("Couldn't update the companions table when there is an emergency alert");
    	}
    	else
    		$newEmergency = 0;
    		
    	return array('output' => $output, 'newEmergency' => ($newEmergency < 1 ? false : true));
	}
    
    public function get_unassigned_companions()
    {
    	//SEE http://stackoverflow.com/questions/354002/mysql-select-where-not-in-table
		$query = $this->db->query('SELECT companions.id, companions.name, companions.description FROM companions LEFT JOIN companions_groups ON companions.id = companions_groups.companion_id WHERE companions_groups.companion_id is NULL');
        return $query;
    }
    
    public function get_companion_by_id($id)
    {
    	$result = $this->db->get_where('companions', array('id' => $id))->result();
    	
    	if($result)
    		return $result[0];
    	
    	return null;
    }
    
    public function get_group_id_by_companion_id($id)
    {
    	$result = $this->db->get_where('companions_groups', array('companion_id' => $id))->result();
    	
    	if($result)
    		return $result[0]->group_id;
    	
    	return null;
    }
    
    //TODO:  Optimize this query
    public function get_companion_by_group_id($groupId)
    {
    	$result = $this->db->get_where('companions_groups', array('group_id' => $groupId))->result();
    	
    	if($result)
    	{
    		return $this->get_companion_by_id($result[0]->companion_id);
    	}
    	
    	return null;
    }
    
    public function get_updates_by_companion_id($id)
    {
    	$this->db->select('*');
    	$this->db->where('companion_id', $id);
    	$this->db->order_by("created_at", "desc");
    	$query = $this->db->get('companion_updates');
    	return $query->result();
    }
    
    public function assignCompanionToGroup($id, $groupId)
    {
    	$data = array(
		   'companion_id' => $id,
		   'group_id' => $groupId
		);
    	$this->db->insert('companions_groups', $data);
    	
    	if( $this->db->affected_rows() < 1 )
    		return false;
    	return true;
    }
    
    public function clear_emergency_alert($id)
    {
    	$data = array(
		   'emergency_alert' => 0
		);
    	$this->db->where('id', $id);
    	$updateResult = $this->db->update('companions', $data);
    	
    	if($updateResult)
    		return true;
    	return false;
    }
    
    public function add_audio($audioNum, $text, $isMessage, $mp3, $size)
    {
    	//check if $audioNum exists in companion_says_audio
    	$exists = $this->db->get_where('companion_says_audio', array('audio_num' => $audioNum))->result();
    	
    	//if couldn't find we will be doing an insert
    	if(!$exists) {
    	
    		//update $mp3 into companion_audio
    		$data = array(
			   'data' => $mp3
			);
    		$this->db->insert('companion_audio', $data);
    	
    		if( $this->db->affected_rows() < 1 )
    			return false;
    		
    		//TODO: this is probably not perfect, use query instead 
    		$companionAudioId = $this->db->insert_id();
    		
    		//update $text and $isMessage into companion_says
    		$data = array(
			   'text' => $text,
			   'is_message' => $isMessage
			);
    		$this->db->insert('companion_says', $data);
    		
    		if( $this->db->affected_rows() < 1 )
    		{	
    			$this->db->where('id', $companionAudioId);
    			$this->db->delete('companion_audio'); 
    			return false;
    		}
    			
    		//TODO: this is probably not perfect, use query instead 
    		$companionSaysId = $this->db->insert_id();
    		
    		//update $audioNum into companion_says_audio
    		$data = array(
			   'audio_num' => $audioNum,
			   'companion_audio_id' => $companionAudioId,
			   'companion_says_id' => $companionSaysId
			);
			
			$this->db->insert('companion_says_audio', $data);
    		
    		if( $this->db->affected_rows() < 1 )
    		{	
    			$this->db->where('id', $companionAudioId);
    			$this->db->delete('companion_audio');
    			$this->db->where('id', $companionSaysId);
    			$this->db->delete('companion_says');  
    			return false;
    		}
    		
    		//TODO: this is probably not perfect, use query instead 
    		$companionSaysAudioId = $this->db->insert_id();
    		
    		return $companionSaysAudioId;
    		
    	}
    	else //we will be updating
    	{
    		$companionSaysAudio = $exists[0];
    		
    		//update $mp3 into companion_audio
    		$data = array(
    			'data' => $mp3
			);
			$this->db->where('id', $companionSaysAudio->companion_audio_id);
    		$result = $this->db->update('companion_audio', $data);
    	
    		if( !$result )
    			return false;
    		
    		//update $text and $isMessage into companion_says
    		$data = array(
			   	'text' => $text,
			   	'is_message' => $isMessage
			);
			$this->db->where('id', $companionSaysAudio->companion_says_id);
    		$result = $this->db->update('companion_says', $data);
    		
    		if( !$result )
    			return false;
    			
    		return $companionSaysAudio->id;
    		
    	}
    }
    
    public function getAudioURL($audioNum, $forceUpdate = FALSE)
	{
		$audio = $this->get_audio_association_by_audio_num($audioNum);
		if(!$audio)
		{
			throw new Exception("Audio does not exist for audio num: ".$audioNum);
		}
		else
		{
			$basePath = 'assets/uploads/media/audio';
			$filePath = $basePath.'/'.$audioNum.'.mp3';
			$absoluteURL = base_url($filePath).'?updated_at='.$audio->updated_at;
			
			//make directory if does not exist
			if (!file_exists($basePath)) {
				$dirExists = mkdir($basePath, 0777, true);
			}
			else
				$dirExists = true;
				
			//if directory doesn't exist after trying to create it then error out
			if(!$dirExists)
				throw new Exception("Can't make audio directory: ".$basePath);
			
			$this->load->helper('file');
			
			//check if stored as a file yet
			$audioFileInfo = get_file_info($filePath, array('readable','writable'));
			
			//if audio found
			if( $audioFileInfo )
			{
				//if readable and not forcing and update then return the absolute URL
				if($audioFileInfo['readable'] && !$forceUpdate)
					return $absoluteURL;
					
				//if forcing and update and not writeable then error out
				if($forceUpdate && !$audioFileInfo['writable'])
					throw new Exception("Can't write audio to file: ".$filePath);
			}
			
			//get audio data
			$audioData = $audio = $this->get_audio_data($audio);
			
			//if couldn't retrieve it then error out
			if(!$audioData)
				throw new Exception("Couldn't read audio data from the database for audio num: ".$audioNum);
				
			if(!write_file($filePath, $audioData))
				throw new Exception("Can't write audio to file: ".$filePath);
				
			//return the absolute URL
			return $absoluteURL;
		}
	}
    
    protected function get_audio_association_by_audio_num($audioNum)
    {
    	return $this->get_audio_association_by('audio_num', $audioNum);
    }
    
    protected function get_audio_association_by($criteria, $content)
    {
    	//check if $content exists in companion_says_audio by $criteria
    	$result = $this->db->get_where('companion_says_audio', array($criteria => $content))->result();
    	
    	if($result)
    		return $result[0];
    	
    	return null;
    }
    
    protected function get_audio_data($companionSaysAudio)
    {
    	$this->db->where('id', $companionSaysAudio->companion_audio_id);
    	$result = $this->db->get('companion_audio')->result();
    	
		if( !$result )
			return null;
			
		$companionAudio = $result[0];
		
		return $companionAudio->data;
    }
}