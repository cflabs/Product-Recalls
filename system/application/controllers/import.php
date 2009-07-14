<?php 
 
class Import extends Controller {

	private $log = array();
	private $imported_counter = 0;
	private $recalls_counter = 0;

	function index() {
		
		//if this is being run through the main website
		//disallow and throw a 404 error
  		if(substr_count($_SERVER['SCRIPT_FILENAME'],'run_scraper.php') < 1) {
  			$this->load->helper('url_helper');
  			show_404(current_url());
  			exit;
  		}
  		
		//load helper and model
  		$this->load->helper('scraping_helper');
    	$this->load->model('Recall_model','',TRUE);
    	
   	
    	//scrape the content    
    	$this->scrape_ts();
    	
    	//send log email
    	$this->log_mail();
    	
    	//delete recent recalls cache if cache is active
    	if (WWW_CACHE_ACTIVE) {
    		$this->mp_cache->delete('recent-recalls');
    	}
    
		//load the view   
		$this->load->view('import');
	}
	
	private function log($message,$log_type='debug') {
		
		//print the message to the screen
		print $message."\n";
		//push it to the log
		array_push($this->log,$message);
		log_message($log_type,SCRAPER_EMAIL_LOG_PREFIX." ".$message);
		
		
	}
	
	private function log_mail($complete = TRUE) {
		
		//load the email helper
		//$this->load->helper('email_helper');
		$this->load->library('email');
		

		//set up the email		
		$this->email->from(EMAILER_ADDRESS,SITE_NAME);
		$this->email->to(SCRAPER_EMAIL_LOG);
		if ($complete) {
			$this->email->subject(SCRAPER_EMAIL_LOG_PREFIX." Scraped ".$this->imported_counter."/".$this->recalls_counter);
		} else {
			$this->email->subject(SCRAPER_EMAIL_LOG_PREFIX." ERROR");
		}
		$this->email->message(implode("\n",$this->log));
		
		//send the email
		$this->email->send();
		
	}
  
	private function scrape_ts() {
		
		//url info
		$url_prefix = "http://www.tradingstandards.gov.uk/navless/recall/";
		$url_page = "listing.asp";
		
		$link_regex = "/<a href=\"(.*?)\">.*?<\/a>/s";
		
		$this->log("starting scraping ({$url_prefix}{$url_page})");
		
		//get content of product list page
		try {
			$ts_html = scrape_content($url_prefix.$url_page);
		} catch (Exception $e) {
			$this->log("fatal error: ". $e->getMessage(),'error');
			$this->log_mail(FALSE);
			exit;
			
		}
		
		//grab all the links on the page
		preg_match_all($link_regex,$ts_html,$matches,PREG_PATTERN_ORDER);
		
		$this->log("found ".count($matches[1])." recalls to scrape");
		
		if (count($matches[1]) < 1) {
			$this->log("fatal error: no links to scrape",'error');
			$this->log_mail(FALSE);
			exit;
		}
		
		//for each URL on the page, scrape its content
		foreach($matches[1] as $recallitem) {
			
			$this->recalls_counter++;
			
			$this->log("-- getting scrape for ({$url_prefix}{$recallitem})");
			
			//try and scrape the content, if an error occurs
			//log it and move to next item
			try {
				//scrape
				$result = $this->scrape_ts_info($url_prefix.$recallitem);

	    		//does the record already exist?
	    		$exists = $this->Recall_model->id_exist($result["id"],1);
	    		
	
	    		if (!$exists) {
	    			$this->log("-- importing data for product (".$result["title"].")");
	    			
	    			//produce the unique url
					$internal_url = $this->produce_slug($result["title"],$result["id"]);
	    			//store in database
		    		$data = array(
		    			'product_name' => $result["title"],
		    			'description' => $result["content"],
		    			'external_url' => $url_prefix.$recallitem,
		    			'internal_url' => $internal_url,
		    			'source_id' => $result["id"],
		    			'source' => 1   			
		    		);
		    		$this->db->insert('recalls',$data);
		    		$this->imported_counter++;
	    		} else {
	    			$this->log("-- product already recorded");
	    		}
    		
			} catch (Exception $e) {
				$this->log("error: ". $e->getMessage(),'error');
			}
			sleep(SCRAPER_SLEEP);
			//exit;
			
		}
		
		$this->log("import complete (".$this->imported_counter."/".$this->recalls_counter.")");
		$this->log("end");
		
	}
	
	private function scrape_ts_info($url) {

		$return = array();

		//get id of item
		
		$id_regex = "/id=([0-9]*)/";
		
		preg_match_all($id_regex,$url,$id_matches,PREG_PATTERN_ORDER);
		
		if (count($id_matches[1]) == 1) {
			
			$return["id"] = 0+$id_matches[1][0];
			
		}
		
		
		
		//scrape content
		try {
			$html = scrape_content($url);
		} catch (Exception $e) {
			throw $e;
			exit;
		}
	
		//get title from recall
		
		$title_regex = "/<h2 class=\"iframe_content_news_heading\">(.*?)<\/h2>/s";
		
		preg_match_all($title_regex,$html,$title_matches,PREG_PATTERN_ORDER);
		
		foreach($title_matches[1] as $title) {
			$title = str_replace("- recall","",$title);
			$return["title"] = trim($title);
		}
		
		//get content from recall
		
		$content_regex = "/<p>(.*?)<\/p>/s";
		
		preg_match_all($content_regex,$html,$content_matches,PREG_PATTERN_ORDER);
		
		$return["content"] = "";
		foreach($content_matches[1] as $content) {
			
			//strip out tags from html
			$content = strip_tags($content);
			$content = html_entity_decode($content);
			
			//add line breaks
			$content = trim($content)."\n";

			//add to result			
			$return["content"] .= $content;
			
		}

		return $return;
		
	}
	
	private function produce_slug($title,$id) {
		$this->load->helper('slug_helper');
		$internal_base_url = produce_slug($title,"-");
  		$internal_url = $internal_base_url;
  		$internal_url_attempts = 0;
  				
  		//do while url already exists
  				
  		while ($this->Recall_model->internal_url_exist($internal_url)) {
  			if ($internal_url_attempts < 10) {
  				$internal_url = $internal_base_url."-".($internal_url_attempts+1);
  				$internal_url_attempts++;
  			} else {
  				$internal_url = $internal_base_url."-".$id;
  				break;
  				
  			}
  		}
  		
  		return $internal_url;
	}
	
	function checker() {
		
		//if this is being run through the main website
		//disallow and throw a 404 error
  		if(substr_count($_SERVER['SCRIPT_FILENAME'],'run_scraper_checker.php') < 1) {
  			$this->load->helper('url_helper');
  			show_404(current_url());
  			exit;
  		}
		
		$this->load->model('Recall_model','',TRUE);
		$this->load->library('email');
		$this->load->helper('date');
		
		if ($this->Recall_model->are_there_recent_updates() < 1) {
			//there have been no updates for 2 days, send a warning email

			//set up the email		
			$this->email->from(EMAILER_ADDRESS,SITE_NAME);
			$this->email->to(SCRAPER_EMAIL_LOG);

			$this->email->subject(SCRAPER_EMAIL_LOG_PREFIX." No Recent Updates");
			$this->email->message("There have been no updates for 2 days. Please check.");
			
			//send the email
			$this->email->send();

		}
	
	}
 
}
?>