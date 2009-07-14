<?php
/**
 * Search Class
 * 
 * @package Product Recalls
 * @subpackage Controllers
 * @category Search
 * @author Dafydd Vaughan, Consumer Focus Labs
 * @link http://www.consumerfocuslabs.org
 */
class Search extends Controller {

	/**
	 * Constructor
	 * 
	 * @access public
	 */
	function Search() {
		
		//load parent
		parent::Controller();
		
		//load in some helpers
		//TO DO: autoload please!
	  	$this->load->helper('url_helper');
	  	$this->load->helper('typography_helper');
	  	$this->load->helper('date_helper');
	  	$this->load->helper('text_helper');
	    $this->load->model('Recall_model','',TRUE);
	    $this->load->model('Category_model','',TRUE);
  	}


	/**
	 * Lists all categories in database
	 * 
	 * @access public
	 */
  	function index() {

		$this->load->library('form_validation');
	
		$valid = FALSE;
		
		$this->form_validation->set_rules('txtSearch','search','trim|required|xss_clean');
		$this->form_validation->set_rules('ddlCategory','category','trim|required|xss_clean');
		
		$search_string = $this->input->post('txtSearch',TRUE);
		$category = $this->input->post('ddlCategory',TRUE);
		$valid = $this->form_validation->run(); 

		if (!$valid) {
			$page_data['page_title'] = "Not a valid search | ".SITE_NAME;
			$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
			$page_data['feeds'] = array();
			$page_data['menu'] = 'default';
			$this->load->view('header',$page_data);
			$this->load->view('view_recall_search_empty');
			$this->load->view('footer');
		} else {
	
		  	$this->lookup($category,$search_string);
		}
  	}

  	
  	/**
  	 * Searches the recalls database for a particular term
  	 * filters by category
  	 * 
  	 * @access public
  	 * @param string $category the slug of the category to filter by
  	 * @param string $term the term to search for
  	 * 
  	 */
  	function lookup($category="all",$term="") {

  		//ass this is essentially just the first page
  		//call the appropriate method with a page number of 0
  		$this->lookup_page($category,$term,0);
  		
  	}
  	
  	/**
  	 * Searches the recalls database for a particular term
  	 * filters by category and jumps to a specific row
  	 * 
  	 * @access public
  	 * @param string $category the slug of the category to filter by
  	 * @param string $term the term to saerch for
  	 * @param integer $row the row to jump to 
  	 */
  	function lookup_page($category="all",$term="",$row=0) {
  		
		//load pagination library
	    $this->load->library('pagination');
	   
	    //load category information
	    $data['category'] = $this->Category_model->get_category($category,TRUE);

	    //if this is not a valid category, show 404 and exit!
		if (empty($data['category'])) {
			show_404(current_url());
		} else {
			
			
			//get recalls
			$data['recalls'] = $this->Recall_model->search_recalls_category($data['category']->id,$term,SITE_ENTRIES_PER_PAGE,$row);
			
		    //work out page number
		    if ($row === 0) {
		    	$data['pagenum'] = "";
		    } else {
		    	$pagenum = ($row / SITE_ENTRIES_PER_PAGE)+1;
		    	$data['pagenum'] = "Page ".$pagenum;
		    }
		    
		    
			//set default values
		  	$data['links'] = "";
		  	$data['search_string'] = $term;
		  	$data['result_count'] = $this->Recall_model->search_records_count($data['category']->id,$term);
			  	
			//set page data
			$page_data['page_title'] = $data['result_count']." results for search '".$term."' in ".$data['category']->name;
		    if ($row > 0) { $page_data['page_title'].=" (Page ".$pagenum.")"; }
		    $page_data['page_title'] .= " | ".SITE_NAME;
			$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
			$page_data['feeds'] = array();
			$page_data['feeds'][0]['feed_name']= 'Search results feed for '.$term.' (category: '.$data['category']->name.')';
			$page_data['feeds'][0]['feed_url']= site_url(array('feed','search',$category,$term));
			$page_data['menu'] = 'default';
			
			//load header view
		   	$this->load->view('header',$page_data);
		   	
	    	//set up pagination
	    	$config['base_url'] = site_url(array("search",$data['category']->slug,$term,"page"));
	    	$config['total_rows'] = $data['result_count'];
			$config['per_page'] = SITE_ENTRIES_PER_PAGE;
			$config['num_links'] = 8;
			$config['uri_segment'] = 5;
			$this->pagination->initialize($config);
			$data['links'] = $this->pagination->create_links();
	   	
			//load main view
	    	$this->load->view('view_recall_search', $data);
	    		
	    	//load footer view#
	    	$this->load->view('footer');
	    	
		}
  		
  		
  	}
  	

  
}

/* End of file search.php */ 
/* Location: ./system/application/controllers/search.php */ 