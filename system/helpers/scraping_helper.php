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
 * CodeIgniter Scraping Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Consumer Focus Labs
 * @link		http://consumerfocuslabs.org
 */

// ------------------------------------------------------------------------

/**
 * Validate email address
 *
 * @access	public
 * @return	bool
 */	
if ( ! function_exists('scrape_content')) {
	function scrape_content($url) {
		$return = false;
		$html = file_get_contents($url);
		
		if ($html != '' && isset($html)) {
			$return = $html;
		} else {
			throw new Exception('cannot load content from '.$url);
		}
		
		return $return;
	}
}


/* End of file scraping_helper.php */
/* Location: ./system/helpers/scraping_helper.php */