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
		$companionToLastQuietTimeOnUserUpdate = array();
		$companionToLastSaid = array();
		$companionToLastUpdateWithEmotion = array();
		$companionToLastEmergencyUpdate = array();
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
					
					$companionToLastChargingUpdate[$companion->id] = $companionToLastUpdate[$companion->id];
					
					if(!$lastUpdate->is_charging)
					{
						$lastUpdateWithLowBattery = $this->Companion_model->get_latest_low_battery_update_by_companion_id($companion->id);
						if($lastUpdateWithLowBattery)
						{
							$companionToLowBattery[$companion->id]['update'] = $lastUpdateWithLowBattery;
							$companionToLowBattery[$companion->id]['timeElapsed'] = $this->humanTiming($lastUpdateWithLowBattery->created_at);
						}
						else if(!$lastUpdateWithCharging->is_charging)
						{
							$companionToLowBattery[$companion->id] = $companionToLastChargingUpdate[$companion->id];
						}
						else
						{
							$companionToLowBattery[$companion->id] = $companionToLastUpdate[$companion->id];
						}
						
						$hasAlerts = true;
					}
					
					if($lastUpdate->quiet_time)
					{
						$lastUpdateWithQuietTimeUser = $this->Companion_model->get_latest_quiet_time_update_by_user_by_companion_id($companion->id);
						
						if($lastUpdateWithQuietTimeUser && $lastUpdateWithQuietTimeUser->quiet_time)
						{
							$companionToLastQuietTimeOnUserUpdate[$companion->id]['update'] = $lastUpdateWithQuietTimeUser;
							$companionToLastQuietTimeOnUserUpdate[$companion->id]['timeElapsed'] = $this->humanTiming($lastUpdateWithQuietTimeUser->created_at);
						}
					}
					
					$lastUpdateWithSaid = $this->Companion_model->get_latest_said_update_by_companion_id($companion->id);
					if($lastUpdateWithSaid)
					{
						$said = $this->Companion_model->get_audio('id', $lastUpdateWithSaid->last_said_id);
						if($said)
						{
							$companionToLastSaid[$companion->id]['update'] = $lastUpdateWithSaid;
							$companionToLastSaid[$companion->id]['timeElapsed'] = $this->humanTiming($lastUpdateWithSaid->created_at);
							$companionToLastSaid[$companion->id]['text'] = $said->text;
							
							$playerData = array('audioNum' => $said->audio_num, 'audioText' => $said->text, 'audioURL' => $said->audio_url);
							
							$companionToLastSaid[$companion->id]['player'] = $this->load->view('audio/player', $playerData, TRUE);
						}
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
		$this->data['companionToLastQuietTimeOnUserUpdate'] = $companionToLastQuietTimeOnUserUpdate;
		$this->data['companionToLastSaid'] = $companionToLastSaid;
		$this->data['companionToLastUpdateWithEmotion'] = $companionToLastUpdateWithEmotion;
		$this->data['companionToLastEmergencyUpdate'] = $companionToLastEmergencyUpdate;
		$this->data['hasAlerts'] = $hasAlerts;
		
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
			echo json_encode(null);
			return;
		}
		
		$this->load->model('Companion_model');
		
		$messages = $this->Companion_model->get_messages();
		
		if(!$messages)
		{
			$this->output->set_status_header('401');
			echo json_encode(null);
			return;
		}
		else
		{
			if(!is_array($messages))
				$messages = array($messages);
		}
			
		echo json_encode($messages);
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
			echo json_encode(null);
			return;
		}
		
		$audioId = $this->input->get('id', TRUE);
		
		if(!$audioId)
		{
			$this->output->set_status_header('401');
			echo json_encode(null);
			return;
		}
		
		$this->load->model('Companion_model');
		$audio = $this->Companion_model->get_audio('companion_says_id', $audioId);
		if($audio) 
		{
			$playerData = array('audioNum' => $audio->audio_num, 'audioText' => $audio->text, 'audioURL' => $audio->audio_url);
			echo $this->load->view('audio/player', $playerData, TRUE);
			return;
		}
		else 
		{
			$this->output->set_status_header('401');
			echo json_encode($audio);
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
			echo json_encode(null);
			return;
		}
		
		$audioId = $this->input->get('audioId', TRUE);
		$companionId = $this->input->get('companionId', TRUE);
		
		if(!$audioId || !$companionId)
		{
			$this->output->set_status_header('401');
			echo json_encode(null);
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
					echo json_encode(null);
					return;
				}
				else
				{
					$result = $this->Companion_model->set_pending_message($companionId, $audioId);
					
					if($result)
					{
						echo json_encode(null);
						return;
					}
					else
					{
						$this->output->set_status_header('401');
						echo json_encode(null);
						return;
					}
				}
			}
			else
			{
				$this->output->set_status_header('401');
				echo json_encode(null);
				return;
			}
		}
		
		echo json_encode(null);
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
	}
}