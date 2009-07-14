<?php

/**
 * Categories Model
 * 
 * @package Product Recalls
 * @subpackage Models
 * @category Categories
 * @author Dafydd Vaughan, Consumer Focus Labs
 * @link http://www.consumerfocuslabs.org
 */
class Category_model extends Model {

	/**
	 * Constructor
	 * 
	 * @access public
	 */
    function Category_model()
    {
        parent::Model();

    }
    
    /**
     * Check if a category exists, return its 
     * ID if it does
     * 
     * @access public
     * @param string $category the slug of the category
     * @return integer id of category (0 if does not exist)
     */
    function category_exists($category) {
    	
    	//select stuff
    	$this->db->select('id');
    	$this->db->where('slug',$category);
    	$query = $this->db->get('categories');

		//if there are records
		if ($query->num_rows() > 0)
		{
			//get the id and return it
			$row = $query->row();
			return $row->id;
		} else {
			//otherwise return 0
			return 0;
		}
    	
    }
    
    /**
     * Gets the details of a category
     * 
     * @access public
     * @param string $category the slug of the category to get
     * @return object the record (or empty object if not exists)
     */
    function get_category($category,$include_all=FALSE) {
    	//$this->output->enable_profiler();

    	if ($include_all && $category==="all") {
    		$cat = new catObject();
    		$cat->id = 0;
    		$cat->name = "All Categories";
    		$cat->slug = "all";
    		
    		return $cat;
    		
    	} else {
	    	$this->db->where('slug',$category);
	    	$query = $this->db->get('categories');
	    	
	    	return $query->row();
    	}
    	
    }
    
    /**
     * Gets the details of a category from its id
     * 
     * @access public
     * @param integer $id the id of the category to get
     * @return object the record (or empty object if not exists)
     */
    function get_category_from_id($id,$include_all=FALSE) {
    	//$this->output->enable_profiler();

    	if ($include_all && $id==0) {
    		$cat = new catObject();
    		$cat->id = 0;
    		$cat->name = "All Categories";
    		$cat->slug = "all";
    		
    		return $cat;
    		
    	} else {
	    	$this->db->where('id',$id);
	    	$query = $this->db->get('categories');
	    	
	    	return $query->row();
    	}
    	
    }
    
    
    /**
     * Gets a list of categories from the database
     * including number of recalls and date last updated
     * 
     * @access public
     * @param boolean $include_empty include empty categories in results
     * @return array the results of the query
     */
    function category_list($include_empty=false) {
    	
    	//select
    	$this->db->select('categories.*, (SELECT count(*) FROM recalls WHERE category_id=categories.id) as recalls, (SELECT date_scraped FROM recalls WHERE category_id=categories.id ORDER BY date_scraped DESC LIMIT 1) as last_updated');
    	
    	//include empty results
    	if (! $include_empty) {
    		$this->db->where('(SELECT count(*) FROM recalls WHERE category_id=categories.id) >',0);
    	}
    	
    	//order by
    	$this->db->order_by('name','asc');
    	
    	//query and return
    	$query = $this->db->get('categories');
    	return $query->result_array();
    	
    }
    
    /**
     * Gets a list of categories from the database
     * including only slug and category name (for drop down)
     * 
     * @access public
     * @param boolean $include_empty include empty categories in results
     * @return array the results of the query
     */
    function category_list_dropdown_slug($include_empty=false) {
    	
    	$this->db->select('slug,name');
    	$this->db->order_by('name','asc');
    	$query = $this->db->get('categories');
    	
    	if ($query->num_rows() > 0) {
    	
    		$data['all'] = 'All Categories';
			foreach ($query->result_array() as $row) {
				$data[$row['slug']] = $row['name'];
			}
    	}
    	return $data;	
    }
    /**
     * Gets a list of categories from the database
     * including only id and category name (for drop down)
     * 
     * @access public
     * @param boolean $include_empty include empty categories in results
     * @return array the results of the query
     */
    function category_list_dropdown($include_empty=false) {
    	
    	$this->db->select('id,name');
    	$this->db->order_by('name','asc');
    	$query = $this->db->get('categories');
    	
    	if ($query->num_rows() > 0) {
    	
    		$data[0] = 'All Categories';
			foreach ($query->result_array() as $row) {
				$data[$row['id']] = $row['name'];
			}
    	}
    	return $data;
    	
    }
    
    /**
     * Gets the number of recalls for a category
     * 
     * @access public
     * @param string $category the slug of the category to check
     * @return integer number of records
     */
    function category_records_count($category)
    {
    	//select
    	$this->db->select('recalls.id');
    	$this->db->from('recalls');
    	$this->db->join('categories','categories.id=recalls.category_id');
    	$this->db->where('categories.slug',$category);
    	
    	//count and return
    	$count = $this->db->count_all_results();
    	return $count;
    }
    
    /**
     * Adds a category to the database
     * 
     * @access public
     * @param string $name the name of the category
     * @param string $slug the category's slug
     * @return integer the id of the category
     */
    function add_category($name,$slug) {
    	
    	//set up data
    	$data = array(
    		'name' =>xss_clean($name),
    		'slug' => xss_clean($slug)
    	);

		//insert and return
    	$this->db->insert('categories',$data);
    	return $this->db->insert_id();
    }

}
class catObject {
	var $id;
	var $name;
	var $slug;
}

/* End of file category_model.php */ 
/* Location: ./system/application/models/category_model.php */ 