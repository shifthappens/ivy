<?php

//Import base class

class Importer
{


	public $source = '';
	public $fields = array();
	public $rows = array();
	public $upload_path = './uploads/';


	public function __construct()
	{
	
	}
	
	
	public function load_file($file)
	{
		if(!file_exists($file))
			$this->throw_error(1, "Import file does not exist or is not readable.");
		
		$contents = file_get_contents($file);
		
		if(empty($contents))
			$this->throw_error(3, "Import file is empty.");
		
		$this->load_string($contents);
				
		return TRUE;
				
	}


	public function load_string($string)
	{
		if(empty($string))
			$this->throw_error(3, "Import string is empty.");

		$this->source = trim($string);			
	}
	

	protected function throw_error($level, $message)
	{

		switch($level)
		{
			case 1:
			$terminate = TRUE;
			$level = 'error';
			break;
			
			case 2:
			$terminate = FALSE;
			$level = 'debug';
			break;
			
			case 3:
			default:
			$terminate = FALSE;
			$level = 'info';
			break;
		}
		
		$this->error_message = $message;
		log_message($level, $this->error_message);
		
		if($terminate === TRUE)
			die($this->error_message);
	}
	
	protected function strip($source, $input)
	{
		$input = str_ireplace($input, '', $source);
		
		return $input;
	}


}


/* End of file Importer.php */
/* Location: ./system/application/libraries/Importer.php */