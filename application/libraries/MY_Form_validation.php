<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class MY_Form_validation extends CI_Form_validation
{

	var $number_of_steps;
	var $steps_base_url;
	
	
	function MY_Form_validation()
	{
		parent::CI_Form_validation();
		
		//echo "<pre>".print_r($this, true)."</pre>"; 
	}
	
	function config($config)
	{
		if(!is_array($config))
			return FALSE;
			
		foreach($config as $key => $value)
			$this->$key = $value;
		
		return TRUE;
	}
	
}


/* End of file MY_Form_validation.php */
/* Location: ./system/application/libraries/MY_Form_validation.php */