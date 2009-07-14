<?php
/**
 * Welcome Class
 * 
 * @package Product Recalls
 * @subpackage Controllers
 * @category Welcome
 * @author Consumer Focus Labs
 * @link http://www.consumerfocuslabs.org
 */
class Welcome extends Controller {

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
		$this->load->helper('form_helper');
		//TO DO: autoload please!
	  	$this->load->helper('url_helper');
	  	$this->load->helper('typography_helper');
	  	$this->load->helper('date_helper');
	  	$this->load->helper('text_helper');
	    $this->load->model('Recall_model','',TRUE);
	    $this->load->model('Category_model','',TRUE);
	}
	
	/**
	 * Show the homepage
	 * 
	 * @access public
	 */
	function index()
	{
		//if the cache is turned on, then cache the homepage
		if ( WWW_CACHE_ACTIVE ) {
			$this->output->cache(WWW_CACHE);
		}

		//load the most recent recalls from the database
    	$data['recalls'] = $this->Recall_model->get_recent_entries(5);
    	//load a list of categories for use in the signup form (name/id)
    	$data['categories'] = $this->Category_model->category_list_dropdown();
    	//load a list of categories for use in the search form (name/slug)
    	$data['categories_slug'] = $this->Category_model->category_list_dropdown_slug();
    	//load page data
    	$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
    	$page_data['page_title'] = "Welcome to ".SITE_NAME." (BETA) - ".SITE_TAGLINE;
    	$page_data['feeds']= array();
    	$page_data['menu'] = 'default';
    	//display views
    	$this->load->view('header',$page_data);
		$this->load->view('welcome_message',$data);
		$this->load->view('footer');
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */