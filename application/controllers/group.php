<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Group extends MY_Controller {

	//display all groups or edit just the one if id
	public function index($id = null)
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
				//list the users
				$us = $this->ion_auth->users()->result();
				
				$users = array();
				$superUsers = array();
				foreach ($us as $user)
				{
					if( $user->active && $this->ion_auth->in_group($group->name,$user->id) )
					{
						if($this->ion_auth->is_admin($user->id))
							array_push($superUsers,$user);
						else if($this->ion_auth->is_group_editor($user->id))
							array_push($superUsers,$user);
						else
							array_push($users,$user);
					}
				}
		
				$this->data['is_admin'] = $this->ion_auth->is_admin();
 				$this->data['superUsers'] = $superUsers;
				$this->data['users'] = $users;
		
				//validate form input
				if($isGroupEditable)
					$this->form_validation->set_rules('group_name', 'Group name', 'required|alpha|xss_clean');
					
				$this->form_validation->set_rules('group_description', 'Group Description', 'required|xss_clean');
		
				if (isset($_POST) && !empty($_POST))
				{
					if ($this->form_validation->run() === TRUE)
					{
						if($isGroupEditable)
							$group_update = $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description']);
						else
							$group_update = $this->ion_auth->update_group($id, FALSE, $_POST['group_description']);
		
						if($group_update)
						{
							$this->session->set_flashdata('message', $this->lang->line('group_update_successful'));
							redirect('groups', 'refresh');
						}
						else
						{
							$this->session->set_flashdata('message', $this->ion_auth->errors());
						}
					}
				}
		
				//set the flash data error message if there is one
				$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
		
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
	function create()
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
		$this->form_validation->set_rules('group_name', 'Group name', 'required|alpha|xss_clean');
		$this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
		$this->form_validation->set_rules('companion', 'Therapuetic Companions (Unassigned)', 'required|xss_clean');
		$this->form_validation->set_rules('leaders', 'Group Leaders', 'required|xss_clean');

		//load companions
		$this->load->model('Companion_model');
		$companions = $this->Companion_model->get_unassigned_companions()->result();
		$companionId = $this->input->post('companion');
		$this->data['companion_id'] = $companionId;
		$this->data['companions'] = $companions;
		$companionAssociationError = false;
		
		//load group leaders
		$groupLeaders = $this->ion_auth->get_group_leaders()->result();
		$currentLeaders = $this->input->post('leaders');
		
		if(!$currentLeaders)
			$currentLeaders = array();
		
		$this->data['groupLeaders'] = $groupLeaders;
		$this->data['currentLeaders'] = $currentLeaders;
		$leaderAssociationError = false;
		
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
							// check to see if we are creating the group
							// redirect them back to the admin page
							$this->session->set_flashdata('message', $this->ion_auth->messages());
							redirect('groups', 'refresh');
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
		
		//display the create group form
		//set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

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

		$this->_render_page('group/create', $this->data);
	}
	
	//create a new user via invitation to the group
	function invite($id)
	{
		if (!$this->ion_auth->logged_in())
		{
			redirect('sign_in', 'refresh');
		}
		
		$group = $this->ion_auth->group($id)->row();
		
		//if no group by that id
		if(!$id || count($group) == 0)
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
				redirect('group/'.$id, 'refresh');
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
	function remove($id, $userId)
	{
		if (!$this->ion_auth->logged_in())
		{
			redirect('sign_in', 'refresh');
		}
		
		$group = $this->ion_auth->group($id)->row();
		
		//if no group by that id
		if(!$id || count($group) == 0)
			redirect('groups', 'refresh');
		
		$isAdmin = $this->ion_auth->is_admin();
		$groupName = $group->name;
		
		//no user in the group
		if(!$userId || !$this->ion_auth->in_group($groupName,$userId))
			redirect('groups', 'refresh');
		
		$isGroupEditable = $this->ion_auth->is_group_editable($groupName);
		$isGroupEditor = $this->ion_auth->in_group($groupName);
		
		if($isAdmin || $isGroupEditable && $isGroupEditor && !$this->ion_auth->is_admin($userId) && !$this->ion_auth->is_group_editor($userId)) 
		{
			$this->ion_auth->remove_from_group($id, $userId);
			$this->session->set_flashdata('message', $this->lang->line('group_update_successful'));
			redirect('group/'.$id, 'refresh');
		}
		else {
			redirect('dashboard', 'refresh');
		}
	}
	
}