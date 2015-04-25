<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if (!$this->ion_auth->logged_in())
		{
			//redirect them to sign in
			redirect('sign_in', 'refresh');
			return;
		}
		
		$clearAlertId = $this->input->get('clear_alert', TRUE);
		$isTeamLeader = $this->ion_auth->is_admin();
		
		$groups = $this->ion_auth->get_users_groups()->result();
		$companions = array();
		$groupToCompanion = array();
		$companionToGroup = array();
		$companionToEmotionUpdates = array();
		$companionToLastUpdate = array();
		$companionToLowBattery = array();
		$companionToLastChargingUpdate = array();
		$companionToLastPlayMessageOnUserUpdate = array();
		$companionToLastSaid = array();
		$companionToMessagesSaidUpdates = array();
		$companionToLastUpdateWithEmotion = array();
		$companionToLastEmergencyUpdate = array();
		$companionToLastCurfewUpdate = array();
		$hasAlerts = false;
		
		$this->load->model('Companion_model');
		
		foreach( $groups as $group )
		{
			if(!$isTeamLeader)
				$isTeamLeader = $this->ion_auth->is_group_editor($group->name);
				
			$companion = $this->Companion_model->get_companion_by_group_id($group->id);
			if($companion)
			{
				if($clearAlertId == $companion->id && $isTeamLeader)
				{
					if($this->Companion_model->clear_emergency_alert($clearAlertId))
					{
						redirect('dashboard', 'refresh');
					}
				}
				
				array_push($companions, $companion);
				$groupToCompanion[$group->id] = $companion;
				$companionToGroup[$companion->id] = $group;
				
				$lastUpdate = $this->Companion_model->get_latest_update_by_companion_id($companion->id);
				
				if($lastUpdate)
				{
					$companionToLastUpdate[$companion->id]['update'] = $lastUpdate;
					$companionToLastUpdate[$companion->id]['timeElapsed'] = $this->humanTiming($lastUpdate->created_at);
					
					/*$lastUpdateWithCharging = $this->Companion_model->get_latest_charging_update_by_companion_id($companion->id);
					
					if($lastUpdateWithCharging)
					{
						$companionToLastChargingUpdate[$companion->id]['update'] = $lastUpdateWithCharging;
						$companionToLastChargingUpdate[$companion->id]['timeElapsed'] = $this->humanTiming($lastUpdateWithCharging->created_at);
					}*/
					
					//this is used to show if currently using battery or recharging along with the voltage measurement
					$companionToLastChargingUpdate[$companion->id] = $companionToLastUpdate[$companion->id];
					
					//only check for a low battery alert if currently not charging and the last update voltage is in the low battery range
					if(!$lastUpdate->is_charging && $lastUpdate->voltage < Companion_model::LOW_BATTERY_THRESHOLD)
					{
						$lastUpdateWithLowBattery = $this->Companion_model->get_latest_low_battery_update_by_companion_id($companion->id);
						$lastUpdateWithCharging = $this->Companion_model->get_latest_charging_update_by_companion_id($companion->id);
						
						//if a low battery update and no last charging update or there hasn't been a charge update since the last low battery update
						if($lastUpdateWithLowBattery && !$lastUpdateWithCharging || $lastUpdateWithLowBattery->created_at >= $lastUpdateWithCharging->created_at)
						{
							$companionToLowBattery[$companion->id]['update'] = $lastUpdateWithLowBattery;
							$companionToLowBattery[$companion->id]['timeElapsed'] = $this->humanTiming($lastUpdateWithLowBattery->created_at);
							$hasAlerts = true;
						}
					}
					
					$lastUpdateWithPlayMessageUser = $this->Companion_model->get_latest_play_message_update_by_user_by_companion_id($companion->id);
					
					if($lastUpdateWithPlayMessageUser && $lastUpdateWithPlayMessageUser->play_message)
					{
						$companionToLastPlayMessageOnUserUpdate[$companion->id]['update'] = $lastUpdateWithPlayMessageUser;
						$companionToLastPlayMessageOnUserUpdate[$companion->id]['timeElapsed'] = $this->humanTiming($lastUpdateWithPlayMessageUser->created_at);
					}
					
					$lastUpdateWithSaid = $this->Companion_model->get_latest_said_update_by_companion_id($companion->id);
					if($lastUpdateWithSaid)
					{
						log_message('info', "lastUpdateWithSaid exists - get_latest_said_update_by_companion_id: ".$companion->id);
						$said = $this->Companion_model->get_audio('id', $lastUpdateWithSaid->last_said_id);
						if($said)
						{
							log_message('info', "lastUpdateWithSaid exists - get_audio('id', lastUpdateWithSaid->last_said_id): ".$lastUpdateWithSaid->last_said_id);
							$companionToLastSaid[$companion->id]['update'] = $lastUpdateWithSaid;
							$companionToLastSaid[$companion->id]['timeElapsed'] = $this->humanTiming($lastUpdateWithSaid->created_at);
							$companionToLastSaid[$companion->id]['text'] = $said->text;
							
							$playerData = array('audioNum' => $said->audio_num, 'audioText' => $said->text, 'audioURL' => $said->audio_url);
							
							log_message('info', "playerData: ".json_encode($playerData));

							$companionToLastSaid[$companion->id]['player'] = $this->load->view('audio/player', $playerData, TRUE);
						}
					}
					
					$messagesSaidUpdates = $this->Companion_model->get_message_said_updates_by_companion_id($companion->id);
					if($messagesSaidUpdates)
					{
						$companionToMessagesSaidUpdates[$companion->id] = $messagesSaidUpdates;
					}
					
					$lastUpdateWithEmotion = $this->Companion_model->get_latest_emotion_update_by_companion_id($companion->id);
					if($lastUpdateWithEmotion)
					{
						$companionToLastUpdateWithEmotion[$companion->id]['update'] = $lastUpdateWithEmotion;
						$companionToLastUpdateWithEmotion[$companion->id]['timeElapsed'] = $this->humanTiming($lastUpdateWithEmotion->created_at);
						$emotionUpdates = $this->Companion_model->get_emotion_updates_by_companion_id($companion->id);
						$companionToEmotionUpdates[$companion->id] = $emotionUpdates;
					}
					
					if($companion->emergency_alert)
					{
						$hasAlerts = true;
						$lastUpdateWithEmergency = $this->Companion_model->get_latest_emergency_update_by_companion_id($companion->id);
						if($lastUpdateWithEmergency)
						{
							$companionToLastEmergencyUpdate[$companion->id]['update'] = $lastUpdateWithEmergency;
							$companionToLastEmergencyUpdate[$companion->id]['timeElapsed'] = $this->humanTiming($lastUpdateWithEmergency->created_at);
						}
					}
					
					if($companion->curfew_alert)
					{
						$hasAlerts = true;
						$companionToLastCurfewUpdate[$companion->id] = $companionToLastUpdate[$companion->id];
					}
				}
			}
		} 
		
		$this->data['leader'] = $isTeamLeader;
		$this->data['groups'] = $groups;
		$this->data['companions'] = $companions;
		$this->data['groupToCompanion'] =  $groupToCompanion;
		$this->data['companionToGroup'] =  $companionToGroup;
		$this->data['companionToEmotionUpdates'] =  $companionToEmotionUpdates;
		$this->data['companionToLastUpdate'] = $companionToLastUpdate;
		$this->data['companionToLastChargingUpdate'] = $companionToLastChargingUpdate;
		$this->data['companionToLowBattery'] = $companionToLowBattery;
		$this->data['companionToLastPlayMessageOnUserUpdate'] = $companionToLastPlayMessageOnUserUpdate;
		$this->data['companionToLastSaid'] = $companionToLastSaid;
		$this->data['companionToMessagesSaidUpdates'] = $companionToMessagesSaidUpdates;
		$this->data['companionToLastUpdateWithEmotion'] = $companionToLastUpdateWithEmotion;
		$this->data['companionToLastEmergencyUpdate'] = $companionToLastEmergencyUpdate;
		$this->data['companionToLastCurfewUpdate'] = $companionToLastCurfewUpdate;
		$this->data['hasAlerts'] = $hasAlerts;
		
		$this->session->set_userdata('companions', $companions);
		$this->session->set_userdata('companionToLastUpdate', $companionToLastUpdate);

		$this->_render_page('dashboard/widgets', $this->data);
	}
	
	public function getMessages()
	{
		if(!$this->input->is_ajax_request())
		{
			//redirect them to sign in
			redirect('/', 'refresh');
			return;
		}
		
		if (!$this->ion_auth->logged_in())
		{
			$this->output->set_status_header('401');
			$this->output->set_output(json_encode(null));
			return;
		}
		
		$this->load->model('Companion_model');
		
		$messages = $this->Companion_model->get_messages();
		
		if(!$messages)
		{
			$this->output->set_status_header('401');
			$this->output->set_output(json_encode(null));
			return;
		}
		else
		{
			if(!is_array($messages))
				$messages = array($messages);
		}
			
		$this->output->set_output(json_encode($messages));
	}
	
	public function getAudioPlayer()
	{
		if(!$this->input->is_ajax_request())
		{
			//redirect them to sign in
			redirect('/', 'refresh');
			return;
		}
		
		if (!$this->ion_auth->logged_in())
		{
			$this->output->set_status_header('401');
			$this->output->set_output(json_encode(null));
			return;
		}
		
		$audioId = $this->input->get('id', TRUE);
		
		if(!$audioId)
		{
			$this->output->set_status_header('401');
			$this->output->set_output(json_encode(null));
			return;
		}
		
		$this->load->model('Companion_model');
		$audio = $this->Companion_model->get_audio('companion_says_id', $audioId);
		if($audio) 
		{
			$playerData = array('audioNum' => $audio->audio_num, 'audioText' => $audio->text, 'audioURL' => $audio->audio_url);
			$this->output->set_output($this->load->view('audio/player', $playerData, TRUE));
			return;
		}
		else 
		{
			$this->output->set_status_header('401');
			$this->output->set_output(json_encode($audio));
			return;
		}
	}
	
	public function sendAudioMessage()
	{
		if(!$this->input->is_ajax_request())
		{
			//redirect them to sign in
			redirect('/', 'refresh');
			return;
		}
		
		if (!$this->ion_auth->logged_in())
		{
			$this->output->set_status_header('401');
			$this->output->set_output(json_encode(null));
			return;
		}
		
		$audioId = $this->input->get('audioId', TRUE);
		$companionId = $this->input->get('companionId', TRUE);
		
		if(!$audioId || !$companionId)
		{
			$this->output->set_status_header('401');
			$this->output->set_output(json_encode(null));
			return;
		}
		else
		{
			$this->load->model('Companion_model');
			$groupId = $this->Companion_model->get_group_id_by_companion_id($companionId);
			
			if($groupId)
			{
				$group = $this->ion_auth->group($groupId)->row();
		
				//if there is no group attached to the companion, the user is not in the group, or there is not message with that id then error
				if(count($group) == 0 || !$this->ion_auth->in_group($group->name) || !$this->Companion_model->get_message($audioId))
				{
					$this->output->set_status_header('401');
					$this->output->set_output(json_encode(null));
					return;
				}
				else
				{
					$result = $this->Companion_model->set_pending_message($companionId, $audioId, $this->ion_auth->user()->row()->id);
					
					if($result)
					{
						$this->output->set_output(json_encode(null));
						return;
					}
					else
					{
						$this->output->set_status_header('401');
						$this->output->set_output(json_encode(null));
						return;
					}
				}
			}
			else
			{
				$this->output->set_status_header('401');
				$this->output->set_output(json_encode(null));
				return;
			}
		}
		
		$this->output->set_output(json_encode(null));
	}
	
	public function poll() {
		if(!$this->input->is_ajax_request())
		{
			//redirect them to sign in
			redirect('/', 'refresh');
			return;
		}
		
		if (!$this->ion_auth->logged_in())
		{
			$this->output->set_status_header('401');
			$this->output->set_output(json_encode(null));
			return;
		}

		$oldCompanions = $this->session->userdata('companions');
		$oldCompanionToLastUpdate = $this->session->userdata('companionToLastUpdate');

		$isTeamLeader = $this->ion_auth->is_admin();
		
		$groups = $this->ion_auth->get_users_groups()->result();
		$companions = array();
		$companionToLastUpdate = array();
		
		$this->load->model('Companion_model');
		
		foreach( $groups as $group )
		{
			$companion = $this->Companion_model->get_companion_by_group_id($group->id);
			if($companion)
			{
				array_push($companions, $companion);
				$lastUpdate = $this->Companion_model->get_latest_update_by_companion_id($companion->id);
				
				if($lastUpdate)
				{
					$companionToLastUpdate[$companion->id]['update'] = $lastUpdate;
				}
			}
		}

		/*
			For all companions associated with this user,
			If the last companion_updates record's created_at field
			Or the companions updated_at field is different then
			The last ones then return false as view is no longer clean
			Else return true as the view is clean
		*/

		//must exist
		if(!is_array($oldCompanions)) {
			$this->output->set_output(json_encode(false));
			return;
		}

		//must exist
		if(!is_array($oldCompanionToLastUpdate)) {
			$this->output->set_output(json_encode(false));
			return;
		}

		//must have the same number of elements
		if(count($oldCompanions) != count($companions)) {
			$this->output->set_output(json_encode(false));
			return;
		}

		$numCompanions = count($companions);
		for( $i = 0; $i < $numCompanions; $i++ ) {
			$oldCompanion = $oldCompanions[$i];
			$companion = $companions[$i];
			$oldCompanionId = $oldCompanion->id;
			$companionId = $companion->id;

			//must be comparing the same companions
			if($oldCompanionId != $companionId) {
				$this->output->set_output(json_encode(false));
				return;
			}

			//the last updated timestamp must be the same
			if($oldCompanion->updated_at != $companion->updated_at) {
				$this->output->set_output(json_encode(false));
				return;
			}
 
 			//could have received its first update
			if( !array_key_exists($oldCompanionId, $oldCompanionToLastUpdate) && array_key_exists($companionId, $companionToLastUpdate) )
			{
				$this->output->set_output(json_encode(false));
				return;
			}

			//if this companion has an update
			if( array_key_exists($oldCompanionId, $oldCompanionToLastUpdate) ) {
				$oldLastUpdate = $oldCompanionToLastUpdate[$oldCompanionId];
				$lastUpdate = $companionToLastUpdate[$companionId];

				//the ids must be the same (could have used created_at, but this is better)
				if($oldLastUpdate['update']->id != $lastUpdate['update']->id) {
					$this->output->set_output(json_encode(false));
					return;
				}
			}
		}

		$this->output->set_output(json_encode(true));
	}

	protected function humanTiming ($time)
	{
		$time = time() - strtotime($time); // to get the time since that moment
	
		$tokens = array (
			31536000 => 'year',
			2592000 => 'month',
			604800 => 'week',
			86400 => 'day',
			3600 => 'hour',
			60 => 'minute',
			1 => 'second'
		);
	
		foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
		}

		return '0 seconds';
	}
}