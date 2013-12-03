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
		
		//get user groups
		//get companions by groups
		//display companion alerts or not
		//display every status ordered newest to oldest
		
		$groups = $this->ion_auth->get_users_groups()->result();
		$companions = array();
		$groupToCompanion = array();
		$companionToGroup = array();
		$companionToUpdates = array();
		$companionToLastUpdate = array();
		$companionToLowBattery = array();
		$companionToLastUpdateWithEmotion = array();
		$hasAlerts = false;
		$this->load->model('Companion_model');
		foreach( $groups as $group )
		{
			if(!$isTeamLeader)
				$isTeamLeader = $this->ion_auth->is_group_editor($group->name);
				
			$companion = $this->Companion_model->get_companion_by_group_id($group->id);
			if($companion)
			{
				array_push($companions, $companion);
				$groupToCompanion[$group->id] = $companion;
				$companionToGroup[$companion->id] = $group;
				$updates = $this->Companion_model->get_emotion_updates_by_companion_id($companion->id);
				if($updates)
				{
					$companionToUpdates[$companion->id] = $updates;
					$lastUpdate = $this->Companion_model->get_latest_update_by_companion_id($companion->id);
					$companionToLastUpdate[$companion->id]['update'] = $lastUpdate;
					$companionToLastUpdate[$companion->id]['timeElapsed'] = $this->humanTiming($lastUpdate->created_at);
					if(!$lastUpdate->is_charging && $lastUpdate->voltage <= 3.0)
					{
						$companionToLowBattery[$companion->id] = true;
						$hasAlerts = true;
					}
				}
				
				$lastUpdateWithEmotion = $this->Companion_model->get_latest_emotion_update_by_companion_id($companion->id);
				if($lastUpdateWithEmotion)
				{
					$companionToLastUpdateWithEmotion[$companion->id]['update'] = $lastUpdateWithEmotion;
					$companionToLastUpdateWithEmotion[$companion->id]['timeElapsed'] = $this->humanTiming($lastUpdateWithEmotion->created_at);
				}
				
				if($clearAlertId == $companion->id && $isTeamLeader)
				{
					if($this->Companion_model->clear_emergency_alert($clearAlertId))
					{
						redirect('dashboard', 'refresh');
					}
				}
				
				if($companion->emergency_alert)
				{
					$hasAlerts = true;
				}
			}
		} 
		
		$this->data['leader'] = $isTeamLeader;
		$this->data['groups'] = $groups;
		$this->data['companions'] = $companions;
		$this->data['groupToCompanion'] =  $groupToCompanion;
		$this->data['companionToGroup'] =  $companionToGroup;
		$this->data['companionToUpdates'] =  $companionToUpdates;
		$this->data['companionToLastUpdate'] = $companionToLastUpdate;
		$this->data['companionToLowBattery'] = $companionToLowBattery;
		$this->data['companionToLastUpdateWithEmotion'] = $companionToLastUpdateWithEmotion;
		$this->data['hasAlerts'] = $hasAlerts;
		
		$this->_render_page('dashboard/widgets', $this->data);
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