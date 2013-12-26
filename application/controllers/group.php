<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Group extends MY_Controller {

	//display all groups or edit just the one if id
	public function index($id = NULL)
	{
		//if id and admin or social worker in the safety team then show the group view
		//if no id and admin show groups view (create, list: name (link) and description)
		//else redirect to the dashboard
		
		if (!$this->ion_auth->logged_in())
		{
			redirect('sign_in', 'refresh');
		}
		
		$noId = (!$id || empty($id));
		$isAdmin = $this->ion_auth->is_admin();
		$isGroupEditor = $this->ion_auth->is_group_editor();
		
		if( !($isAdmin || $isGroupEditor) )
			redirect('dashboard', 'refresh');
		
		if(!$noId) {
			
			$group = $this->ion_auth->group($id)->row();
		
			//if no group by that id
			if(count($group) == 0)
				redirect('groups', 'refresh');
			
			$groupName = $group->name;
			$isGroupEditable = $this->ion_auth->is_group_editable($groupName);
			$isGroupEditor = $this->ion_auth->in_group($groupName);
			
			if($isAdmin || $isGroupEditable && $isGroupEditor) 
			{
				$us = $this->ion_auth->getUsersAndSuperUsers($id);
		
				$this->data['is_admin'] = $this->ion_auth->is_admin();
 				$this->data['superUsers'] = $us[0];
				$this->data['users'] = $us[1];
		
				$companion = null;
		
				//validate form input
				if($isGroupEditable) {
					$this->form_validation->set_rules('group_name', 'Child\'s Name/Nickname', 'required|xss_clean');
				}
				
				$companion_update = true;
				$this->load->model('Companion_model');
				$companion = $this->Companion_model->get_companion_by_group_id($id);
				if($companion)
					$this->form_validation->set_rules('companion_name', 'Safety Sam\'s Nickname', 'required|xss_clean');	
					
				$this->form_validation->set_rules('group_description', 'Team Description', 'required|xss_clean');
		
				if (isset($_POST) && !empty($_POST))
				{
					if ($this->form_validation->run() === TRUE)
					{
						if($isGroupEditable)
							$group_update = $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description']);
						else
							$group_update = $this->ion_auth->update_group($id, FALSE, $_POST['group_description']);
		
						if(!$group_update)
							$this->session->set_flashdata('message', $this->ion_auth->errors());
						
						$companion_update = $this->Companion_model->change_name($companion->id, $_POST['companion_name']);
						
						if(!$companion_update) {
							$this->data['message'] = $this->lang->line('group_change_companion_name_failed');
						}
						
						if($companion_update && $group_update) {
							$this->session->set_flashdata('message', $this->lang->line('group_update_successful'));
							redirect('group/'.$id.'#basic', 'refresh');
						}
					}
				}
				
				//set the flash data error message if there is one
				if(!$companion_update)
					$this->data['message'] .= '<br/>';
				else
					$this->data['message'] = '';
				$this->data['message'] .= (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
		
				//pass the group to the view
				$this->data['group'] = $group;
				
				$this->data['group_name'] = array(
					'name'  => 'group_name',
					'id'    => 'group_name',
					'type'  => 'text',
					'value' => $this->form_validation->set_value('group_name', $group->name),
				);
				
				if(!$isGroupEditable)
					$this->data['group_name']['readonly'] = 'readonly';
				
				if($companion) {
					$this->data['companion_name'] = array(
						'name'  => 'companion_name',
						'id'    => 'companion_name',
						'type'  => 'text',
						'value' => $this->form_validation->set_value('companion_name', $companion->name),
					);
				}
				
				$this->data['group_description'] = array(
					'name'  => 'group_description',
					'id'    => 'group_description',
					'type'  => 'text',
					'value' => $this->form_validation->set_value('group_description', $group->description),
				);
				
				$this->data['is_group_editable'] = $isGroupEditable;
				$this->data['group_id'] = $id;
		
				$this->_render_page('group/edit', $this->data);
			}
			else {
				redirect('dashboard', 'refresh');
			}
		}
		else {
			if($this->ion_auth->is_admin())
			{
				$groups = $this->ion_auth->groups()->result();
			}
			else 
			{
				$grps = $this->ion_auth->get_users_groups()->result();
				$groups = array();
				foreach ($grps as $grp)
				{
					if($this->ion_auth->is_group_editable($grp->name))
					{
						array_push($groups,$grp);
					}
				}
			}
			
			$this->data['is_admin'] = $this->ion_auth->is_admin();
			$this->data['groups'] = $groups;

			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			

			$this->_render_page('group/groups', $this->data);
		}
	}
	
	// create a new safety team
	public function create()
	{
		if (!$this->ion_auth->logged_in())
		{
			redirect('sign_in', 'refresh');
		}
		
		if (!$this->ion_auth->is_admin())
		{
			redirect('dashboard', 'refresh');
		}

		//validate form input
		$this->form_validation->set_rules('group_name', 'Child\'s Name/Nickname', 'required|xss_clean');
		$this->form_validation->set_rules('description', 'Team Description', 'required|xss_clean');
		$this->form_validation->set_rules('companion', 'Therapuetic Companions (Unassigned)', 'required|xss_clean');
		$this->form_validation->set_rules('companion_name', 'Safety Sam\'s Nickname', 'required|xss_clean');
		$this->form_validation->set_rules('leaders', 'Team Leaders', 'required|xss_clean');

		//load companions
		$this->load->model('Companion_model');
		$companions = $this->Companion_model->get_unassigned_companions()->result();
		$companionId = $this->input->post('companion');
		$this->data['companion_id'] = $companionId;
		$this->data['companions'] = $companions;
		
		//load group leaders
		$us = $this->ion_auth->getUsersAndSuperUsers();
		$groupLeaders = $us[0];
		$currentLeaders = $this->input->post('leaders');
		
		if(!$currentLeaders)
			$currentLeaders = array();
		
		$this->data['groupLeaders'] = $groupLeaders;
		$this->data['currentLeaders'] = $currentLeaders;
		
		$companion_update = true;
		if ($this->form_validation->run() == TRUE)
		{
			$companion = null;
			foreach($companions as $c)
			{
				if($companionId == $c->id)
				{
					$companion = $c;
					break;
				}
			}
			if($companion)
			{
				$new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
				if($new_group_id)
				{
					if( $this->Companion_model->assignCompanionToGroup($companion->id, $new_group_id) )
					{
						foreach($currentLeaders as $l)
						{
							$this->ion_auth->add_to_group($new_group_id, $l);
						}
						
						//only successful if no ion auth errors
						if(!$this->ion_auth->errors())
						{
							$companion_update = $this->Companion_model->change_name($companion->id, $_POST['companion_name']);
						
							if(!$companion_update) {
								$this->ion_auth->delete_group($new_group_id);
								$this->data['message'] = $this->lang->line('group_change_companion_name_failed');
							}
							else {
								// check to see if we are creating the group
								// redirect them back to the admin page
								$this->session->set_flashdata('message', $this->ion_auth->messages());
								redirect('groups', 'refresh');
							}
						}
						else
						{
							$this->ion_auth->delete_group($new_group_id);
						}
					}
					else {
						$this->ion_auth->delete_group($new_group_id);
						$this->session->set_flashdata('message', $this->lang->line('group_assign_companion_failed'));
					}
				}
			}
			else
				$this->session->set_flashdata('message', $this->lang->line('group_assign_companion_failed'));
		}
		
		
		//set the flash data error message if there is one
		if(!$companion_update)
			$this->data['message'] .= '<br/>';
		else
			$this->data['message'] = '';
		$this->data['message'] .= (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		$this->data['group_name'] = array(
			'name'  => 'group_name',
			'id'    => 'group_name',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('group_name'),
		);
		$this->data['description'] = array(
			'name'  => 'description',
			'id'    => 'description',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('description'),
		);

		$this->data['companion_name'] = array(
			'name'  => 'companion_name',
			'id'    => 'companion_name',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('companion_name'),
		);

		$this->_render_page('group/create', $this->data);
	}
	
	//create a new user via invitation to the group
	public function invite($id = NULL)
	{
		if (!$this->ion_auth->logged_in())
		{
			redirect('sign_in', 'refresh');
		}
		
		//if no id
		if(!$id)
			redirect('groups', 'refresh');
		
		$group = $this->ion_auth->group($id)->row();
		
		//if no group by that id
		if(count($group) == 0)
			redirect('groups', 'refresh');
			
		$isAdmin = $this->ion_auth->is_admin();
		$groupName = $group->name;
		$isGroupEditable = $this->ion_auth->is_group_editable($groupName);
		$isGroupEditor = $this->ion_auth->in_group($groupName);
		
		if($isAdmin || $isGroupEditable && $isGroupEditor) 
		{
			//validate form input
			$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
			$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
			$this->form_validation->set_rules('email', 'Email Address', 'required|valid_email');
			$this->form_validation->set_rules('phone1', 'First Part of Phone', 'required|xss_clean|min_length[3]|max_length[3]');
			$this->form_validation->set_rules('phone2', 'Second Part of Phone', 'required|xss_clean|min_length[3]|max_length[3]');
			$this->form_validation->set_rules('phone3', 'Third Part of Phone', 'required|xss_clean|min_length[4]|max_length[4]');
	
			if ($this->form_validation->run() == true)
			{
				$username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
				$email    = $this->input->post('email');
	
				$additional_data = array(
					'first_name' => $this->input->post('first_name'),
					'last_name'  => $this->input->post('last_name'),
					'phone'      => $this->input->post('phone1') . '-' . $this->input->post('phone2') . '-' . $this->input->post('phone3'),
				);
			}
			
			$group_ids = array($id);
			
			if ($this->form_validation->run() == true && $this->ion_auth->invite($username, $email, $additional_data, $group_ids))
			{
				//redirect them back to the group edit page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect('group/'.$id.'#add-invite', 'refresh');
			}
			else
			{
				//display the create user form
				//set the flash data error message if there is one
				$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
				$this->data['group_id'] = $id;
	
				$this->data['first_name'] = array(
					'name'  => 'first_name',
					'id'    => 'first_name',
					'type'  => 'text',
					'value' => $this->form_validation->set_value('first_name'),
				);
				$this->data['last_name'] = array(
					'name'  => 'last_name',
					'id'    => 'last_name',
					'type'  => 'text',
					'value' => $this->form_validation->set_value('last_name'),
				);
				$this->data['email'] = array(
					'name'  => 'email',
					'id'    => 'email',
					'type'  => 'text',
					'value' => $this->form_validation->set_value('email'),
				);
				$this->data['phone1'] = array(
					'name'  => 'phone1',
					'id'    => 'phone1',
					'type'  => 'text',
					'value' => $this->form_validation->set_value('phone1'),
				);
				$this->data['phone2'] = array(
					'name'  => 'phone2',
					'id'    => 'phone2',
					'type'  => 'text',
					'value' => $this->form_validation->set_value('phone2'),
				);
				$this->data['phone3'] = array(
					'name'  => 'phone3',
					'id'    => 'phone3',
					'type'  => 'text',
					'value' => $this->form_validation->set_value('phone3'),
				);
	
				$this->_render_page('group/invite', $this->data);
			}
		}
		else {
			redirect('dashboard', 'refresh');
		}
	}
	
	//remove the user from the group
	public function remove($id = NULL, $userId = NULL)
	{
		if (!$this->ion_auth->logged_in())
		{
			redirect('sign_in', 'refresh');
		}
		
		//if no id
		if(!$id || !$userId)
			redirect('groups', 'refresh');
		
		$group = $this->ion_auth->group($id)->row();
		
		//if no group by that id
		if(count($group) == 0)
			redirect('groups', 'refresh');
		
		$isAdmin = $this->ion_auth->is_admin();
		$groupName = $group->name;
		
		//no user in the group
		if(!$this->ion_auth->in_group($groupName,$userId))
		{
			$this->session->set_flashdata('message', $this->lang->line('group_update_successful'));
			redirect('group/'.$id, 'refresh');
		}
		
		$isGroupEditable = $this->ion_auth->is_group_editable($groupName);
		$isGroupEditor = $this->ion_auth->in_group($groupName);
		$isRemoveUserAdmin = $this->ion_auth->is_admin($userId);
		$isRemoveUserGroupEditor = $this->ion_auth->is_group_editor($userId);
		
		if($isAdmin || $isGroupEditable && $isGroupEditor && !$isRemoveUserAdmin && !$isRemoveUserGroupEditor) 
		{
			//TODO:  Make sure this is a transaction by putting it in ion auth library
			if( !($isRemoveUserAdmin || $isRemoveUserGroupEditor) || count($this->ion_auth->getUsersAndSuperUsers($id)[0]) > 1)
			{
				$this->ion_auth->remove_from_group($id, $userId);
				$this->session->set_flashdata('message', $this->lang->line('group_update_successful'));
			}
			else
			{
				$this->session->set_flashdata('message', $this->lang->line('group_not_enough_group_leaders'));
			}
			
			redirect('group/'.$id, 'refresh');
		}
		else {
			redirect('dashboard', 'refresh');
		}
	}
	
	public function add($id = NULL)
	{
		if (!$this->ion_auth->logged_in())
		{
			redirect('sign_in', 'refresh');
		}
		
		//if no id
		if(!$id)
			redirect('groups', 'refresh');
		
		$group = $this->ion_auth->group($id)->row();
		
		//if no group by that id
		if(count($group) == 0)
			redirect('groups', 'refresh');
			
		$isAdmin = $this->ion_auth->is_admin();
		$groupName = $group->name;
		$isGroupEditable = $this->ion_auth->is_group_editable($groupName);
		$isGroupEditor = $this->ion_auth->in_group($groupName);
		
		if($isAdmin || $isGroupEditable && $isGroupEditor) 
		{
			$us = $this->ion_auth->getUsersAndSuperUsers($id, true);
			
			$groupLeaders = $us[0];
			$groupMembers = $us[1];
			
			$this->data['groupLeaders'] = $groupLeaders;
			$newLeaders = $this->input->post('leaders');
			if(!$newLeaders)
				$newLeaders = array();
			$this->data['newLeaders'] = $newLeaders;
			
			$this->data['groupMembers'] = $groupMembers;
			$newMembers = $this->input->post('members');
			if(!$newMembers)
				$newMembers = array();
			$this->data['newMembers'] = $newMembers;
			
			$this->form_validation->set_rules('leaders', 'Add Group Leaders', 'xss_clean');
			$this->form_validation->set_rules('members', 'Add Group Members', 'xss_clean');
			
			if ($this->form_validation->run() == true)
			{
				foreach($newLeaders as $l)
				{
					$this->ion_auth->add_to_group($id, $l);
				}
				
				foreach($newMembers as $m)
				{
					$this->ion_auth->add_to_group($id, $m);
				}
				
				if(!$this->ion_auth->errors())
					$this->session->set_flashdata('message', $this->lang->line('group_update_successful'));
				
				//set the flash data error message if there is one
				$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
				
				redirect('group/'.$id.'#add-invite', 'refresh');
			}
			else
			{
				//set the flash data error message if there is one
				$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
				
				$this->data['is_admin'] = $isAdmin;
				$this->data['id'] = $id;
				
				$this->_render_page('group/add_users', $this->data);
			}
		}
		else {
			redirect('dashboard', 'refresh');
		}
	}
	
}