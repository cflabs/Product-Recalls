<?php
/**
 * Recall Class
 * 
 * @package Product Recalls
 * @subpackage Controllers
 * @category Recall
 * @author Consumer Focus Labs
 * @link http://www.consumerfocuslabs.org
 */
class Recall extends Controller {

	/**
	 * Constructor
	 * 
	 * @access public
	 */
	function Recall() {
		//load parent
  		parent::Controller();
		//load in some helpers
		$this->load->helper('typography_helper');
		//TO DO: autoload please!
  		$this->load->model('Recall_model', '', TRUE);
  		$this->load->model('Category_model', '', TRUE);
  		$this->load->helper('url_helper');
  		$this->load->helper('date_helper');
  		$this->load->helper('text_helper');
  	}

	/**
	 * Lists all recalls held in the database
	 * 
	 * @access public
	 */
  	function index()
  	{
		//this is the first page of results
		//so call first page
    	$this->page(0);
  	}
  
	/**
	 * Show the details of a particular recall
	 * 
	 * @access public
	 * @param string $url the unique slug of the recall to display
	 */
  	function view($url) {
  
		//load the recall
    	$data['recalls'] = $this->Recall_model->get_recall($url);

		//if the recall doesn't exist, display a 404 error    
    	if (count($data['recalls']) < 1) {
    		show_404(current_url());
    	} else {
    		
    		//load page data
	    	$page_data['page_title'] = " | ".SITE_NAME;
	    	if ($data['recalls'][0]['status'] == "removed") { $page_data['page_title'] = " (Removed)".$page_data['page_title']; }
	    	if ($data['recalls'][0]['status'] == "updated") { $page_data['page_title'] = " (Updated)".$page_data['page_title']; }
			$page_data['page_title'] = $data['recalls'][0]['product_name'].$page_data['page_title'];
			if ($data['recalls'][0]['brand'] != "") { $page_data['page_title'] = $data['recalls'][0]['brand']." ".$page_data['page_title']; }
			$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
			$page_data['feeds']= array();
			$page_data['menu'] = 'default';
		
			//load views
	    	$this->load->view('header', $page_data);
	    	$this->load->view('view_recall_item', $data);
	    	$this->load->view('footer');
    	}
	}

	/**
	 * List a particular page of recalls
	 * from the database
	 * 
	 * @access public
	 * @param integer $row the row from which to start the display
	 */
	function page($row=0) {
  	
  		//load pagination library 	
    	$this->load->library('pagination');
   
   		//load recalls from the database
		$data['recalls'] = $this->Recall_model->get_recent_entries(SITE_ENTRIES_PER_PAGE,$row);
	
		//if there are no recalls to display, show a 404 error
    	if (count($data['recalls']) < 1) {
    		show_404(current_url());
    	} else {
    	
    		//if this is the first page, don't display "Page" on screen'
    		if ($row === 0) {
    			$data['pagenum'] = "";
    		} else {
    			$pagenum = ($row / SITE_ENTRIES_PER_PAGE)+1;
    			$data['pagenum'] = "Page ".$pagenum;
    		}
    		
    		//load page data
    		$page_data['page_title'] = "Latest UK Product Recalls ";
			if ( $row > 0 ) { $page_data['page_title'] .= "(Page ".$pagenum.") "; }
			$page_data['page_title'] .= "| ".SITE_NAME;
			$page_data['feeds']= array();
			$page_data['menu'] = 'default';
			$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
			
			//load header view
    		$this->load->view('header',$page_data);
    		
    		//setup pagination
			$config['base_url'] = site_url("recall/page/");
			$config['total_rows'] = $this->Recall_model->records();
			$config['per_page'] = SITE_ENTRIES_PER_PAGE;
			$config['num_links'] = 8;
			$this->pagination->initialize($config);
			$data['links'] = $this->pagination->create_links();
			
			//load main view
    		$this->load->view('recallview', $data);
    		
    		//load footer view
    		$this->load->view('footer');
    	}
  	}
  
	/**
	 * Searches for a term within the recalls database
	 * shows the results on the page
	 * 
	 * @access public
	 * @param string $search_string the string to search for
	 * @deprecated 0.3 - 9 Jul 2009
	 */
  	function search($search_string="") {
  	
		redirect(site_url(array('search','all',$search_string)),'location','301');
		exit;
	
		//this code has been depreciated as of 20090709
		//search now handled in search controller
		/*
	  	$this->load->library('form_validation');
	  	$this->load->helper('form');
	
		$this->load->helper('typography_helper');
		$this->load->helper('text_helper');
		$this->load->model('Recall_model','',TRUE);
		
		$valid = FALSE;
		
		$this->form_validation->set_rules('txtSearch','search','trim|required|xss_clean');
		
	 	if ($search_string == "") {
			$search_string = $this->input->post('txtSearch',TRUE);
			$valid = $this->form_validation->run(); 
	 	} else {
	 		$valid = TRUE;
	  	}
		
		if (!$valid) {
			$page_data['page_title'] = "Not a valid search | ".SITE_NAME;
			$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
			$page_data['feeds'] = array();
			$page_data['menu'] = 'default';
			$this->load->view('header',$page_data);
			$this->load->view('view_recall_search_empty');
			$this->load->view('footer');
		} else {
	
		  	$data['recalls'] = $this->Recall_model->search_recalls($search_string);
		  	$data['links'] = "";
		  	$data['search_string'] = $search_string;
		  	$data['result_count'] = count($data['recalls']);
		  	
			$page_data['page_title'] = $data['result_count']." results for search '".$search_string."' | ".SITE_NAME;
			$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
			$page_data['feeds'][0]['feed_name']= 'Search results feed for '.$search_string;
			$page_data['feeds'][0]['feed_url']= site_url(array('feed','search',$search_string));
			$page_data['menu'] = 'default';
		   	$this->load->view('header',$page_data);
		   	$this->load->view('view_recall_search', $data);
		   	$this->load->view('footer');
		}*/
	}
}
/* End of file recall.php */
/* Location: ./system/application/controllers/recall.php */