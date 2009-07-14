<?php
/**
 * Categories Class
 * 
 * @package Product Recalls
 * @subpackage Controllers
 * @category About
 * @author Dafydd Vaughan, Consumer Focus Labs
 * @link http://www.consumerfocuslabs.org
 */
class About extends Controller {

	/**
	 * Constructor
	 * 
	 * @access public
	 */
	function Welcome()
	{
		//load parent
		parent::Controller();
		
		//load in some helpers
		//TO DO: autoload please!
		$this->load->model('Recall_model','',TRUE);
		$this->load->helper('url_helper');
	}
	
	/**
	 * Displays "about us" information page
	 * 
	 * @access public
	 */
	function index()
	{
		//load page data
    	$page_data['page_title'] = "About ".SITE_NAME;
    	$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
    	$page_data['feeds']= array();
    	$page_data['menu'] = 'about';
    	//load views
    	$this->load->view('header',$page_data);
		$this->load->view('about');
		$this->load->view('footer');
	}
	
	/**
	 * Displays an example email view
	 * 
	 * @access public
	 */
	function example() {
		
		//load page data
		$page_data['page_title']= "Example Email Alert | ".SITE_NAME;
		$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
		$page_data['feeds'] = array();
		$page_data['menu'] = 'about';
		//load views
		$this->load->view('header',$page_data);
		$this->load->view('example_email');
		$this->load->view('footer');
	}
	
	/**
	 * Displays feedback request page
	 * 
	 * @access public
	 */
	function feedback() {
		//load page data
		$page_data['page_title']= "Send Us Your Feedback | ".SITE_NAME;
		$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
		$page_data['feeds'] = array();
		$page_data['menu'] = 'feedback';
		//load views
		$this->load->view('header',$page_data);
		$this->load->view('feedback');
		$this->load->view('footer');
	}
}

/* End of file about.php */
/* Location: ./system/application/controllers/about.php */