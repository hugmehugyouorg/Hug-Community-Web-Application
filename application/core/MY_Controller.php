<?php
/**
 * Abstract Controller
 *
 * @author Andrew Welters
 */
class MY_Controller extends CI_Controller {
	public $title = "&hearts;";

	public function  __construct() {
		parent::__construct();

        $this->load->library('ion_auth');
		$this->load->library('session');
		$this->load->database();
		$this->load->helper('url');
	}

	public function headerViewData()
	{
		$this->load->library('session');
		
		$headerData = array();
		$headerData['title'] = $this->title;
		$headerData['bodyID'] = strtolower($this->router->fetch_class() .'-'.$this->router->fetch_method());
        $headerData['user'] = $this->ion_auth->logged_in() ? $this->ion_auth->user()->row() : FALSE;

		return $headerData;
	}
}