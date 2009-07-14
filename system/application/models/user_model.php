<?php
/**
 * User Model
 * 
 * @package Product Recalls
 * @subpackage Models
 * @category User
 * @author Consumer Focus Labs
 * @link http://www.consumerfocuslabs.org
 */
class User_model extends Model {

	/**
	 * Constructor
	 * 
	 * @access public
	 */
    function User_model()
    {
    	//load parent
        parent::Model();

    }

	/**
	 * Gets a list of users which need to be sent an updated
	 * list of recalls
	 * 
	 * @access public
	 * @return array of users
	 */
    function get_email_list()
    {
        $query = $this->db->query('(SELECT users.*, confirmations.user_key FROM users INNER JOIN confirmations on users.id=confirmations.parent_id WHERE confirmations.parent_table="users" AND users.live=1 AND users.frequency=2 AND date_last_sent<DATE_SUB(now(),INTERVAL 1 WEEK)) UNION (SELECT users.*, confirmations.user_key FROM users INNER JOIN confirmations on users.id=confirmations.parent_id WHERE confirmations.parent_table="users" AND users.live=1 AND users.frequency=3 AND date_last_sent<DATE_SUB(now(),INTERVAL 1 MONTH))');
        return $query->result_array();
    }
    
    /**
     * Returns whether a user is active or inactive
     * 
     * @access public
     * @param string $email the email address of the user to check
     * @return boolean true if active, false if inactive
     */
    function user_active($email) {
    	
    	$this->db->select('users.id');
    	$this->db->from('users');
    	$this->db->join('confirmations','confirmations.parent_id=users.id');
    	$this->db->where('confirmations.parent_table','users');
    	$this->db->where('users.live',TRUE);
    	$this->db->where('users.email',$email);
    	$count = $this->db->count_all_results();
    	
    	return $count==0 ? false : true;
    	
    	
    }
    
    /**
     * Creates a new user in the database
     * 
     * @access public
     * @param string $email the email of the user to create
     * @param integer $frequency the frequency with which to send emails (1=daily,2=weekly,3=monthly)
     * @param integer $category the id of the category of recalls to send (0=all)
     * @return string a unique text key to idenfity the user
     */
    function create_user($email,$frequency,$category) {
    	
    	//create data array & add to database
		$data = array(
	    	'email' => $email,
	    	'frequency' => $frequency,
	    	'date_last_sent' => date('Y-m-01 00:00:00',time()),
	    	'live' => FALSE,
	    	'category' => $category 			
	    );
	    $this->db->insert('users',$data);
	    
	    //get id of user
	    $user_id = $this->db->insert_id();
	    //create unique hash for this user
	    $user_key = hash('md5',$email.date("YmdHis"));
	    
	    //create activation record in database
	    $data = array(
	    	'parent_table' => 'users',
	    	'parent_id' => $user_id,
	    	'user_key' => $user_key
	    );
	    $this->db->insert('confirmations',$data);
	    //return key
	    return $user_key;
   	
    }
    
    /**
     * Confirms whether a unique key corresponds to a valid user
     * 
     * @access public
     * @param string $key the unique key to validate
     * @return boolean whether key is valid or not
     */
	function validate_user($key) {
		
    	$this->db->select('users.id');
    	$this->db->from('confirmations');
    	$this->db->where('user_key',$key);
    	$count = $this->db->count_all_results();
    	
    	return $count==0 ? false : true;
		
	}
	
	/**
	 * Attempts to activate a user to begin sending them messages
	 * 
	 * @access public
	 * @param string $key the unique key of the user to activate
	 */
	function activate_user($key) {
		//set live to true
	    $data = array(
	    	'live' => TRUE
	    );
	    $where = "users.id=(SELECT confirmations.parent_id FROM confirmations WHERE confirmations.parent_table='users' AND confirmations.user_key='".$key."')";
	    $this->db->where($where);
	    $this->db->update('users',$data);

	}
	
	/**
	 * Attempts to unsubscribe a user to stop sending them messages
	 * 
	 * @access public
	 * @param string $key the unique key of the user to unsubscribe
	 */
	function unsubscribe_user($key) {
		
	    $data = array(
	    	'live' => FALSE
	    );
	    $where = "users.id=(SELECT confirmations.parent_id FROM confirmations WHERE confirmations.parent_table='users' AND confirmations.user_key='".$key."')";
	    $this->db->where($where);
	    $this->db->update('users',$data);
	
	}
	
	/**
	 * Updates the last sent date of a user
	 * 
	 * @access public
	 * @param integer $user the id number of the user to update
	 */
	function update_user_sent_date($user) {
		
		$data = array('date_last_sent'=>date('Y-m-d H:i:s'));
		$this->db->where('id',$user);
		$this->db->update('users',$data);
		
	}
    
}

/* End of file recall_model.php */ 
/* Location: ./system/application/models/recall_model.php */ 