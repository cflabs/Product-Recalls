<?php

class About extends Controller {

	function Welcome()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$this->load->model('Recall_model','',TRUE);
		$this->load->helper('url_helper');
    	$page_data['page_title'] = "About ".SITE_NAME;
    	$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
    	$page_data['feeds']= array();
    	$page_data['menu'] = 'about';
    	$this->load->view('header',$page_data);
		$this->load->view('about');
		$this->load->view('footer');
	}
	
	function example() {
		$this->load->model('Recall_model','',TRUE);
		$this->load->helper('url_helper');
		$page_data['page_title']= "Example Email Alert | ".SITE_NAME;
		$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
		$page_data['feeds'] = array();
		$page_data['menu'] = 'about';
		$this->load->view('header',$page_data);
		$this->load->view('example_email');
		$this->load->view('footer');
	}
	
	function feedback() {
		$this->load->model('Recall_model','',TRUE);
		$this->load->helper('url_helper');
		$page_data['page_title']= "Send Us Your Feedback | ".SITE_NAME;
		$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
		$page_data['feeds'] = array();
		$page_data['menu'] = 'feedback';
		$this->load->view('header',$page_data);
		$this->load->view('feedback');
		$this->load->view('footer');
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/about.php */