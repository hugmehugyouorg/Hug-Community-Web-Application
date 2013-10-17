<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
			redirect('auth/sign_in', 'refresh');
		}
		else if($this->ion_auth->is_admin()) {
			//redirect them to the admin page
			redirect('auth', 'refresh');
		}
		else {
			$this->load->view('partials/header', $this->headerViewData());
			$this->load->view('welcome_message');
			$this->load->view('partials/footer');
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */