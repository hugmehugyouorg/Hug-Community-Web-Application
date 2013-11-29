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
		$companionToFirstUpdate = array();
		$hasAlerts = false;
		$this->load->model('Companion_model');
		foreach( $groups as $group )
		{
			if(!$isTeamLeader)
				$isTeamLeader = $this->ion_auth->is_group_editor($group->id);
				
			$companion = $this->Companion_model->get_companion_by_group_id($group->id);
			if($companion)
			{
				array_push($companions, $companion);
				$groupToCompanion[$group->id] = $companion;
				$companionToGroup[$companion->id] = $group;
				$updates = $this->Companion_model->get_updates_by_companion_id($companion->id);
				if($updates)
				{
					$companionToUpdates[$companion->id] = $updates;
					$companionToFirstUpdate[$companion->id] = $updates[0];
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
		$this->data['companionToFirstUpdate'] = $companionToFirstUpdate;
		$this->data['hasAlerts'] = $hasAlerts;
		
		$this->_render_page('dashboard/widgets', $this->data);
	}
}