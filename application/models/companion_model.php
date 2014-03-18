<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Companion_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /*
		# Bits -> Data
		
		6 -> Update Flags Update Flags (32 = said update, 16 = message said update, 8 = battery update, 4 = emotion update, 2 = play messages update, 1 = ready to play update... flags can be combined obviously)
		3 -> Voltage Reading (whole number, e.g. #.00) ... max value is 5
		7 -> Voltage (after decimal point, e.g. 0.##) ... max value is 99
		1 -> Is Charging ... if value is 0 then false, if 1 then true
		2 -> Emotional State ... if value is 0 then None, 1 then Happy, if 2 then Unhappy, if 3 then Emergency (HUG protocol)
		1 -> Should Play Message ... if value is 0 then false, if 1 then true
		9 -> Last Said (Companion/Interaction initiated) ... if value is 0 then nothing said yet, represent id's in the database
		9 -> Last Message Said (Community initiated) ... if value is 0 then nothing said yet, represent id's in the database
	
		Total # of bits = 38 bits
				  00010000111001001001111000101000000000 00                  000100 001 1100100 1 00 1 111000101 000000000
		EXAMPLE:  00000000100111011110010001010000000000, SPLITS LIKE THIS:  000001 001 0011101 1 11 0 010001010 000000000
		
			Update Flags Update Flags (32 = said update, 16 = message said update, 8 = battery update, 4 = emotion update, 2 = play messages update, 1 = ready to play update... flags can be combined obviously)
			outgoing data: 1
			000001
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
			Should Play Message? (0 = NO, 1 = YES)
			outgoing data: 0
			0
			Last thing Safety Sam said:
			outgoing data: 162
			010001010
			Last Safety Team message Safety Sam said.
			outgoing data: 0
			000000000
			bits: 00000001
			bits: 00100111
			bits: 01111001
			bits: 00010100
			bits: 00000000
			
			bits length: 38
			bytes to write: 5
			bytes written: 5
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
		if($dataLength < 38)
			throw new Exception($id.": ".$data." has a length ".$dataLength.", which is less than 38");
		if($dataLength > 38)
			throw new Exception($id.": ".$data." has a length ".$dataLength.", which is greater than 38");
		
		if(strspn($data,'01') != $dataLength)
			throw new Exception($id.": ".$data." is not a binary string");
			
		$result = 'data = '.$data;
		$output .= $result;
		if($debug)
			echo $result;	
			
		/**
		*
		* PARSE BINARY STRING AND CHANGE FROM LSB TO MSB
		*
		**/
			
		$current = 0;
		
		$updateFlags = strrev(substr($data,$current,6));
		$current+=6;
		
		$result = '<br/>updateFlags = '.$updateFlags;
		$output .= $result;
		if($debug)
			echo $result;
		
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
			
		$shouldPlayMessage = substr($data,$current,1);
		$current+=1;
		
		$result = '<br/>shouldPlayMessage = '.$shouldPlayMessage;
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
	
		/**
		*
		* CONVERT BINARY STRINGS TO PHP VALUES WE CAN OPERATE ON
		*
		**/
	
		$updateFlags = bindec($updateFlags);
		$saidUpdateFlagged = ($updateFlags & 32) >> 5;
		$messageSaidUpdateFlagged = ($updateFlags & 16) >> 4;
		$batteryUpdateFlagged = ($updateFlags & 8) >> 3;
		$emotionUpdateFlagged = ($updateFlags & 4) >> 2;
		$playMessagesUpdateFlagged = ($updateFlags & 2) >> 1;
		$readyToPlayUpdateFlagged = ($updateFlags & 1);
		
		$result = '<br/>saidUpdateFlagged = '.$batteryUpdateFlagged;
		$output .= $result;
		if($debug)
			echo $result;
			
		$result = '<br/>messageSaidUpdateFlagged = '.$batteryUpdateFlagged;
		$output .= $result;
		if($debug)
			echo $result;
		
		$result = '<br/>batteryUpdateFlagged = '.$batteryUpdateFlagged;
		$output .= $result;
		if($debug)
			echo $result;
			
		$result = '<br/>emotionUpdateFlagged = '.$emotionUpdateFlagged;
		$output .= $result;
		if($debug)
			echo $result;
			
		$result = '<br/>playMessagesUpdateFlagged = '.$playMessagesUpdateFlagged;
		$output .= $result;
		if($debug)
			echo $result;
			
		$result = '<br/>readyToPlayUpdateFlagged = '.$readyToPlayUpdateFlagged;
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
			
		$shouldPlayMessage = bindec($shouldPlayMessage);
		
		$result = '<br/>shouldPlayMessage = '.$shouldPlayMessage;
		$output .= $result;
		if($debug)
			echo $result;
			
		$lastSaid = bindec($lastSaid);
		
		$result = '<br/>lastSaid = '.$lastSaid;
		$output .= $result;
		if($debug)
			echo $result;
			
		$lastSaidAssoc = $this->get_audio_association_by_audio_num($lastSaid);
		
		$lastMessageSaid = bindec($lastMessageSaid);
		
		$result = '<br/>lastMessageSaid = '.$lastMessageSaid;
		$output .= $result;
		if($debug)
			echo $result;
			
		$lastMessageSaidAssoc = $this->get_audio_association_by_audio_num($lastMessageSaid);
		
		//TODO: start a transaction so that updates happen correctly
		
		$lastUpdate = $this->get_latest_update_by_companion_id($companion->id);
		
		//if there has never been an update before
		if(!$lastUpdate)
		{
			$result = '<br/>no last update, there has never been an update before';
			$output .= $result;
			if($debug)
				echo $result;
		
			//only an emotion update if the emotion state is known (user pressed on the of emotional buttons)
			$emotionUpdate = $emotionUpdateFlagged == 1 || $emotionState != 0 ? 1 : 0;
			
			$result = '<br/>emotionUpdate = '.$emotionUpdate;
			$output .= $result;
			if($debug)
				echo $result;
			
			$playMessageUpdate = $playMessagesUpdateFlagged || $shouldPlayMessage;
			
			$result = '<br/>playMessageUpdate = '.$playMessageUpdate;
			$output .= $result;
			if($debug)
				echo $result;
			
			//we know the first update will be charging or not
			$chargeUpdate = 1;
			
			$result = '<br/>chargeUpdate = '.$chargeUpdate;
			$output .= $result;
			if($debug)
				echo $result;
				
			$lowBatteryUpdate = !$isCharging && $voltage <= 3.0 ? 1 : 0;
			
			$result = '<br/>lowBatteryUpdate = '.$lowBatteryUpdate;
			$output .= $result;
			if($debug)
				echo $result;
			
			//only a play message update (also by user) if play message is on (user pressed the play message button)
			$playMessageUpdateByUser = $playMessagesUpdate;
			
			$result = '<br/>playMessageUpdateByUser = '.$playMessageUpdateByUser;
			$output .= $result;
			if($debug)
				echo $result;
			
			//only a message said update if last message said is known (therapuetic companion said the message)
			$messageSaidUpdate = $lastMessageSaid != 0 && $lastMessageSaidAssoc ? 1 : 0;
			
			$result = '<br/>messageSaidUpdate = '.$messageSaidUpdate;
			$output .= $result;
			if($debug)
				echo $result;
			
			//we know the first update would have been talking
			$saidUpdate = $lastSaidAssoc ? 1 : 0;
			
			$result = '<br/>saidUpdate = '.$saidUpdate;
			$output .= $result;
			if($debug)
				echo $result;
		}
		else //there has been updates in the past
		{
			$lastEmotionUpdate = $this->get_latest_emotion_update_by_companion_id($companion->id);
			$lastChargeUpdate = $this->get_latest_charging_update_by_companion_id($companion->id);
			$lastSaidUpdate = $this->get_latest_said_update_by_companion_id($companion->id);
			$lastMessageSaidUpdate = $this->get_latest_message_said_update_by_companion_id($companion->id);
			
			//play message update simply happens if there is a change in the play message status
			$playMessageUpdate = $playMessagesUpdateFlagged || $shouldPlayMessage != $lastUpdate->play_message ? 1 : 0;
			
			$result = '<br/>playMessageUpdate = '.$playMessageUpdate;
			$output .= $result;
			if($debug)
				echo $result;
			
			if(!$lastChargeUpdate) //there has never been a charge update before, so update is true
			{
				$chargeUpdate = 1;
			} //if the charge state is changing
			else if($lastChargeUpdate->is_charging != $isCharging) 
			{ 	
				$chargeUpdate = 1;
			}
			else //still need to determine if update occurred
			{
				$chargeUpdate = $batteryUpdateFlagged;
			}
			
			$result = '<br/>chargeUpdate = '.$chargeUpdate;
			$output .= $result;
			if($debug)
				echo $result;
			
			if($isCharging) //if charging then not a low battery
				$lowBatteryUpdate = 0;
			else if($voltage >= 3.5) //if not above the threshold then not a low battery
				$lowBatteryUpdate = 0;
			else if(!$lastChargeUpdate) //there has never been a charge update before, so update is true
			{
				$lowBatteryUpdate = 1;
			}
			else if($lastChargeUpdate->is_charging) //if the charge state is changing from charging to not then update is true
				$lowBatteryUpdate = 1;
			else //low battery yes, but the last charge update was not charging so don't want to reupdate
				$lowBatteryUpdate = 0;
			
			$result = '<br/>lowBatteryUpdate = '.$lowBatteryUpdate;
			$output .= $result;
			if($debug)
				echo $result;
				
			if($emotionState != 0)
			{
				if(!$lastEmotionUpdate) //there has never been an emotional update before
				{
					$emotionUpdate = 1;
				} //if the emotional state is changing
				else if($lastEmotionUpdate->emotional_state != $emotionState) 
				{ 	
					$emotionUpdate = 1;
				}
				else //still need to determine if update occurred
				{
					$emotionUpdate = $emotionUpdateFlagged;
				}
			}
			else
				$emotionUpdate = 0;
			
			$result = '<br/>emotionUpdate = '.$emotionUpdate;
			$output .= $result;
			if($debug)
				echo $result;	
				
			//can only know for sure if it is a play message update by user if specified directly
			if($playMessagesUpdateFlagged || $shouldPlayMessage) 
			{ 	
				$playMessageUpdateByUser = 1;
			}
			else //still need to determine if update occurred
				$playMessageUpdateByUser = 0;
				
			$result = '<br/>playMessageUpdateByUser = '.$playMessageUpdateByUser;
			$output .= $result;
			if($debug)
				echo $result;	
				
			//only if there is a current association with what was said
			if($lastSaidAssoc)
			{
				if($lastSaidUpdate && $lastSaidUpdate->last_said_id != $lastSaidAssoc->id)
				{
					$saidUpdate = 1;
				}
				else
				{
					$saidUpdate = $saidUpdateFlagged;
				}
			}
			else
				$saidUpdate = 0;
			
			$result = '<br/>saidUpdate = '.$saidUpdate;
			$output .= $result;
			if($debug)
				echo $result;
			
			//only if there is a current association with what message was said
			if($lastMessageSaidAssoc)
			{
				if($lastMessageSaid == 0) //no update
				{
					$messageSaidUpdate = 0;
				}
				else if(!$lastMessageSaidUpdate) //there has never been a message said update before, so update is true
				{
					$messageSaidUpdate = 1;
				} //if the last message state is changing
				else if($lastMessageSaidUpdate->last_message_said_id != $lastMessageSaidAssoc->id)
				{
					$messageSaidUpdate = 1;
				}
				else
				{
					$messageSaidUpdate = $messageSaidUpdateFlagged;
				}
			}
			else
				$messageSaidUpdate = 0;
			
			$result = '<br/>messageSaidUpdate = '.$messageSaidUpdate;
			$output .= $result;
			if($debug)
				echo $result;
		}
		
		//update emergency status
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
		
		$data = array(
		   'companion_id' => $companion->id,
		   'voltage' => $voltage,
		   'is_charging' => $isCharging,
		   'is_charging_update' => $chargeUpdate,
		   'low_battery_update' => $lowBatteryUpdate,
		   'emotional_state' => $emotionState,
		   'emotion_update' => $emotionUpdate,
		   'emergency_update' => $newEmergency,
		   'play_message' => $playMessageUpdateByUser,
		   'play_message_update' => $playMessageUpdate,
		   'play_message_update_by_user' => $playMessageUpdateByUser,
		   'last_said_id' => $lastSaidAssoc ? $lastSaidAssoc->id : NULL,
		   'last_said_update' => $saidUpdate,
		   'last_message_said_id' => $lastMessageSaid != 0 && $lastMessageSaidAssoc ? $lastMessageSaidAssoc->id : NULL,
		   'last_message_said_update' => $messageSaidUpdate
		);
		
		$result = '<br/>'.json_encode($data);
		$output .= $result;
		if($debug)
			echo $result;
			
		$this->db->insert('companion_updates',$data);
		
		$companionUpdateId = $this->db->insert_id();
		
		$result = '<br/>insert id: '.$companionUpdateId.'<br/>affected rows: '.$this->db->affected_rows().'<br/>db error: '.$this->db->_error_message();
		$output .= $result;
		if($debug)
			echo $result;
			
		if( $this->db->affected_rows() < 1 )
		{
			// TODO:  Delete the last update???
    		throw new Exception("Couldn't update the companion_updates table");
    	}
    		
    	//only return a pending message if currently asking for one
    	if($playMessageUpdateByUser)
    		$pendingMessage = $this->get_pending_message_association();
    	else
    		$pendingMessage = null;
    		
    	$result = '<br/>pendingMessage = '.json_encode($pendingMessage);
		$output .= $result;
		if($debug)
			echo $result;
    		
    	//only try to update the companion message with this companion update if there was a message said update that has a message association
    	if($messageSaidUpdate)
    	{
    		$result = '<br/>lastMessageSaidAssoc->companion_says_id = '.$lastMessageSaidAssoc->companion_says_id;
			$output .= $result;
			if($debug)
				echo $result;
				
    		$companionMessageUpdate = $this->update_companion_message_for_companion_update($companionUpdateId, $lastMessageSaidAssoc->companion_says_id);
    		
    		$result = '<br/>companionMessageUpdate = '.json_encode($companionMessageUpdate);
			$output .= $result;
			if($debug)
				echo $result;
    	}
    		
    	//turn off curfew alert
		$this->db->where(array('id' => $companion->id, 'curfew_alert' => 1));
		$this->db->update('companions', array('curfew_alert' => 0));
    	
    	$result = '<br/>turn off curfew alert<br/>affected rows: '.$this->db->affected_rows().'<br/>db error: '.$this->db->_error_message();
		$output .= $result;
		if($debug)
			echo $result;
    	
    	return array('output' => $output, 'newEmergency' => ($newEmergency < 1 ? false : true), 'pendingMessage' => ($pendingMessage ? $pendingMessage->audio_num : false));
	}
    
    public function curfew_check() {
    	$this->db->trans_start();
    	
    	//grab the first distinct companion_ids that have been updated in the last 15 minutes
    	$this->db->select('companion_id');
    	$this->db->from('companion_updates');
    	$this->db->where('companion_updates.created_at > timestampadd(minute, -15, now())');
    	$this->db->group_by('companion_id');
    	$this->db->order_by('created_at', 'DESC');
    	$this->db->distinct();
		
		$result = $this->db->get();
		
		if(!$result)
    		return '';

		$ids = array();
		foreach ($result->result() as $row)
		{
			array_push($ids, $row->companion_id);
		}
		
		//update all companions who are not in that list as out past curfew
		if(count($ids) != 0)
			$this->db->where_not_in('id', $ids);
		$this->db->where('curfew_alert', '0');
		$result = $this->db->update('companions', array('curfew_alert'=>1)); 
		
    	$this->db->trans_complete();
    	
    	if ($this->db->trans_status() !== FALSE)
		{
			return '';
		}
		
    	return $this->db->_error_message().' ...failed to update companions curfew_alert: '.implode(', ',$ids);
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
    	$this->db->order_by("created_at", "asc");
    	$query = $this->db->get('companion_updates');
    	return $query->result();
    }
    
    public function get_emotion_updates_by_companion_id($id)
    {
    	$this->db->select('*');
    	$this->db->where('companion_id', $id);
    	$this->db->where('emotional_state !=', 0);
    	$this->db->where('emotion_update', 1);
    	$this->db->order_by("created_at", "asc");
    	$query = $this->db->get('companion_updates');
    	return $query->result();
    }
    
    public function get_latest_emotion_update_by_companion_id($id)
    {
    	$this->db->select('*');
    	$this->db->where('companion_id', $id);
    	$this->db->where('emotion_update', 1);
    	$this->db->order_by("created_at", "desc");
    	$this->db->limit(1);
    	$query = $this->db->get('companion_updates');
    	$result = $query->result();
    	if($result)
    		return $result[0];
    	return $result;
    }
    
    public function get_latest_emergency_update_by_companion_id($id)
    {
    	$this->db->select('*');
    	$this->db->where('companion_id', $id);
    	$this->db->where('emergency_update', 1);
    	$this->db->order_by("created_at", "desc");
    	$this->db->limit(1);
    	$query = $this->db->get('companion_updates');
    	$result = $query->result();
    	if($result)
    		return $result[0];
    	return $result;
    }
    
    public function get_latest_charging_update_by_companion_id($id)
    {
    	$this->db->select('*');
    	$this->db->where('companion_id', $id);
    	$this->db->where('is_charging_update', 1);
    	$this->db->order_by("created_at", "desc");
    	$this->db->limit(1);
    	$query = $this->db->get('companion_updates');
    	$result = $query->result();
    	if($result)
    		return $result[0];
    	return $result;
    }
    
    public function get_latest_low_battery_update_by_companion_id($id)
    {
    	$this->db->select('*');
    	$this->db->where('companion_id', $id);
    	$this->db->where('low_battery_update', 1);
    	$this->db->order_by("created_at", "desc");
    	$this->db->limit(1);
    	$query = $this->db->get('companion_updates');
    	$result = $query->result();
    	if($result)
    		return $result[0];
    	return $result;
    }
    
    public function get_latest_play_message_update_by_companion_id($id)
    {
    	$this->db->select('*');
    	$this->db->where('companion_id', $id);
    	$this->db->where('play_message_update', 1);
    	$this->db->order_by("created_at", "desc");
    	$this->db->limit(1);
    	$query = $this->db->get('companion_updates');
    	$result = $query->result();
    	if($result)
    		return $result[0];
    	return $result;
    }
    
    public function get_latest_play_message_update_by_user_by_companion_id($id)
    {
    	$this->db->select('*');
    	$this->db->where('companion_id', $id);
    	$this->db->where('play_message_update_by_user', 1);
    	$this->db->order_by("created_at", "desc");
    	$this->db->limit(1);
    	$query = $this->db->get('companion_updates');
    	$result = $query->result();
    	if($result)
    		return $result[0];
    	return $result;
    }
    
    public function get_latest_said_update_by_companion_id($id)
    {
    	$this->db->select('*');
    	$this->db->where('companion_id', $id);
    	$this->db->where('last_said_update', 1);
    	$this->db->order_by("created_at", "desc");
    	$this->db->limit(1);
    	$query = $this->db->get('companion_updates');
    	$result = $query->result();
    	if($result)
    		return $result[0];
    	return $result;
    }
    
    public function get_latest_message_said_update_by_companion_id($id)
    {
    	$this->db->select('*');
    	$this->db->where('companion_id', $id);
    	$this->db->where('last_message_said_update', 1);
    	$this->db->order_by("created_at", "desc");
    	$this->db->limit(1);
    	$query = $this->db->get('companion_updates');
    	$result = $query->result();
    	if($result)
    		return $result[0];
    	return $result;
    }
    
    public function get_message_said_updates_by_companion_id($id)
    {
    	/*$this->db->select('*');
    	$this->db->where('companion_id', $id);
    	$this->db->where('last_message_said_update', 1);
    	$this->db->order_by("created_at", "asc");
    	$query = $this->db->get('companion_updates');
    	return $query->result();*/
    	
    	$query = $this->db->query('SELECT companion_updates.*, companion_messages.user_id, companion_messages.companion_says_id, users.first_name, users.last_name, companion_says.text FROM companion_updates, companion_messages, users, companion_says WHERE companion_updates.id = companion_messages.companion_updates_id AND users.id = companion_messages.user_id AND companion_messages.companion_says_id = companion_says.id');
		return $query->result();
    }
    
    public function get_latest_update_by_companion_id($id)
    {
    	$this->db->select('*');
    	$this->db->where('companion_id', $id);
    	$this->db->order_by("created_at", "desc");
    	$this->db->limit(1);
    	$query = $this->db->get('companion_updates');
    	$result = $query->result();
    	if($result)
    		return $result[0];
    	return $result;
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
    
    public function unassignCompanion($id)
    {
    	$data = array(
		   'companion_id' => $id
		);
    	$this->db->delete('companions_groups', $data);
    	
    	if( $this->db->affected_rows() < 1 )
    		return false;
    	return true;
    }
    
    public function change_name($id, $name)
    {
    	$data = array(
		   'name' => $name
		);
    	$this->db->where('id', $id);
    	$updateResult = $this->db->update('companions', $data);
    	
    	if($updateResult)
    		return true;
    	return false;
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
    
    public function get_messages()
    {
    	$result = $this->db->get_where('companion_says', array('is_message' => 1))->result();
    	
    	if($result)
    		return $result;
    		
    	return null;
    }
    
    public function get_message($companionSaysId)
    {
    	$this->db->select('*');
    	$this->db->where(array('id' => $companionSaysId, "is_message" => "1"));
    	$this->db->limit(1);
    	$query = $this->db->get('companion_says');
    	$result = $query->result();
    	if($result)
    		return $result[0];
    	return $result;
    }
    
    public function set_pending_message($id, $companionSaysId, $userId)
    {
    	$data = array(
    		'user_id' => $userId,
		   	'companion_id' => $id,
		   	'companion_says_id' => $companionSaysId
		);
    	$this->db->insert('companion_messages', $data);
    	
    	if( $this->db->affected_rows() < 1 )
    		return false;
    	return true;
    }
    
    public function get_pending_message_association()
    {
    	$this->db->select('*');
    	$this->db->order_by("id", "asc");
    	$this->db->limit(1);
    	$this->db->where('companion_says_id !=', 'NULL');
    	$this->db->where('is_pending', 1);
    	$query = $this->db->get('companion_messages');
    	$result = $query->result();
    	
    	if($result)
    	{
    		$message = $result[0];
    		$data = array(
				'is_pending' => 0
			);
			$this->db->where('id', $message->id);
			$updateResult = $this->db->update('companion_messages', $data);
			
			if($updateResult)
			{
				return $this->get_audio_association_by('companion_says_id',$message->companion_says_id);
			}
			else
				return null;
    	}
    	
    	return $result;
    }
    
    /**
    *
    * The companion message record that is updated is the one with the following criteria:
    * 
    *  1.  FIFO: The earliest messages in the queue (by id)
    *  2.  That matches the $companionSaysId
    *  3.  That is not pending as it has already has been sent (is_pending is 0), and
    *  4.  That does not already have a companion update associated yet (companion_updates_id is NULL)
    *
    *  TODO:  Make this a transaction (this will happen when the function that calls this is a made into a transaction
    *
    **/
    protected function update_companion_message_for_companion_update($companionUpdateId, $companionSaysId)
    {
    	$message = $this->check_for_companion_message_for_companion_says_id($companionSaysId);
    	
    	if($message)
    	{
    		$data = array(
				'companion_updates_id' => $companionUpdateId
			);
			$this->db->where('id', $message->id);
			$updateResult = $this->db->update('companion_messages', $data);
			
			if($updateResult)
			{
				return true;
			}
			else
				return false;
    	}
    	
    	return $message;
    }
    
    protected function check_for_companion_message_for_companion_says_id($companionSaysId)
    {
    	$this->db->select('*');
    	$this->db->order_by("id", "asc");
    	$this->db->limit(1);
    	$this->db->where('companion_says_id', $companionSaysId);
    	$this->db->where('is_pending', 0);
    	$this->db->where('companion_updates_id', NULL);
    	$query = $this->db->get('companion_messages');
    	$result = $query->result();
    	
    	if($result)
    		return $result[0];
    	
    	return $result;
    }
    
    public function get_audio($criteria, $content)
    {
    	$result = $this->get_audio_association_by($criteria,$content);
    	if($result)
    	{
    		$audio = new stdClass;
    		$audio->audio_num = $result->audio_num;
    		
    		$result = $this->db->get_where('companion_says', array('id' => $result->companion_says_id))->result();
    		
    		if($result)
    		{
    			$result = $result[0];
    			$audio->text = $result->text;
    			$audio->is_message = $result->is_message;
    			
    			try {
    				$audio->audio_url = $this->getAudioURL($audio->audio_num);
    			}
    			catch(Exception $e) {
    				return null;
    			}
    			
    			return $audio;
    		}
    		else
    			return $result;
    	}
    	else
    		return $result;
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