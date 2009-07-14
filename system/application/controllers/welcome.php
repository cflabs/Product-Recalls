<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();	
	}
	
	function index()
	{
		if ( WWW_CACHE_ACTIVE ) {
			$this->output->cache(WWW_CACHE);
		}
		$this->load->helper('url_helper');
		$this->load->helper('text_helper');
		$this->load->helper('form_helper');
		$this->load->helper('typography_helper');
    	$this->load->model('Recall_model','',TRUE);
    	$this->load->model('Category_model','',TRUE);
    	$data['recalls'] = $this->Recall_model->get_recent_entries(5);
    	$data['categories'] = $this->Category_model->category_list_dropdown();
    	$data['categories_slug'] = $this->Category_model->category_list_dropdown_slug();
    	
    	$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
    	$page_data['page_title'] = "Welcome to ".SITE_NAME." (BETA) - ".SITE_TAGLINE;
    	$page_data['feeds']= array();
    	$page_data['menu'] = 'default';
    	$this->load->view('header',$page_data);
		$this->load->view('welcome_message',$data);
		$this->load->view('footer');
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */