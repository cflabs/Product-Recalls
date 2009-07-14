<?php
/**
 * Signup Class
 * 
 * @package Product Recalls
 * @subpackage Controllers
 * @category Signup
 * @author Consumer Focus Labs
 * @link http://www.consumerfocuslabs.org
 */
class Signup extends Controller {

	/**
	 * Constructor
	 * 
	 * @access public
	 */
	function Signup() {
		//load parent
  		parent::Controller();
  		
		//load in some helpers
		$this->load->model('User_model', '', TRUE);
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('email');
		//TO DO: autoload please!
	  	$this->load->helper('url_helper');
	  	$this->load->helper('typography_helper');
	  	$this->load->helper('date_helper');
	  	$this->load->helper('text_helper');
	    $this->load->model('Recall_model','',TRUE);
	    $this->load->model('Category_model','',TRUE);
	}

	/**
	 * Attempts to process the registration
	 * 
	 * @access public
	 */
  	function index() {
			
		//validate form
		$this->form_validation->set_rules('txtEmail','email address','trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('ddlFrequency','frequency','trim|required|alpha|exact_length[1]|xss_clean');
		$this->form_validation->set_rules('ddlCat','category','trim|required|numeric|is_natural|xss_clean');
		
		if ($this->form_validation->run() == FALSE) {
			//form validation has failed. show the signup form again

			//set page data
    		$page_data['page_title'] = "Sign Up | ".SITE_NAME;
    		$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
    		$page_data['feeds']= array();
    		$page_data['menu'] = 'default';
    		
    		//load a list of categories for use in the signup form (name/id)
    		$data['categories'] = $this->Category_model->category_list_dropdown();
    		
    		//load views
	    	$this->load->view('header',$page_data);
    		$this->load->view('signup/signup.php',$data);
    		$this->load->view('footer');   
		
		} 
		else {
			//form is valid, add to database
			
			//which interval to use?
			$email_frequency = 0;
			switch ($this->input->post('ddlFrequency',TRUE)) {
				//daily is no longer an option
				//case "d" :
				//	$email_frequency=1;
				//	break;
				case "w" :
					$email_frequency=2;
					break;
				case "m" :
					$email_frequency=3;
					break;
			}
			$email_address = $this->input->post('txtEmail',TRUE);
			$category = $this->input->post('ddlCat',TRUE);
			//create confirmation key
			$confirmation_key = $this->User_model->create_user($email_address,$email_frequency,$category);
			
			//generate confirmation email
			$this->email->from(EMAILER_ADDRESS,SITE_NAME);
			$this->email->to($email_address);
			$this->email->subject('['.SITE_NAME.'] Please confirm your product recall alert');
			
			$message = "Please click on the link below to confirm you want to receive emails for product recall notices.\n\n";
			$message .= site_url(array('signup','confirm',$confirmation_key))."\n\n";
			$message .="If your email program does not let you click on this link, just copy and paste it into your web browser and hit return.";
			//send message
			$this->email->message($message);
			$this->email->send();
			
			//load activation required view
    		$page_data['page_title'] = "Sign Up - Activation Required | ".SITE_NAME;
    		$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
    		$page_data['feeds']= array();
    		$page_data['menu'] = 'default';
	    	$this->load->view('header',$page_data);
	    	$data['email_address'] = $email_address;
    		$this->load->view('signup/activation_required.php',$data);
    		$this->load->view('footer');   

		}
		
  	}

	/**
	 * Attempts to confirm the registration attempt
	 * 
	 * @access public
	 * @param string $key the unique hashed key for the user
	 */
	function confirm($key) {

  		//validate confirmation key
  		$valid = $this->User_model->validate_user($key);
  	
  		//if valid
  		if ($valid) {
  			//activate user
  			$this->User_model->activate_user($key);
  			//show message
   			$page_data['page_title'] = "Your alert has been activated | ".SITE_NAME;
   			$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
   			$page_data['feeds']= array();
   			$page_data['menu'] = 'default';
    		$this->load->view('header',$page_data);
   			$this->load->view('signup/signup_complete.php');
   			$this->load->view('footer');  
  		} else {
  			//show invalid key error
   			$page_data['page_title'] = "Sign Up - Key not recognised | ".SITE_NAME;
   			$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
   			$page_data['feeds']= array();
   			$page_data['menu'] = 'default';
    		$this->load->view('header',$page_data);
   			$this->load->view('signup/problem.php');
   			$this->load->view('footer');   
  		}
  	
	}
  
	/**
	 * Attempts to unsubscribe the user from the site
	 * 
	 * @access public
	 * @param string $key the unique hashed key for the user
	 */
  	function unsubscribe($key) {

  		//validate unsubscribe key
  		$valid = $this->User_model->validate_user($key);
  	
  		if ($valid) {
  			//if valid, unsubscribe user
  			$this->User_model->unsubscribe_user($key);
  			//show message
   			$page_data['page_title'] = "Your alert has been removed | ".SITE_NAME;
   			$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
   			$page_data['feeds']= array();
   			$page_data['menu'] = 'default';
    		$this->load->view('header',$page_data);
   			$this->load->view('signup/unsubscribe_complete.php');
   			$this->load->view('footer');  
  		} else {
  			//show invalid key message
   			$page_data['page_title'] = "Unsubscribe - Key not recognised | ".SITE_NAME;
   			$page_data['recent_recalls'] = $this->Recall_model->num_recent_recalls();
   			$page_data['feeds']= array();
   			$page_data['menu'] = 'default';
    		$this->load->view('header',$page_data);
   			$this->load->view('signup/problem.php');
   			$this->load->view('footer');   
  		}
  	
  	}
  
  /*
  A FUNCTION FOR CHECKING TO SEE IF AN EMAIL ADDRESS IS ALREADY REGISTERED IN THE DATABASE
  AT PRESENT, IT IS NOT REQUIRED
  function email_check($str) {
  	if ($this->User_model->user_active($str) == true) {
  		$this->form_validation->set_message('email_check','The %s you entered is already registered');
  		return FALSE;
  	} 
  	else {
  		return TRUE;  	
  	}
  }*/
  

			
  
}
/* End of file signup.php */ 
/* Location: ./system/application/controllers/signup.php */ 