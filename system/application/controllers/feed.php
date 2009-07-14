<?php
/**
 * Feed Class
 * 
 * @package Product Recalls
 * @subpackage Controllers
 * @category Feeds
 * @author Consumer Focus Labs
 * @link http://www.consumerfocuslabs.org
 */
class Feed extends Controller {

	/**
	 * Constructor
	 * 
	 * @access public
	 */
  	function Feed() {
  		//load parent
  		parent::Controller();

		//load in some helpers
  		$this->load->helper('xml_helper');		
		//TO DO: autoload please!
  		$this->load->model('Recall_model', '', TRUE);
  		$this->load->model('Category_model', '', TRUE);
  		$this->load->helper('url_helper');
  		$this->load->helper('date_helper');
  		$this->load->helper('text_helper');
  	}

	/**
	 * Provides the latest recalls as an RSS feed
	 * 
	 * NOTE: Possibly useless, if we are scraping in 40+ each
	 * 		 week 30+ of them are never going to make it into the
	 * 		 feed
	 * 
	 * @access public
	 */
  	function index()
  	{
  		//set up values
    	$data['encoding'] = 'utf-8';
    	$data['feed_name'] = FEED_NAME;
    	$data['feed_url'] = WWW_SERVER;
    	$data['page_description'] = "Latest UK product recalls";
    	$data['page_language'] = 'en-gb';
    	$data['creator_email'] = 'team at consumerfocuslabs dot org';
    	
    	//get recalls
    	$data['posts'] = $this->Recall_model->get_recent_entries(FEED_ENTRIES,0,FALSE);
    	
    	//set header and load view
    	header("Content-Type: application/rss+xml");
    	$this->load->view('feed',$data);
  	}
  
	/**
	 * Provides a particular recall as an RSS feed
	 * 
	 * NOTE: Possibly useless, would be better as an API maybe
	 * 
	 * @access public
	 * @param string $url the unique slug of the recall
	 */
  	function view($url) {
  
  		//set up values
    	$data['encoding'] = 'utf-8';
    	$data['feed_name'] = SITE_NAME." RSS Feed";
    	$data['feed_url'] = WWW_SERVER;
    	$data['page_description'] = "UK product recall information";
    	$data['page_language'] = 'en-gb';
    	$data['creator_email'] = 'team at consumerfocuslabs dot org';
    	
    	//get recall from db
    	$data['posts'] = $this->Recall_model->get_recall($url);
    	
    	//set header and load view
    	header("Content-Type: application/rss+xml");
    	$this->load->view('feed',$data);
    
  	}
  
	/**
	 * Provides a search term as an RSS feed
	 * 
	 * @access public
	 * @param string $search_string the string to search the database for
	 * @deprecated 0.3 - 9 Jul 2009
	 */
  	function search($search_string="") {
  		redirect(site_url(array('feed','search','all',$search_string)),'location','301');
  		exit();
  		
  		//the following code has been deprectiated
  		//searches now include categories
  		
  		/*
  		//if search string is empty and submission box is not empty
  		if ($search_string == "" && $this->input->post('txtSearch',TRUE) != "") {
  			//set the search string value
			$search_string = $this->input->post('txtSearch',TRUE);
  		}
  	
  		//set up values
    	$data['encoding'] = 'utf-8';
    	$data['feed_name'] = SITE_NAME." RSS Feed";
    	$data['feed_url'] = WWW_SERVER;
    	$data['page_description'] = "Product recall search results for ".xml_convert($search_string);
    	$data['page_language'] = 'en-gb';
    	$data['creator_email'] = 'team at consumerfocuslabs dot org';
    
    	//get the appropriate recalls from the database
    	$data['posts'] = $this->Recall_model->search_recalls($search_string);
    
    	//set the header and load view
    	header("Content-Type: application/rss+xml");
    	$this->load->view('feed',$data);*/
  	}
  	
	/**
	 * Provides an RSS feed for a search term
	 * filtered by a particular category
	 * 
	 * @access public
	 * @param string $category the slug of the category
	 * @param string $term the saerch term
	 */
	 function search_category($category="all",$term="") {
	 	//$this->output->enable_profiler();
	 	//load category information
	 	$data['category'] = $this->Category_model->get_category($category,TRUE);
	 	
	    //if this is not a valid category, show 404 and exit!
		if (empty($data['category'])) {
			show_404(current_url());
		} else {
				
	  		//set up values
	    	$data['encoding'] = 'utf-8';
	    	$data['feed_name'] = SITE_NAME." RSS Feed";
	    	$data['feed_url'] = WWW_SERVER;
	    	$data['page_description'] = "Product recall search results for ".xml_convert($term)." in ".$data['category']->name;
	    	$data['page_language'] = 'en-gb';
	    	$data['creator_email'] = 'team at consumerfocuslabs dot org';
	    
	    	//get the appropriate recalls from the database
	    	$data['posts'] = $this->Recall_model->search_recalls_category($data['category']->id,$term,FEED_ENTRIES,0,FALSE);

	    	//set the header and load view
	    	header("Content-Type: application/rss+xml");
	    	$this->load->view('feed',$data);
			
		}
	 	
	 }
  	
  	/**
  	 * Provides an RSS feed for a particular category
  	 * 
  	 * @access public
  	 * @param string $category the slug for a particular category
  	 */
  	 function category($category="") {
  	 	
	    //load category information
	    $data['category'] = $this->Category_model->get_category($category);
	    
	    //if this is not a valid category, show 404 and exit!
		if (empty($data['category'])) {
			show_404(current_url());
		} else {
			
			//set up values
	    	$data['encoding'] = 'utf-8';
	    	$data['feed_name'] = SITE_NAME." RSS Feed";
	    	$data['feed_url'] = WWW_SERVER;
	    	$data['page_description'] = "Latest recalls of ".xml_convert($data['category']->name);
	    	$data['page_language'] = 'en-gb';
	    	$data['creator_email'] = 'team at consumerfocuslabs dot org';

			//get the appropriate recalls from the database
			$data['posts'] = $this->Recall_model->get_category_entries($category,FEED_ENTRIES,0,FALSE);
				
	    	//set the header and load view
	    	header("Content-Type: application/rss+xml");
	    	$this->load->view('feed',$data);
			 
		}
  	 	
  	 }
}
/* End of file feed.php */
/* Location: ./system/application/controllers/feed.php */