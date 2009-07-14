<?php

class User_model extends Model {


    function User_model()
    {
        parent::Model();

    }

    function get_email_list()
    {
        $query = $this->db->query('(SELECT users.*, confirmations.user_key FROM users INNER JOIN confirmations on users.id=confirmations.parent_id WHERE confirmations.parent_table="users" AND users.live=1 AND users.frequency=2 AND date_last_sent<DATE_SUB(now(),INTERVAL 1 WEEK)) UNION (SELECT users.*, confirmations.user_key FROM users INNER JOIN confirmations on users.id=confirmations.parent_id WHERE confirmations.parent_table="users" AND users.live=1 AND users.frequency=3 AND date_last_sent<DATE_SUB(now(),INTERVAL 1 MONTH))');
        return $query->result_array();
    }
    
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
    
    function create_user($email,$frequency,$category) {
    	
		$data = array(
	    	'email' => $email,
	    	'frequency' => $frequency,
	    	'date_last_sent' => date('Y-m-01 00:00:00',time()),
	    	'live' => FALSE,
	    	'category' => $category 			
	    );
	    $this->db->insert('users',$data);
	    
	    $user_id = $this->db->insert_id();
	    $user_key = hash('md5',$email.date("YmdHis"));
	    
	    $data = array(
	    	'parent_table' => 'users',
	    	'parent_id' => $user_id,
	    	'user_key' => $user_key
	    );
	    $this->db->insert('confirmations',$data);
	    
	    return $user_key;

    	
    }
    
	function validate_user($key) {
		
    	$this->db->select('users.id');
    	$this->db->from('confirmations');
    	$this->db->where('user_key',$key);
    	$count = $this->db->count_all_results();
    	
    	return $count==0 ? false : true;
		
	}
	
	function activate_user($key) {
		
	    $data = array(
	    	'live' => TRUE
	    );
	    $where = "users.id=(SELECT confirmations.parent_id FROM confirmations WHERE confirmations.parent_table='users' AND confirmations.user_key='".$key."')";
	    $this->db->where($where);
	    $this->db->update('users',$data);
		//UPDATE users
		//SET users.live=1
		//WHERE users.id = (SELECT confirmations.parent_id FROM confirmations WHERE confirmations.parent_table='users' AND confirmations.user_key='46c9b7a3c5f3e998b280c10d0a69ebe1')
	
	}
	
	function unsubscribe_user($key) {
		
	    $data = array(
	    	'live' => FALSE
	    );
	    $where = "users.id=(SELECT confirmations.parent_id FROM confirmations WHERE confirmations.parent_table='users' AND confirmations.user_key='".$key."')";
	    $this->db->where($where);
	    $this->db->update('users',$data);
	
	}
	
	function update_user_sent_date($user) {
		
		$data = array('date_last_sent'=>date('Y-m-d H:i:s'));
		$this->db->where('id',$user);
		$this->db->update('users',$data);
		
	}
    
}

?>