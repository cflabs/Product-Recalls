<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Consumer Focus Labs
 * @copyright	Copyright (c) 2009, Consumer Focus Labs.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Slug Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Consumer Focus Labs
 * @link		http://consumerfocuslabs.org
 */

// ------------------------------------------------------------------------

/**
 * Produce a simple slug
 *
 * @access	public
 * @return	bool
 */	
if ( ! function_exists('produce_slug')) {
	function produce_slug($text,$split_character) {
		$slug = str_replace("-"," ",str_replace("/"," ",$text));
		$slug =  trim(preg_replace("/[^a-zA-Z0-9 ]/", "", $slug));
  		$slug = strtolower(str_replace(" ", $split_character, str_replace("  "," ",$slug)));
  		return $slug;
	}
}


/* End of file scraping_helper.php */
/* Location: ./system/helpers/scraping_helper.php */