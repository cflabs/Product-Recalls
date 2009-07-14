<?php
/**
 * Categories Class
 * 
 * @package Product Recalls
 * @subpackage Controllers
 * @category Categories
 * @author Dafydd Vaughan, Consumer Focus Labs
 * @link http://www.consumerfocuslabs.org
 */
class Categories extends Controller {

	/**
	 * Constructor
	 * 
	 * @access public
	 */
	function Categories() {
		
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
	  
		//set up page data 
		$page_data['page_title'] = "Recall Categories | ".SITE_NAME;
		$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
		$page_data['feeds']= array();
		$page_data['menu'] = 'categories';
		
		//get list of categories
		$data['categories'] = $this->Category_model->category_list();
		
		//load views
    	$this->load->view('header', $page_data);
    	$this->load->view('categories/categories_list',$data);
    	$this->load->view('footer');

  	}
  	
  	/**
  	 * Displays list of recalls for a particular category
  	 * 
  	 * @access public
  	 * @param string $category category name
  	 */
  	function category_lookup($category="test") {
  		
  		//as this is essentially just the first page
  		//call the appropriate method with a page number of 0
  		$this->category_lookup_page($category,0);
  	}
  	
  	/**
  	 * Displays a list of recalls for a particular category
  	 * in paginated form SITE_ENTRIES_PER_PAGE items per page
  	 * 
  	 * @access public
  	 * @param string $category the slug of the category to display
  	 * @param integer $row the row to display from
  	 */
  	function category_lookup_page($category="test",$row=0) {

		//load pagination library
	    $this->load->library('pagination');
	   
	    //load category information
	    $data['category'] = $this->Category_model->get_category($category);
	    
	    //if this is not a valid category, show 404 and exit!
		if (empty($data['category'])) {
			show_404(current_url());
		} else { 
			
			//load all recalls for this category
			$data['recalls'] = $this->Recall_model->get_category_entries($category,SITE_ENTRIES_PER_PAGE,$row);
			
			//if there are no recalls to display, show 404 and exit!
		    if (count($data['recalls']) < 1) {
		    	show_404(current_url());
		    } else {
		    	
		    	//work out page number
		    	if ($row === 0) {
		    		$data['pagenum'] = "";
		    	} else {
		    		$pagenum = ($row / SITE_ENTRIES_PER_PAGE)+1;
		    		$data['pagenum'] = "Page ".$pagenum;
		    	}
		    	
		    	//produce page title
		    	$page_data['page_title'] = "Recalls of ".$data['category']->name." ";
		    	if ($row > 0) { $page_data['page_title'].="(Page ".$pagenum.") "; }
		    	$page_data['page_title'] .= "| ".SITE_NAME;
		    	
		    	//set other page items
				$page_data['feeds'][0]['feed_name']= 'Latest recalls for '.$data['category']->name;
				$page_data['feeds'][0]['feed_url']= site_url(array('feed','category',$category));
		    	$page_data['menu'] = 'categories';
		    	$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
		    	
		    	//load header template
		    	$this->load->view('header',$page_data);
		    	
		    	//set up pagination
		    	$config['base_url'] = site_url(array("category",$category,"page"));
		    	$config['total_rows'] = $this->Category_model->category_records_count($category);
				$config['per_page'] = SITE_ENTRIES_PER_PAGE;
				$config['num_links'] = 8;
				$config['uri_segment'] = 4;
				$this->pagination->initialize($config);
				$data['links'] = $this->pagination->create_links();
				
				//load main view
	    		$this->load->view('categories/category_recall_list', $data);
	    		
	    		//load footer view#
	    		$this->load->view('footer');
		    }//end count
			
			
		}//end empty test


  	}//end function
  
  
}

/* End of file categories.php */ 
/* Location: ./system/application/models/categories.php */ 