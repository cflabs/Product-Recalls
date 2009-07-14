<?php
class Recall extends Controller {

  function Recall() {
  	parent::Controller();

  }


  function index()
  {
  	/*$this->load->helper('url_helper');
    $this->load->model('Recall_model','',TRUE);
    $data['recalls'] = $this->Recall_model->get_recent_entries(SITE_ENTRIES_PER_PAGE,0);
    
    //var_dump($data);// exit; 
    $this->load->view('header');
    $this->load->view('recallview', $data);
    $this->load->view('footer');*/
    $this->page(0);
  }
  
  
  function view($url) {
  
  	
  
  	$this->load->helper('url_helper');
  	$this->load->helper('typography_helper');
  	$this->load->helper('date_helper');
    $this->load->model('Recall_model','',TRUE);
    $data['recalls'] = $this->Recall_model->get_recall($url);
    
    
    if (count($data['recalls']) < 1) {
    	show_404(current_url());
    } else {
    	$page_data['page_title'] = " | ".SITE_NAME;
    	if ($data['recalls'][0]['status'] == "removed") { $page_data['page_title'] = " (Removed)".$page_data['page_title']; }
    	if ($data['recalls'][0]['status'] == "updated") { $page_data['page_title'] = " (Updated)".$page_data['page_title']; }
		$page_data['page_title'] = $data['recalls'][0]['product_name'].$page_data['page_title'];
		if ($data['recalls'][0]['brand'] != "") { $page_data['page_title'] = $data['recalls'][0]['brand']." ".$page_data['page_title']; }
		$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
		$page_data['feeds']= array();
		$page_data['menu'] = 'default';
		
    	$this->load->view('header', $page_data);
    	$this->load->view('view_recall_item', $data);
    	$this->load->view('footer');
    }
    
  }
  
/*  function since($timestamp=0) {
  	$this->load->helper('url_helper');
	$this->load->helper('typography_helper');
	$this->load->helper('text_helper');
	$this->load->model('Recall_model','',TRUE);
	$data['recalls'] = $this->Recall_model->get_entries_since($timestamp);
		$page_data['page_title'] = "Recalls List | ".SITE_NAME;
    	$this->load->view('header',$page_data);
		$data['links'] = "";
    	$this->load->view('recallview', $data);
    	$this->load->view('footer');
	
	
  }*/
  
  function page($row=0) {
  	
  	
  	
  	$this->load->helper('url_helper');
	$this->load->helper('typography_helper');
	$this->load->helper('text_helper');
	$this->load->model('Recall_model','',TRUE);

    $this->load->library('pagination');
   
	$data['recalls'] = $this->Recall_model->get_recent_entries(SITE_ENTRIES_PER_PAGE,$row);
	
    if (count($data['recalls']) < 1) {
    	show_404(current_url());
    } else {
    	
    	if ($row === 0) {
    		$data['pagenum'] = "";
    	} else {
    		$pagenum = ($row / SITE_ENTRIES_PER_PAGE)+1;
    		$data['pagenum'] = "Page ".$pagenum;
    	}
    	
    	
		$page_data['page_title'] = "Latest UK Product Recalls ";
		if ( $row > 0 ) { $page_data['page_title'] .= "(Page ".$pagenum.") "; }
		$page_data['page_title'] .= "| ".SITE_NAME;
		$page_data['feeds']= array();
		$page_data['menu'] = 'default';
		$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
    	$this->load->view('header',$page_data);
		$config['base_url'] = site_url("recall/page/");
		$config['total_rows'] = $this->Recall_model->records();
		$config['per_page'] = SITE_ENTRIES_PER_PAGE;
		$config['num_links'] = 8;
		$this->pagination->initialize($config);
		$data['links'] = $this->pagination->create_links();
    	$this->load->view('recallview', $data);
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
  	$this->load->helper('url_helper');
  	
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
?>