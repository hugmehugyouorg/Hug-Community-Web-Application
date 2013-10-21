<?php
/**
 * Abstract Controller
 *
 * @author Andrew Welters
 */
class MY_Controller extends CI_Controller {
	public $title = "&hearts;";

	function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->helper('url');

		// Load MongoDB library instead of native db driver if required
		$this->config->item('use_mongodb', 'ion_auth') ?
		$this->load->library('mongo_db') :

		$this->load->database();

		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
	}

	public function headerViewData($data=null)
	{
		$this->load->library('session');
		
		$headerData = array();
		$headerData['title'] = $this->title;
		$headerData['bodyID'] = strtolower($this->router->fetch_class() .'-'.$this->router->fetch_method());
        $headerData['user'] = $this->ion_auth->logged_in() ? $this->ion_auth->user()->row() : FALSE;
        $headerData['is_admin'] = $this->ion_auth->is_admin() ? TRUE : FALSE;
        $headerData['is_group_editor'] = $this->ion_auth->is_group_editor() ? TRUE : FALSE;
		$headerData['homeLink'] = $data && array_key_exists('homeLink',$data) ? $data['homeLink'] : 'dashboard';	
		$headerData['signInPage'] = $data && array_key_exists('signInPage',$data) ? ($data['signInPage'] != 'true' ? 'false' : 'true') : 'false';	
		
		return $headerData;
	}
	
	function _render_page($view, $data=null, $render=false)
	{
		$this->viewdata = (empty($data)) ? $this->data: $data;

		//standard layout if rendering
		if(!$render) {
			$this->load->view('partials/header', $this->headerViewData($this->viewdata));
			$view_html = $this->load->view($view, $this->viewdata, $render);
			$this->load->view('partials/footer');
		}
		else
			$view_html = $this->load->view($view, $this->viewdata, $render);

		if (!$render) return $view_html;
	}
}