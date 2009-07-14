<?php
class Emailer extends Controller {
	private $log = array();
	private $email_counter = 0;
  function index()
  {
  		//$this->output->enable_profiler();
  		if(substr_count($_SERVER['SCRIPT_FILENAME'],'run_emailer.php') < 1) {
  			$this->load->helper('url_helper');
  			show_404(current_url());
  			exit;
  		}
  	
		$this->load->model('User_model','',TRUE);
		$this->load->model('Recall_model','',TRUE);
		$this->load->model('Category_model','',TRUE);
		$this->load->library('email');
		$this->load->helper('url_helper');
		$this->load->helper('date_helper');
		$this->load->helper('typography_helper');
		
		$this->log("starting emailer ".time());
		
		//get a list of people to send emails to
		$data['users'] = $this->User_model->get_email_list();
		
		$this->log(count($data['users'])." emails to send");
		
		//send_email(SCRAPER_EMAIL_LOG,SCRAPER_EMAIL_LOG_PREFIX."test","test");
		
		foreach($data['users'] as $user) {
			
			//get a list of recalls to send to this person
			$recalls = $this->Recall_model->get_entries_since($user['date_last_sent'],FALSE,$user['category']);
			$category = $this->Category_model->get_category_from_id($user['category'],TRUE);
			$this->log("-- email ".$this->email_counter.": u ".$user['id']."; c ".$user['category']."; f ".$user['frequency']."; r ".count($recalls));
		

			//if there are recalls to send
			if (count($recalls) > 0) {
				
				try {
					//produce subject
					$subject = str_replace("%n",count($recalls)-1,str_replace("%t",$recalls[0]['product_name'],EMAILER_SUBJECT));
					
					$category_name = "";
					if ($category->id > 0) {
						$category_name = " of ".$category->name." ";
					} 
					
					//produce content top
					$content = "Product recall notices ".$category_name." issued since ".date("d-M-Y",mysql_to_unix($user['date_last_sent']))."\n\n";
			
					
					//produce content for each product
					foreach($recalls as $recall) {
						$content .= "------------------------------------------------------------\n\n";
						$content .= $recall['product_name']."\n\n";
						$content .= "DETAILS:\n".$recall['description']."\n\n";
						$content .= "DANGER:\n".$recall['danger']."\n\n";
						$content .= "ACTION TAKEN:\n".$recall['measures_taken']."\n\n";
						$content .= "More Info: ".site_url(array("recalls","view",$recall['internal_url']))."\n\n";
						
					}
					
					//add footer
					$content .= "------------------------------------------------------------\n\n";
					$content .= "This email has been sent to you because you subscribed to the service from ".SITE_NAME.".\nIf you do not wish to receive any future emails, please unsubscribe by visiting this address: ".site_url(array("signup","unsubscribe",$user['user_key']))."";
					
					//send and wait to send next
					try {
						$this->email->from(EMAILER_ADDRESS,SITE_NAME);
						$this->email->to($user['email']);
						$this->email->subject($subject);
						$this->email->message($content);
						$this->email->send();
						$this->log("-- email ".$this->email_counter.": sent");
						$this->email_counter++;
						
					} catch (Exception $e) {
						$this->log("error: ". $e->getMessage(),'error');
					}
					
					$this->User_model->update_user_sent_date($user['id']);
				} catch (Exception $e) {
					$this->log("error: ". $e->getMessage(),'error');
				}


				sleep(EMAILER_SLEEP);

			}
			

		}
		$this->log($this->email_counter." emails sent");
		$this->log("end");
		$this->log_mail();
		
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

		//set up the email		
		$this->email->from(EMAILER_ADDRESS,SITE_NAME);
		$this->email->to(EMAILER_EMAIL_LOG);
		if ($complete) {
			$this->email->subject(EMAILER_EMAIL_LOG_PREFIX." Emailed ".$this->email_counter);
		} else {
			$this->email->subject(EMAILER_EMAIL_LOG_PREFIX." ERROR");
		}
		$this->email->message(implode("\n",$this->log));
		
		//send the email
		$this->email->send();
		
	}
}
?>