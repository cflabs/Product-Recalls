<?php

/**
 * Recall Model
 * 
 * @package Product Recalls
 * @subpackage Models
 * @category Recalls
 * @author Dafydd Vaughan, Consumer Focus Labs
 * @link http://www.consumerfocuslabs.org
 */
class Recall_model extends Model {

	/**
	 * Constructor
	 * 
	 * @access public
	 */
    function Recall_model()
    {
        parent::Model();

    }

	/**
	 * Get recent entries from the database
	 * 
	 * @access public
	 * @param integer $numentries the number of entries to get
	 * @param integer $startpos the start position in the database
	 * @param boolean $include_removed include items marked as removed
	 * @return array the results from the database 
	 */
    function get_recent_entries($numentries,$startpos = 0,$include_removed=TRUE)
    {
        $this->db->limit($numentries,$startpos);
        $this->db->order_by('date_scraped','desc');
        if (! $include_removed) { $this->db->where('status !=','removed'); }
        $query = $this->db->get('recalls');
        return $query->result_array();
    }
    
	/**
	 * Get recent entries from  entries from the database and filter by
	 * a particular category
	 * 
	 * @access public
	 * @param string $category the slug for the category to use
	 * @param integer $numentries the number of entries to get
	 * @param integer $startpos the start position in the database
	 * @param boolean $include_removed include items marked as removed
	 * @return array the results from the database 
	 */
    function get_category_entries($category,$numentries,$startpos=0,$include_removed=TRUE)
    {
    	//select items
    	$this->db->select('recalls.*, categories.name, categories.slug');
    	$this->db->limit($numentries,$startpos);
    	$this->db->join('categories','categories.id=recalls.category_id');
    	$this->db->where('categories.slug',$category);
		$this->db->order_by('date_scraped','desc');
    	//if not to display "removed" items, add where clause
    	if (! $include_removed) { $this->db->where('status !=','removed'); }
    	//get items
    	$query = $this->db->get('recalls');
    	return $query->result_array();
    }
    
    /**
     * Get all recalls from database
     * 
     * @access public
     * @return array the records in the database
     */
    function get_all_entries()
    {
        $this->db->order_by('date_scraped','desc');
        $query = $this->db->get('recalls');
        return $query->result_array();
    }
    
    /**
     * Get a particular recall from the database
     * 
     * @access public
     * @param string $url the unique slug of the recall
     * @return array the database record
     */
    function get_recall($url)
    {
    	$this->db->select('recalls.*, sources.source_name, categories.name as category_name, categories.slug as category_slug');
    	$this->db->join('sources','sources.id=recalls.source');
    	$this->db->join('categories','categories.id=recalls.category_id');
    	$this->db->where('internal_url',$url);
    	$query = $this->db->get('recalls');
    	return $query->result_array();
    }
    
    /**
     * Get recalls from the database, filtered by a search string
     * 
     * @access public
     * @param string $text the text to filter results by
     * @param boolean $include_removed whether to include items marked as removed
     * @return array the database records
     */
    function search_recalls($text,$include_removed=TRUE) {
    	$this->db->like('product_name',$text);
    	$this->db->or_like('description',$text);
    	$this->db->or_like('danger',$text);
        $this->db->order_by('date_scraped','desc');
        if (! $include_removed) { $this->db->where('status !=','removed'); }
        $query = $this->db->get('recalls');
        return $query->result_array();
    }
    
    /**
     * Get recalls from the database, filtered by a search string and category
     * 
     * @access public
     * @param integer $category the id of the category to filter by
     * @param string $text the text to filter results by
     * @param boolean $include_removed whether to include items marked as removed
     * @return array the database records
     */
    function search_recalls_category($category,$text,$numentries,$startpos=0,$include_removed=TRUE) {
    	
    	$this->db->limit($numentries,$startpos);
    	if ($category > 0) { $this->db->having('category_id',$category); }
    	$this->db->like('product_name',$text);
    	$this->db->or_like('description',$text);
    	$this->db->or_like('danger',$text);
        $this->db->order_by('date_scraped','desc');
        if (! $include_removed) { $this->db->where('status !=','removed'); }
        $query = $this->db->get('recalls');
        return $query->result_array();
    }
    
    /**
     * Get all entries since a particular date
     * 
     * @access public
     * @param number $timestamp the timestamp to get results since
     * @param boolean $include_removed whether to include items marked as removed
     * @return array the database records
     */
    function get_entries_since($timestamp,$include_removed=TRUE,$category=0) {
    	$this->db->where('date_scraped >', $timestamp);
    	if ($category > 0) { $this->db->where('category_id',$category); }
    	$this->db->order_by('date_scraped','asc');
    	if (! $include_removed) { $this->db->where('status !=','removed'); }
    	$query = $this->db->get('recalls');
    	return $query->result_array();
    }
    
