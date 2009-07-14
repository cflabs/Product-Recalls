<?php 
 
class EU_Scraper extends Controller {
	private $log = array();
	private $imported_counter = 0;
	private $recalls_counter = 0;
	private $recent_updates = FALSE;
	private $updated_recalls = 0;
	private $updates_counter = 0;
	
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
  		$this->load->helper('security_helper');
    	$this->load->model('Recall_model','',TRUE);
    	$this->load->library('MP_Cache');
    	
   	
    	//scrape the content    
    	$this->begin_scrape();
    	
    	//run import checker
    	$this->checker();
    	
    	//send log email
    	$this->log_mail();
    	
    	//delete recent recalls cache if cache is active
    	if (WWW_CACHE_ACTIVE) {
    		$this->mp_cache->delete('recent-recalls');
    	}
    
		//load the view   
		$this->load->view('import');
	}
	
	function bypass_import($id=0,$dt="") {
		//load helper and model
  		$this->load->helper('scraping_helper');
    	$this->load->model('Recall_model','',TRUE);
    	
    	if ($id > 0) {
    		//echo "DT:<br/>".$dt;
			$this->back_scrape_week("http://ec.europa.eu/consumers/dyna/rapex/create_rapex.cfm?rx_id=".$id,$dt);
    	}
	}
	
	private function log($message,$log_type='debug') {
		
		//print the message to the screen
		print $message."\n<br/>";
		//push it to the log
		array_push($this->log,$message);
		log_message($log_type,SCRAPER_EMAIL_LOG_PREFIX." ".$message);
		
		
	}
	
	private function log_mail($complete = TRUE) {
		
		//load the email helper
		//$this->load->helper('email_helper');
		$this->load->library('email');
		
		//decide on the subject
		$subject_prefix = "";
		if ( ! $this->recent_updates) {
			$subject_prefix = " NO RECENT UPDATES!";
		}

		//set up the email		
		$this->email->from(EMAILER_ADDRESS,SITE_NAME);
		$this->email->to(SCRAPER_EMAIL_LOG);
		$subject = "";
		if ($complete) {
			$subject = SCRAPER_EMAIL_LOG_PREFIX.$subject_prefix." Scraped ".$this->imported_counter."/".$this->recalls_counter;
			if ($this->updates_counter > 0) {
				$subject .= " Updated ".$this->updated_recalls."/".$this->updates_counter;
			}
		} else {
			$subject = SCRAPER_EMAIL_LOG_PREFIX." ERROR";
		}
		$this->email->subject($subject);
		
		//decide if warning needs to be appended to content
		$content_warning = "";
		if ( ! $this->recent_updates) {
			$content_warning = "*********************************************\n";
			$content_warning .= "WARNING: There have been no updates for 7 days. Please check, something may be broken.\n";
			$content_warning .= "*********************************************\n\n";
		}
		
		$this->email->message($content_warning.implode("\n",$this->log));
		
		//send the email
		$this->email->send();
		
	}
  
  	private function begin_scrape() {
  		
  		$this->log("starting EU scraper ".time());
  		
  		try {
  			//get rss feed
  			$rss_content = scrape_content("http://ec.europa.eu/consumers/dyna/rapex/rapex_archives_rss_en.cfm");
		} catch (Exception $e) {
			$this->log("fatal error: ". $e->getMessage(),'error');
			$this->log_mail(FALSE);
			exit;
		}
		
		if ($rss_content != "") {
		
	  		//get all the links from the feed
  			$link_regex = "/<guid>(.*?)<\/guid>/s";
  			preg_match_all($link_regex,$rss_content,$link_matches,PREG_PATTERN_ORDER);

			$this->log("found ".count($link_matches[1])." weeks listed in feed");
  		
	  		//for each item in the list
	  		if (count($link_matches[1])) {
	  			$i = 0; $u = count($link_matches[1]);
	  			//get the top 5 links
	  			while ($i < 10 && $i < $u) {
	  				//scrape the week's recalls
	  				$this->scrape_week($link_matches[1][$i]);	
	  				$i++;				
	  			}
	  		} else {
				$this->log("fatal error: no links to scrape",'error');
				$this->log_mail(FALSE);
	  		} 
	  		
		} else {
	  		$this->log("fatal error: no content - feed empty",'error');
	  	}
	  	
	  	$this->log("imported ".$this->imported_counter." of ".$this->recalls_counter);
	  	$this->log("updated ".$this->updated_recalls." of ".$this->updates_counter);
	  	$this->log("end ".time());
  		
  	}
  	
  	private function scrape_week($url) {
		//get content
		
		$this->log("-scraping ".$url);
		
		try {
  			$page_html = scrape_content($url);
  			
  			if ($page_html != "") {
	  		
	  			//reg exs
	  			$table_regex = "/<table border=\"1\" cellpadding=\"6\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"100%\" id=\"AutoNumber1\" height=\"100%\">(.*?)<\/table>/s";
	  			$row_regex = "/<tr>(.*?)<\/tr>/s";
	  		
	  			//get tables
	  			preg_match_all($table_regex,$page_html,$table_matches,PREG_PATTERN_ORDER);
	  			//get rows from table  		
	  			//print_r($table_matches[1]);
	  			preg_match_all($row_regex,$table_matches[1][0],$row_matches,PREG_PATTERN_ORDER);
	  		
	  			//loop through all the rows (ignoring the first which contains headings)
	  			$first_row = TRUE;
	  			foreach($row_matches[1] as $row) {
	  				if ($first_row) {
	  					$first_row = FALSE;
	  					//$this->process_row($row,$td_regex);
	  					continue;
	  				}
	  				//process the row
	  				$result = $this->process_row($row);
	  			
	  				if ($result['title'] != "") {
		  			
		  				//does the product already exist?
		  				$exists = $this->Recall_model->id_exist($result['id'],2);
		  			
		  				//if it does not exist, import it
			    		if (!$exists) {
			    			$this->log("-- importing product (".$result['id'].")");
			    			
			    			//produce the unique url
			    			$slug_title = $result['title'];
			    			if ($result['brand'] != "") { $slug_title = $result['brand_cleaned']." ".$slug_title; }
			    			//print ($slug_title."<br/>");
							$internal_url = $this->produce_slug($slug_title,$result['id']);
	
							//get category id
							$category_id = $this->get_category_id($result['category']);
	
			    			//store in database
				    		$data = array(
				    			'product_name' => $result['title'],
				    			'description' => $result['content_cleaned'],
				    			'danger' => $result['danger'],
				    			'measures_taken' => $result['measures'],
				    			'country' => $result['country'],
				    			'category' => $result['category'],
				    			'category_id' => $category_id,
				    			'brand' => $result['brand'],
				    			'external_img_url' => $result['image'],
				    			'external_lrgimg_url' => $result['image_large'],
				    			'external_url' => $url,
				    			'internal_url' => $internal_url,
				    			'source_id' => $result['id'],
				    			'source' => 2   			
				    		);
				    		
				    		
				    		$this->db->insert('recalls',$data);
				    		$this->imported_counter++;
			    		} else {
			    			$this->log("-- product already recorded (".$result['id'].")");
			    		}
			    		$this->recalls_counter++;
			    		
	  				}

	  			}
	  			$this->check_for_updates($page_html); 		
  			} else {
  				$this->log("-error: no content - empty page");
  			}
		} catch (Exception $e) {
			$this->log("-error: ". $e->getMessage(),'error');
		}
		sleep(SCRAPER_SLEEP);
  	}
  	
  	private function get_category_id($category) {
  		
  		$this->load->model('Category_model','',TRUE);
		$this->load->helper('slug_helper');
		
		//clean up the category name
		//in some cases product information leaks into the category
		//we need to remove this
		$clean_required = "";
		if (strpos($category, 'Product:') !== false) {
 			//cleaning is required
 			$clean_required = "YES";
 			
 			//run a regex on the category to tidy it
 			$category_regex = "/(.*?) Product.*?/s";
 			preg_match_all($category_regex,$category,$cat_matches,PREG_PATTERN_ORDER);

	  		//if there is a match, store it
	  		if (count($cat_matches[1]) > 0) {
	  			$category = html_entity_decode(trim(strip_tags($cat_matches[1][0])));
	  		}
 			
		}
		
		//produce the slug
		$slug = produce_slug($category,"-");
		
		//merge some categories
		//we have some categories that are virtually identical
		//so merging them will make things easier for people
		switch ($slug) {
			case "personal-protective-equipment" :
				$slug = "protective-equipment";
				break;
			case "lighting-chains" :
				$slug = "lighting-equipment";
				break;
		}
		
		//does this category exist?
		$exists = $this->Category_model->category_exists($slug);
		

		
		//if it does exist, return the id
		if ($exists > 0) {
			echo ('<div style="background:#3981AA;padding:5px;margin:5px;color:#fff;font-weight:bold;">');
			echo ('Exists: '.$exists);
			echo ('</div>');
			return $exists;
		} else {
			//does not exist, add to database
			$id = $this->Category_model->add_category($category,$slug);
			echo ('<div style="background:#398100;padding:5px;margin:5px;color:#fff;font-weight:bold;">');
			echo ('Create: '.$slug.' ('.$id.')');
			echo ('</div>');
			return $id;
		}
  		
  	}
  	
  	private function process_row($row) {
  		//default values
  		$return = array();
  		
  		//td reg ex & process
  		$td_regex = "/<td.*?>(.*?)<\/td>/s";
  		preg_match_all($td_regex,$row,$td_matches,PREG_PATTERN_ORDER);
  		
  		//if there are 6 columns, then process the row
  		if (count($td_matches[1]) == 6) {
	  		
	  		$image = $this->process_image($td_matches[1][2]);
	  		$info = $this->process_product_info($td_matches[1][2]);
	  		
			$return['id'] = trim(html_entity_decode(strip_tags(str_replace("/","-",str_replace("<br>","-",$td_matches[1][0])))));
			$return['country'] = trim(html_entity_decode(strip_tags($td_matches[1][1])));
			$return['content'] = trim(html_entity_decode(strip_tags(str_replace("<br/>","\n",$td_matches[1][2]))));
			$return['danger'] = trim(html_entity_decode(strip_tags(str_replace("<br/><br/>","\n",$td_matches[1][3]))));
			$return['measures'] = trim(html_entity_decode(strip_tags($td_matches[1][4])));
			$return['image'] = $image['image'];
			$return['image_large'] = $image['image_large'];
			$return['category'] = $info['category'];
			$return['title'] = $info['product'];
			$return['brand'] = $info['brand'];
			$return['brand_cleaned'] = preg_replace('/[^a-zA-Z0-9 -,]/','',$info['brand']);
			$return['content_cleaned'] = $info['content_cleaned'];

			
  		} else {
			$return['title'] = "";
  		}
  		
  		return $return;

  	}
  	
  	private function process_image($content) {
  		
  		//default values
  		$return = array();
  		$return['image'] = "";
  		$return['image_large'] = "";
  		
  		//image reg ex
  		$img_regex = "/<img src=\"(.*?).jpg\".*?\/>/s";
  		$img_l_regex = "/<img .*? onClick=\"ZoomPicture\('(.*?).jpg'\)\".*?\/>/s";
  		
  		//run reg ex
  		preg_match_all($img_regex,$content,$img_matches,PREG_PATTERN_ORDER);
  		preg_match_all($img_l_regex,$content,$img_l_matches,PREG_PATTERN_ORDER);
  		
  		//if there is an image, store it
  		if (count($img_matches[1]) > 0) {
  			$return['image'] = "http://ec.europa.eu".$img_matches[1][0].".jpg";
  		}
  		//if there is a large image, store it
  		if (count($img_l_matches[1]) > 0) {
  			$return['image_large'] = $img_l_matches[1][0].".jpg";
  		}
  		
  		//return the object
  		return $return;

  	}
  	
  	private function process_product_info($content) {
  		
  		//default values
  		$return = array();
  		$return['category'] = "Unknown";
  		$return['product'] = "";
  		$return['brand'] = "Unknown";
  		$return['content_cleaned'] = "";
  		
  		//regex for picking out category, product and brand from description
  		$category_regex = "/Category: (.*?)<br\/>/s";
  		$product_regex = "/Product: (.*?)<br\/>/s";
  		$brand_regex = "/Brand: (.*?)Type|Brand: (.*?)<br\/>/s";
  		$content_regex = "/Category: .*?<br\/>.*?Product: .*?<br\/>(.*?)<\/span>/s";
  		
  		//run regexs on the content
  		preg_match_all($category_regex,$content,$category_matches,PREG_PATTERN_ORDER);
  		preg_match_all($product_regex,$content,$product_matches,PREG_PATTERN_ORDER);
  		preg_match_all($brand_regex,$content,$brand_matches,PREG_PATTERN_ORDER);
  		preg_match_all($content_regex,$content,$content_matches,PREG_PATTERN_ORDER);
  	
  		//if there is a category, store it
  		if (count($category_matches[1]) > 0) {
  			$return['category'] = html_entity_decode(trim(strip_tags($category_matches[1][0])));
  		}
  		//if there is a product, store it
  		if (count($product_matches[1]) > 0) {
  			$return['product'] = html_entity_decode(trim(strip_tags($product_matches[1][0])));
  		}  		
  		//if there is a brand, store it
  		if (count($brand_matches[1]) > 0) {
  			$return['brand'] = html_entity_decode(trim(strip_tags($brand_matches[1][0])));
  		}
  		//if there is content, store it
  		if (count($content_matches[1]) > 0) {
  			$return['content_cleaned'] = trim(html_entity_decode(strip_tags(str_replace("<br/>","\n",$content_matches[1][0]))));
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
	
	private function checker() {
		

		$this->load->model('Recall_model','',TRUE);
		$this->load->library('email');
		$this->load->helper('date');
		
		if ($this->Recall_model->are_there_recent_updates() < 1) {
			//there have been no updates for 7 days, note that an email must be sent!
			$this->recent_updates = FALSE;
			$this->log("WARNING: There have been no recent updates. Please check as there might be a problem",'warning');
		} else {
			$this->recent_updates = TRUE;
		}
	
	}
	
	
  	private function back_scrape_week($url,$dt) {
		//get content
		
		$this->log("-scraping ".$url);
		
		try {
  			$page_html = scrape_content($url);
  			
  			if ($page_html != "") {
	  		
	  			//reg exs
	  			$table_regex = "/<table border=\"1\" cellpadding=\"6\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"100%\" id=\"AutoNumber1\" height=\"100%\">(.*?)<\/table>/s";
	  			$row_regex = "/<tr>(.*?)<\/tr>/s";
	  		
	  			//get tables
	  			preg_match_all($table_regex,$page_html,$table_matches,PREG_PATTERN_ORDER);
	  			//get rows from table  		
	  			preg_match_all($row_regex,$table_matches[1][0],$row_matches,PREG_PATTERN_ORDER);
	  		
	  			//loop through all the rows (ignoring the first which contains headings)
	  			$first_row = TRUE;
	  			foreach($row_matches[1] as $row) {
	  				if ($first_row) {
	  					$first_row = FALSE;
	  					//$this->process_row($row,$td_regex);
	  					continue;
	  				}
	  				//process the row
	  				$result = $this->process_row($row);
	  			
	  				if ($result['title'] != "") {
		  			
		  				//does the product already exist?
		  				$exists = $this->Recall_model->id_exist($result['id'],2);
		  			
		  				//if it does not exist, import it
			    		if (!$exists) {
			    			$this->log("-- importing product (".$result['id'].")");
			    			
			    			//produce the unique url
			    			$slug_title = $result['title'];
			    			if ($result['brand'] != "") { $slug_title = $result['brand_cleaned']." ".$slug_title; }
			    			//print ($slug_title."<br/>");
							$internal_url = $this->produce_slug($slug_title,$result['id']);
							//get category id
							$category_id = $this->get_category_id($result['category_id']);
			    			//store in database
				    		$data = array(
				    			'product_name' => $result['title'],
				    			'description' => $result['content_cleaned'],
				    			'danger' => $result['danger'],
				    			'measures_taken' => $result['measures'],
				    			'country' => $result['country'],
				    			'category' => $result['category'],
				    			'category_id' => $result['category_id'],
				    			'brand' => $result['brand'],
				    			'external_img_url' => $result['image'],
				    			'external_lrgimg_url' => $result['image_large'],
				    			'external_url' => $url,
				    			'internal_url' => $internal_url,
				    			'source_id' => $result['id'],
				    			'source' => 2,
				    			'date_scraped' => $dt
				    		);
				    		$this->db->insert('recalls',$data);
				    		$this->imported_counter++;
			    		} else {
			    			$this->log("-- product already recorded (".$result['id'].")");
			    		}
			    		$this->recalls_counter++;
	  				}
	  			
	  			} 		
  			} else {
  				$this->log("-error: no content - empty page");
  			}
		} catch (Exception $e) {
			$this->log("-error: ". $e->getMessage(),'error');
		}
		sleep(SCRAPER_SLEEP);
  	}
  	
  	public function categorise() {
  		
  		$this->output->enable_profiler();
  		
    	$this->load->model('Recall_model','',TRUE);
    	$this->load->model('Category_model','',TRUE);
    	$this->load->helper('Security_helper');
    	
    	//get all the recalls in the database
    	$recalls = $this->Recall_model->get_all_entries();
    	
    	//loop through them all
    	foreach ($recalls as $recall) {
    		
    		//if a category id has not yet been assigned
    		if ($recall['category_id'] < 1) {
    			
    			//get the name of the category
    			$category_string = $recall['category'];
    			
    			//if the name of the category is empty
    			if ($recall['category'] == "") {
    				//assign it to the "other" category
    				$category_string = "Other";
    			} 
    			
    			//get the category id
    			$category_id = $this->get_category_id($category_string);
    			
    			//update the record
    			$data = array('category_id'=>$category_id);
    			$this->db->where('id',$recall['id']);
    			$this->db->update('recalls',$data);
    			
    			
    		}
    		
    	} 		
  		
  	}
  	
  	private function check_for_updates($page_html) {
		//get content
		$this->output->enable_profiler();
		
		$this->log("-- checking for updates");
		
		try {
  			
  			if ($page_html != "") {

	  			//reg exs
	  			$note_regex = "/<p class=\"texte\"><font color=\"CC0000\"><font color=\"#ff0000\"><b>[A-Za-z\:]*<\/b><\/font>.*?<font color=\"#000000\">(.*?)<\/font><\/font><\/p>/s";
	  			$content_regex = "/Notification (\d{4}\/\d{2}).*?removed(.*?)\./";
	  		
	  			//get notes
	  			preg_match_all($note_regex,$page_html,$note_matches,PREG_PATTERN_ORDER);
	  			
	  			if (count($note_matches[1]) < 1) {
	  				$this->log("--- no updates found");
	  			} else {
	  				$this->log("--- ".count($note_matches[1])." updates found");
	  			}

	  			//get content
	  			foreach ($note_matches[1] as $match) {
	  				//print_r($match);
	  				preg_match_all($content_regex,$match,$content_matches,PREG_PATTERN_ORDER);
	  				if (! empty($content_matches[1])) {
	  					
	  					$product_id = trim(html_entity_decode(strip_tags(str_replace("/","-",$content_matches[1][0]))));
	  					//$product_id = "0811-09";
	  					$removal_reason = trim(html_entity_decode(strip_tags($content_matches[2][0])));
	  					
		  				//does the product already exist?
		  				$exists = $this->Recall_model->id_like($product_id,2);
		  			
		  				//if it exits, we need to update the record
			    		if ($exists) {
			    			//has it already been updated?
			    			$already_updated = $this->Recall_model->already_updated($product_id,2,'removed');
			    			if (! $already_updated) {
				    			//store in database
					    		$data = array(
					    			'status' => 'removed',
					    			'status_text' => $removal_reason,
					    			'status_updated' => date('Y-m-d 00:00:00',time())
					    		);
					    		$this->db->where('source_id like','%'.$product_id);
					    		$this->db->update('recalls',$data);
					    		
					    		$this->updated_recalls ++;
					    		$this->log("--- removing (".$product_id.")");
			    			} else {
			    				$this->log("--- already removed (".$product_id.")");
			    			}
			    		} else {
			    			$this->log("--- cannot find (".$product_id.")");
			    		}
			    		$this->updates_counter ++;
	  					//echo "\n<div style=\"background:#ccc;margin:10px;\">\n";
	  					//echo "Product ID: ".$product_id;
	  					//echo "<br/>Reason: ".$removal_reason;
	  					//echo "\n</div>\n";
	  				}

	  			}
	  			//print_r($note_matches[1]);
	  		
	
  			} else {
  				$this->log("-error: no content - empty page");
  			}
		} catch (Exception $e) {
			$this->log("-error: ". $e->getMessage(),'error');
		}
		
		sleep(SCRAPER_SLEEP);
  	}
 

}


?>