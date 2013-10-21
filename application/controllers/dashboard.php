<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	public function index()
	{
		if (!$this->ion_auth->logged_in())
		{
			//redirect them to sign in
			redirect('sign_in', 'refresh');
		}
		elseif (!$this->ion_auth->is_admin())
		{
			$this->load->view('partials/header', $this->headerViewData());
			$this->load->view('partials/footer');
		}
		else
		{
			/*
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			//list the users
			$this->data['users'] = $this->ion_auth->users()->result();
			
			foreach ($this->data['users'] as $k => $user)
			{
				$this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
			}
			
			$this->_render_page('user/index', $this->data);
			*/
			$this->load->view('partials/header', $this->headerViewData());
			$this->load->view('partials/footer');
		}
	}
}