    /**
     * Check to see if a recall has already been added into database
     * 
     * @access public
     * @param string $id the id from the import source
     * @param integer $source the source which needs to be checked
     * @return boolean true if found, false if not
     */
    function id_exist($id,$source) {
    	$this->db->select('id');
    	$this->db->from('recalls');
    	$this->db->where('source_id',$id);
    	$this->db->where('source',$source);
    	$count = $this->db->count_all_results();
    	
    	return $count==0 ? false : true;
    	
    }

    /**
     * Check to see if a recall has already been added into database
     * (uses like instead of equals)
     * 
     * @access public
     * @param string $id the id from the import source
     * @param integer $source the source which needs to be checked
     * @return boolean true if found, false if not
     */
    function id_like($id,$source) {
    	$this->db->select('id');
    	$this->db->from('recalls');
    	$this->db->like('source_id',$id,'before');
    	$this->db->where('source',$source);
    	$count = $this->db->count_all_results();
    	
    	return $count==0 ? false : true;
    	
    }

    /**
     * Check to see if a slug has already been allocated
     * to a recall
     * 
     * @access public
     * @param string $url the slug to check for
     * @return boolean false if not allocated, true if allocated
     */
    function internal_url_exist($url) {
    	$this->db->select('internal_url');
    	$this->db->from('recalls');
    	$this->db->where('internal_url',$url);
    	$count = $this->db->count_all_results();
    	
    	return $count==0 ? false : true;
    }
    
    /**
     * Gets the number of recalls added to the database
     * 
     * @access public
     * @return integer the number of records added to the database
     */
    function records() {
    	return $this->db->count_all_results('recalls');
    }
    
    /**
     * Gets the number of recalls for a search
     * 
     * @access public
     * @param string $category the slug of the category to check
     * @param string $term the term to filter results by 
     * @return integer number of records
     */
    function search_records_count($category,$text,$include_removed=TRUE)
    {
    	if ($category > 0) { $this->db->having('category_id',$category); }
    	$this->db->like('product_name',$text);
    	$this->db->or_like('description',$text);
    	$this->db->or_like('danger',$text);
        $this->db->order_by('date_scraped','desc');
        if (! $include_removed) { $this->db->where('status !=','removed'); }
        $query = $this->db->get('recalls');
        return count($query->result_array());
    	

    }
    
    
    
    /**
     * Check to see if a recall has already been updated during an
     * import
     * 
     * @access public
     * @param string $id the id from the import source
     * @param integer $source the source which needs to be checked
     * @param string $status the status to check
     * @return boolean true if updated, false if not
     */
    function already_updated($id,$source,$status) {
    	$this->db->select('id');
    	$this->db->from('recalls');
    	$this->db->like('source_id',$id,'before');
    	$this->db->where('source',$source);
    	$this->db->where('status',$status);
    	$count = $this->db->count_all_results();
    	
    	return $count==0 ? false : true;
    }
    
    /**
     * Check to see if there have been updates in the last 7 days
     * 
     * @access public
     * @return integer the number of recalls added in the last 7 days
     */
    function are_there_recent_updates() {

    	$this->db->select('date_scraped');
    	$this->db->from('recalls');
		$this->db->where('date_scraped >','DATE_SUB(now(),INTERVAL 7 DAY)',FALSE);
    	$this->db->order_by('date_scraped','desc');
    	$count = $this->db->count_all_results();
    	return $count;
    }
    
    /**
     * Gets the number of recalls added in the last month
     * 
     * @access public
     * @return integer the number of recalls added in the last month
     */
    function num_recent_recalls() {
    	//load the cache library
    	$this->load->library('MP_Cache');
    	//if a cache exists, get it
    	$count = $this->mp_cache->get('recent-recalls');
    	if ($count === false) {
    		//no cache exists, load it from database
	    	$this->db->select('date_scraped');
	    	$this->db->from('recalls');
			$this->db->where('date_scraped >','DATE_SUB(now(),INTERVAL 1 MONTH)',FALSE);
	    	$this->db->order_by('date_scraped','desc');
	    	$count = $this->db->count_all_results();
	    	//write to cache
	    	$this->mp_cache->write($count,'recent-recalls',3600);
    	}
    	
    	return $count;
    }
}

/* End of file recall_model.php */ 
/* Location: ./system/application/models/recall_model.php */ 